<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Client;
use App\Models\Project;
use App\Models\Quote;
use App\Models\DebtsReceivable;
use App\Models\Income;
use App\Models\Payment;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        $client_id = $request->get('client_id');
        
        $invoices = Invoice::where('user_id', $user->id)
            ->with(['client'])
            ->when($search, function ($query) use ($search) {
                return $query->where('invoice_number', 'like', "%{$search}%");
            })
            ->when($status, function ($query) use ($status) {
                return $query->where('status', $status);
            })
            ->when($client_id, function ($query) use ($client_id) {
                return $query->where('client_id', $client_id);
            })
            ->latest()
            ->paginate(10);
            
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
            
        return view('portal.invoices.index', compact('invoices', 'clients', 'search', 'status', 'client_id', 'currencySymbol'));
    }

    public function create(Request $request)
    {
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->whereIn('status', ['planned', 'in_progress'])
            ->orderBy('title')
            ->get();
            
        $quotes = Quote::where('user_id', $user->id)
            ->whereIn('status', ['accepted', 'converted'])
            ->orderBy('quote_number')
            ->get();
            
        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
            
        return view('portal.invoices.create', compact('clients', 'projects', 'quotes', 'currencySymbol'));
    }

    public function store(Request $request)
    {
        Log::info('Invoice store method called', ['request_data' => $request->all()]);
        
        // First, check if client belongs to user
        $client = Client::where('id', $request->client_id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$client) {
            Log::warning('Invalid client selected', ['client_id' => $request->client_id, 'user_id' => Auth::id()]);
            return redirect()->back()
                ->with('error', 'Invalid client selected. Please select a valid client.')
                ->withInput();
        }
        
        // Custom validation for invoice_number uniqueness for this user
        $invoiceNumberExists = Invoice::where('invoice_number', $request->invoice_number)
            ->where('user_id', Auth::id())
            ->exists();
        
        if ($invoiceNumberExists) {
            Log::warning('Duplicate invoice number', ['invoice_number' => $request->invoice_number]);
            return redirect()->back()
                ->with('error', 'This invoice number already exists. Please use a different number.')
                ->withInput();
        }
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'quote_id' => 'nullable|exists:quotes,id',
            'invoice_number' => 'required|string',
            'issue_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:issue_date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,partial,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        Log::info('Validation passed', ['validated_data' => $validated]);
        
        $validated['user_id'] = Auth::id();
        
        try {
            DB::transaction(function () use ($validated) {
                Log::info('Creating invoice in transaction');

                $invoice = Invoice::create($validated);
                Log::info('Invoice created', ['invoice_id' => $invoice->id]);

                foreach ($validated['items'] as $item) {
                    InvoiceItem::create([
                        'invoice_id'  => $invoice->id,
                        'description' => $item['description'],
                        'quantity'    => $item['quantity'],
                        'unit_price'  => $item['unit_price'],
                        'total'       => $item['total'],
                    ]);
                }
                Log::info('Invoice items created');

                // Auto-create the receivable for sent / partial / overdue invoices
                $this->syncDebtsReceivable($invoice, $validated['status']);

                // If this invoice was converted from a quote, mark the quote as converted
                if (!empty($validated['quote_id'])) {
                    Quote::where('id', $validated['quote_id'])
                        ->where('user_id', Auth::id())
                        ->update(['status' => 'converted']);
                }

                Log::info('Transaction completed successfully');
            });
            
            return redirect()->route('invoices.index')
                ->with('success', 'Invoice created successfully.');
        } catch (\Exception $e) {
            Log::error('Invoice creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to create invoice: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function show(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        
        $invoice->load(['client', 'project', 'items', 'payments']);
        
        // Calculate actual paid amount from the invoice
        $actualPaidAmount = $invoice->paid_amount;
        
        // Get user's currency
        $currencySymbol = CurrencyHelper::getSymbol(Auth::user()->currency_code ?? 'USD');
        
        return view('portal.invoices.show', compact('invoice', 'actualPaidAmount', 'currencySymbol'));
    }

    public function edit(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->orderBy('title')
            ->get();
            
        $invoice->load('items');
        
        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
            
        return view('portal.invoices.edit', compact('invoice', 'clients', 'projects', 'currencySymbol'));
    }

    public function update(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $oldStatus = $invoice->status;

        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'due_date' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,partial,paid,overdue,cancelled',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $paidAmount = $invoice->paid_amount;
        $items = $validated['items'];
        unset($validated['items']);
        $validated['paid_amount'] = $paidAmount;

        DB::transaction(function () use ($invoice, $validated, $items, $oldStatus, $paidAmount) {
            $invoice->update($validated);

            // Replace all invoice items
            $invoice->items()->delete();
            foreach ($items as $item) {
                InvoiceItem::create([
                    'invoice_id'  => $invoice->id,
                    'description' => $item['description'],
                    'quantity'    => $item['quantity'],
                    'unit_price'  => $item['unit_price'],
                    'total'       => $item['total'],
                ]);
            }

            // Sync DebtsReceivable based on the new status
            $this->syncDebtsReceivable($invoice, $validated['status']);

            // Only create/update income record if there are actual payments
            if ($paidAmount > 0) {
                if (in_array($validated['status'], ['partial', 'paid']) && !in_array($oldStatus, ['partial', 'paid'])) {
                    $this->createIncomeFromInvoice($invoice);
                }
                if (in_array($validated['status'], ['partial', 'paid'])) {
                    $this->updateIncomeFromInvoice($invoice);
                }
            }

            // Delete income record if status changed away from paid/partial
            if (!in_array($validated['status'], ['partial', 'paid']) && in_array($oldStatus, ['partial', 'paid'])) {
                Income::where('invoice_id', $invoice->id)->delete();
            }
        });

        return redirect()->route('invoices.index')
            ->with('success', 'Invoice updated successfully.');
    }
    
    private function createIncomeFromInvoice(Invoice $invoice)
    {
        // Check if income record already exists for this invoice
        $existingIncome = Income::where('invoice_id', $invoice->id)->first();
        
        if ($existingIncome) {
            return;
        }
        
        // Get actual paid amount from invoice
        $paidAmount = $invoice->paid_amount;
        
        // Only create income record if there are actual payments
        if ($paidAmount <= 0) {
            return;
        }
        
        // Get or create a default income category
        $category = \App\Models\Category::where('user_id', $invoice->user_id)
            ->where('type', 'income')
            ->first();
        
        Income::create([
            'user_id' => $invoice->user_id,
            'client_id' => $invoice->client_id,
            'project_id' => $invoice->project_id,
            'invoice_id' => $invoice->id,
            'category_id' => $category ? $category->id : null,
            'amount' => $paidAmount,
            'income_date' => now(),
            'payment_method' => 'other',
            'reference_number' => 'INV-' . $invoice->invoice_number,
            'notes' => 'Income from invoice #' . $invoice->invoice_number,
        ]);
    }
    
    private function updateIncomeFromInvoice(Invoice $invoice)
    {
        $income = Income::where('invoice_id', $invoice->id)->first();
        
        if (!$income) {
            $this->createIncomeFromInvoice($invoice);
            return;
        }
        
        // Get actual paid amount from invoice
        $paidAmount = $invoice->paid_amount;
        
        // If no payments, don't update the income amount (keep existing)
        // or delete the income record if it exists
        if ($paidAmount <= 0) {
            $income->delete();
            return;
        }
        
        $income->update([
            'amount' => $paidAmount,
            'client_id' => $invoice->client_id,
            'project_id' => $invoice->project_id,
        ]);
    }

    public function destroy(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        
        $invoice->delete();
        
        return redirect()->route('invoices.index')
            ->with('success', 'Invoice deleted successfully.');
    }
    
    public function downloadPdf(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $invoice->load(['client', 'project', 'items', 'payments']);

        $user = Auth::user();

        $businessInfo = [
            'business_name'    => $user->business_name ?? $user->name,
            'tax_number'       => $user->tax_number ?? '',
            'business_address' => $user->business_address ?? '',
            'email'            => $user->email,
            'phone'            => $user->business_phone ?? $user->phone ?? '',
        ];

        // Encode logo as base64 for dompdf (file:// URLs unreliable on Windows)
        $logoBase64 = null;
        if ($user->logo_path && Storage::disk('public')->exists($user->logo_path)) {
            $logoData   = Storage::disk('public')->get($user->logo_path);
            $logoMime   = Storage::disk('public')->mimeType($user->logo_path);
            $logoBase64 = 'data:' . $logoMime . ';base64,' . base64_encode($logoData);
        }

        $docSettings    = $user->getDocSettings();
        $currencyCode   = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        $pdf = PDF::loadView('portal.invoices.pdf', compact(
            'invoice', 'businessInfo', 'currencyCode', 'currencySymbol',
            'docSettings', 'logoBase64'
        ));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    /**
     * Create, update, or delete the linked DebtsReceivable whenever an invoice
     * status changes.  Called from both store() and update().
     */
    private function syncDebtsReceivable(Invoice $invoice, string $newStatus): void
    {
        $receivable = DebtsReceivable::where('invoice_id', $invoice->id)->first();

        if (in_array($newStatus, ['sent', 'partial', 'overdue'])) {
            $debtStatus = match($newStatus) {
                'partial' => 'partial',
                'overdue' => 'overdue',
                default   => 'pending',
            };

            if (!$receivable) {
                // Create the receivable for the first time
                DebtsReceivable::create([
                    'user_id'         => $invoice->user_id,
                    'client_id'       => $invoice->client_id,
                    'invoice_id'      => $invoice->id,
                    'original_amount' => $invoice->total_amount,
                    'paid_amount'     => $invoice->paid_amount,
                    'due_date'        => $invoice->due_date,
                    'status'          => $debtStatus,
                ]);
            } else {
                // Keep amounts and due date in sync when the invoice is edited
                $receivable->update([
                    'original_amount' => $invoice->total_amount,
                    'due_date'        => $invoice->due_date,
                    'client_id'       => $invoice->client_id,
                ]);
            }

            return;
        }

        if ($newStatus === 'paid' && $receivable) {
            $receivable->update([
                'paid_amount' => $invoice->total_amount,
                'status'      => 'paid',
            ]);
            return;
        }

        // draft / cancelled → remove the receivable so it doesn't show as outstanding
        if (in_array($newStatus, ['draft', 'cancelled']) && $receivable) {
            $receivable->delete();
        }
    }

    public function generateShareLink(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $token = Str::random(48);
        $invoice->update(['share_token' => $token]);

        return response()->json([
            'url'   => url('/view/invoice/' . $token),
            'token' => $token,
        ]);
    }

    public function revokeShareLink(Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);

        $invoice->update(['share_token' => null]);

        return response()->json(['success' => true]);
    }

    private function authorizeInvoice($invoice)
    {
        if ($invoice->user_id !== Auth::id()) {
            abort(403);
        }
    }
    
    public function recordPayment(Request $request, Invoice $invoice)
    {
        $this->authorizeInvoice($invoice);
        
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01|max:' . $invoice->outstandingAmount(),
            'payment_date' => 'required|date',
            'payment_method' => 'required|string',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);
        
        try {
            DB::transaction(function () use ($invoice, $validated) {
                // Create payment record
                $payment = Payment::create([
                    'user_id' => Auth::id(),
                    'invoice_id' => $invoice->id,
                    'amount' => $validated['amount'],
                    'payment_date' => $validated['payment_date'],
                    'payment_method' => $validated['payment_method'],
                    'reference_number' => $validated['reference_number'] ?? null,
                    'notes' => $validated['notes'] ?? null,
                ]);
                
                // Calculate total paid amount (existing + new payment)
                $totalPaid = $invoice->paid_amount + $validated['amount'];
                
                // Update invoice paid_amount
                $invoice->update(['paid_amount' => $totalPaid]);
                
                // Update invoice status based on payment
                if ($totalPaid >= $invoice->total_amount) {
                    $invoice->update(['status' => 'paid']);
                } elseif ($totalPaid > 0) {
                    $invoice->update(['status' => 'partial']);
                }
                
                // Create or update income record
                $this->createOrUpdateIncomeFromPayment($invoice, $payment);
                
                // Update debts receivable if exists
                $debtReceivable = DebtsReceivable::where('invoice_id', $invoice->id)->first();
                if ($debtReceivable) {
                    $debtReceivable->update([
                        'paid_amount' => $totalPaid,
                        'status' => $invoice->status === 'paid' ? 'paid' : 'partial',
                    ]);
                }
            });
            
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Payment recorded successfully.');
        } catch (\Exception $e) {
            Log::error('Payment recording failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to record payment: ' . $e->getMessage())
                ->withInput();
        }
    }
    
    private function createOrUpdateIncomeFromPayment(Invoice $invoice, Payment $payment)
    {
        // Get or create "Invoice Payments" income category
        $category = \App\Models\Category::firstOrCreate(
            [
                'user_id' => $invoice->user_id,
                'type' => 'income',
                'name' => 'Invoice Payments'
            ],
            [
                'user_id' => $invoice->user_id,
                'type' => 'income',
                'name' => 'Invoice Payments',
                'color' => 'success'
            ]
        );
        
        // Check if income record already exists for this invoice
        $existingIncome = Income::where('invoice_id', $invoice->id)->first();
        
        if ($existingIncome) {
            // Update existing income with new total paid amount from invoice
            $totalPaid = $invoice->paid_amount;
            $existingIncome->update([
                'amount' => $totalPaid,
                'income_date' => $payment->payment_date,
                'client_id' => $invoice->client_id,
                'project_id' => $invoice->project_id,
            ]);
        } else {
            // Create new income record
            Income::create([
                'user_id' => $invoice->user_id,
                'client_id' => $invoice->client_id,
                'project_id' => $invoice->project_id,
                'invoice_id' => $invoice->id,
                'category_id' => $category ? $category->id : null,
                'amount' => $payment->amount,
                'income_date' => $payment->payment_date,
                'payment_method' => $payment->payment_method,
                'reference_number' => $payment->reference_number,
                'notes' => 'Payment for invoice #' . $invoice->invoice_number,
            ]);
        }
    }
    
    public function destroyPayment(Invoice $invoice, Payment $payment)
    {
        $this->authorizeInvoice($invoice);
        
        if ($payment->invoice_id !== $invoice->id) {
            abort(404);
        }
        
        try {
            DB::transaction(function () use ($invoice, $payment) {
                // Delete the payment
                $payment->delete();
                
                // Recalculate total paid from invoice
                $totalPaid = $invoice->paid_amount;
                
                // Update invoice
                $invoice->update(['paid_amount' => $totalPaid]);
                
                // Update status based on remaining payments
                if ($totalPaid <= 0) {
                    // If no payments left, revert to sent status (or draft if it was draft)
                    $invoice->update(['status' => 'sent']);
                } elseif ($totalPaid < $invoice->total_amount) {
                    $invoice->update(['status' => 'partial']);
                } else {
                    $invoice->update(['status' => 'paid']);
                }
                
                // Update or delete income record
                $income = Income::where('invoice_id', $invoice->id)->first();
                if ($income) {
                    if ($totalPaid <= 0) {
                        $income->delete();
                    } else {
                        $income->update(['amount' => $totalPaid]);
                    }
                }
                
                // Update debts receivable
                $debtReceivable = DebtsReceivable::where('invoice_id', $invoice->id)->first();
                if ($debtReceivable) {
                    if ($totalPaid <= 0) {
                        $debtReceivable->update([
                            'paid_amount' => 0,
                            'status' => 'pending',
                        ]);
                    } else {
                        $debtReceivable->update([
                            'paid_amount' => $totalPaid,
                            'status' => $invoice->status === 'paid' ? 'paid' : 'partial',
                        ]);
                    }
                }
            });
            
            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Payment deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Payment deletion failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->with('error', 'Failed to delete payment: ' . $e->getMessage());
        }
    }
}
