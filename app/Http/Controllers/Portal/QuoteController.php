<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Client;
use App\Models\Project;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;

class QuoteController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = $request->get('search');
        $status = $request->get('status');
        $client_id = $request->get('client_id');
        
        $quotes = Quote::where('user_id', $user->id)
            ->with(['client'])
            ->when($search, function ($query) use ($search) {
                return $query->where('quote_number', 'like', "%{$search}%");
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
            
        return view('portal.quotes.index', compact('quotes', 'clients', 'search', 'status', 'client_id', 'currencySymbol'));
    }

    public function create()
    {
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->orderBy('title')
            ->get();
            
        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
            
        return view('portal.quotes.create', compact('clients', 'projects', 'currencySymbol'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'quote_number' => 'required|string|unique:quotes,quote_number',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected,expired,converted',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        $validated['user_id'] = Auth::id();
        
        DB::transaction(function () use ($validated) {
            $quote = Quote::create($validated);
            
            foreach ($validated['items'] as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }
        });
        
        return redirect()->route('quotes.index')
            ->with('success', 'Quote created successfully.');
    }

    public function show(Quote $quote)
    {
        $this->authorizeQuote($quote);
        
        $quote->load(['client', 'project', 'items', 'invoices']);
        
        $currencySymbol = CurrencyHelper::getSymbol(Auth::user()->currency_code ?? 'USD');
        
        return view('portal.quotes.show', compact('quote', 'currencySymbol'));
    }

    public function edit(Quote $quote)
    {
        $this->authorizeQuote($quote);
        
        $user = Auth::user();
        
        $clients = Client::where('user_id', $user->id)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();
            
        $projects = Project::where('user_id', $user->id)
            ->orderBy('title')
            ->get();
            
        $quote->load('items');
        
        $currencySymbol = CurrencyHelper::getSymbol($user->currency_code ?? 'USD');
            
        return view('portal.quotes.edit', compact('quote', 'clients', 'projects', 'currencySymbol'));
    }

    public function update(Request $request, Quote $quote)
    {
        $this->authorizeQuote($quote);
        
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'project_id' => 'nullable|exists:projects,id',
            'issue_date' => 'required|date',
            'valid_until' => 'required|date',
            'subtotal' => 'required|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'total_amount' => 'required|numeric|min:0',
            'status' => 'required|in:draft,sent,accepted,rejected,expired,converted',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
            'items.*.total' => 'required|numeric|min:0',
        ]);

        DB::transaction(function () use ($quote, $validated) {
            $quote->update($validated);
            
            // Delete existing items and create new ones
            $quote->items()->delete();
            
            foreach ($validated['items'] as $item) {
                QuoteItem::create([
                    'quote_id' => $quote->id,
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total' => $item['total'],
                ]);
            }
        });
        
        return redirect()->route('quotes.index')
            ->with('success', 'Quote updated successfully.');
    }

    public function destroy(Quote $quote)
    {
        $this->authorizeQuote($quote);
        
        $quote->delete();
        
        return redirect()->route('quotes.index')
            ->with('success', 'Quote deleted successfully.');
    }
    
    public function downloadPdf(Quote $quote)
    {
        $this->authorizeQuote($quote);
        
        $quote->load(['client', 'project', 'items']);
        
        // Get user business information for the PDF
        $user = Auth::user();
        $businessInfo = [
            'business_name' => $user->business_name ?? $user->name,
            'tax_number' => $user->tax_number ?? '',
            'business_address' => $user->business_address ?? '',
            'email' => $user->email,
            'phone' => $user->phone ?? '',
        ];
        
        // Get currency symbol
        $currencyCode = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);
        
        $pdf = PDF::loadView('portal.quotes.pdf', compact('quote', 'businessInfo', 'currencyCode', 'currencySymbol'));
        
        return $pdf->download('quote-' . $quote->quote_number . '.pdf');
    }

    private function authorizeQuote($quote)
    {
        if ($quote->user_id !== Auth::id()) {
            abort(403);
        }
    }
}
