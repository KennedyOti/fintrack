<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Helpers\CurrencyHelper;
use App\Models\Client;
use App\Models\Project;
use App\Models\ProjectMilestone;
use App\Models\Category;
use App\Models\Income;
use App\Models\Expense;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Quote;
use App\Models\QuoteItem;
use App\Models\Payment;
use App\Models\SavingsAccount;
use App\Models\SavingsTransaction;
use App\Models\DebtsReceivable;
use App\Models\DebtsPayable;
use App\Models\RecurringTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use ZipArchive;

class ExportController extends Controller
{
    private string $exportDate;
    private string $currency;
    private string $currencyName;
    private $user;

    // ── Pages ─────────────────────────────────────────────────────────────────

    public function index()
    {
        $user = Auth::user();

        $counts = [
            'clients'               => Client::where('user_id', $user->id)->count(),
            'projects'              => Project::where('user_id', $user->id)->count(),
            'categories'            => Category::where('user_id', $user->id)->count(),
            'income'                => Income::where('user_id', $user->id)->count(),
            'expenses'              => Expense::where('user_id', $user->id)->count(),
            'invoices'              => Invoice::where('user_id', $user->id)->count(),
            'quotes'                => Quote::where('user_id', $user->id)->count(),
            'payments'              => Payment::whereHas('invoice', fn($q) => $q->where('user_id', $user->id))->count(),
            'savings_accounts'      => SavingsAccount::where('user_id', $user->id)->count(),
            'savings_transactions'  => SavingsTransaction::where('user_id', $user->id)->count(),
            'debts_receivable'      => DebtsReceivable::where('user_id', $user->id)->count(),
            'debts_payable'         => DebtsPayable::where('user_id', $user->id)->count(),
            'recurring'             => RecurringTransaction::where('user_id', $user->id)->count(),
        ];

        $totalRecords = array_sum($counts);

        return view('portal.export.index', compact('counts', 'totalRecords'));
    }

    // ── Download ──────────────────────────────────────────────────────────────

    public function download(Request $request)
    {
        $this->user = Auth::user();

        // Rate limit: 1 export per 5 minutes per user
        $key = 'data-export:' . $this->user->id;
        if (RateLimiter::tooManyAttempts($key, 1)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->with('error',
                "Export rate limit reached. Please wait {$seconds} more seconds before downloading again."
            );
        }
        RateLimiter::hit($key, 300);

        $this->currency     = $this->user->currency_code ?? 'USD';
        $this->currencyName = CurrencyHelper::getName($this->currency) ?? $this->currency;
        $this->exportDate   = now()
            ->setTimezone($this->user->timezone ?? 'UTC')
            ->format('Y-m-d H:i:s T');

        // Create temp ZIP
        $tmpFile = tempnam(sys_get_temp_dir(), 'ft_exp_');
        $zipPath = $tmpFile . '.zip';

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($tmpFile);
            return back()->with('error', 'Could not create export archive. Please try again.');
        }

        // Add all files
        $zip->addFromString('README.txt',                    $this->buildReadme());
        $zip->addFromString('01_profile.csv',                $this->buildProfileCsv());
        $zip->addFromString('02_clients.csv',                $this->buildClientsCsv());
        $zip->addFromString('03_projects.csv',               $this->buildProjectsCsv());
        $zip->addFromString('04_project_milestones.csv',     $this->buildMilestonesCsv());
        $zip->addFromString('05_categories.csv',             $this->buildCategoriesCsv());
        $zip->addFromString('06_income.csv',                 $this->buildIncomeCsv());
        $zip->addFromString('07_expenses.csv',               $this->buildExpensesCsv());
        $zip->addFromString('08_invoices.csv',               $this->buildInvoicesCsv());
        $zip->addFromString('09_invoice_items.csv',          $this->buildInvoiceItemsCsv());
        $zip->addFromString('10_quotes.csv',                 $this->buildQuotesCsv());
        $zip->addFromString('11_quote_items.csv',            $this->buildQuoteItemsCsv());
        $zip->addFromString('12_payments.csv',               $this->buildPaymentsCsv());
        $zip->addFromString('13_savings_accounts.csv',       $this->buildSavingsAccountsCsv());
        $zip->addFromString('14_savings_transactions.csv',   $this->buildSavingsTransactionsCsv());
        $zip->addFromString('15_debts_receivable.csv',       $this->buildDebtsReceivableCsv());
        $zip->addFromString('16_debts_payable.csv',          $this->buildDebtsPayableCsv());
        $zip->addFromString('17_recurring_transactions.csv', $this->buildRecurringCsv());

        $zip->close();
        @unlink($tmpFile);

        $fileName = 'fintrack-export-' . now()->format('Ymd-His') . '.zip';

        return response()->download($zipPath, $fileName, [
            'Content-Type'        => 'application/zip',
            'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
        ])->deleteFileAfterSend(true);
    }

    // ── CSV builder helper ────────────────────────────────────────────────────

    /**
     * Builds a branded CSV string with a metadata header block.
     */
    private function makeCsv(string $moduleName, array $headers, array $rows): string
    {
        $handle = fopen('php://temp', 'r+');

        // ── Branding header block ──
        fputcsv($handle, ['FinTrack Data Export']);
        fputcsv($handle, ['Module:', $moduleName]);
        fputcsv($handle, ['Account:', $this->user->name . ' <' . $this->user->email . '>']);

        if ($this->user->business_name) {
            fputcsv($handle, ['Business:', $this->user->business_name]);
        }

        fputcsv($handle, ['Currency:', $this->currency . ' — ' . $this->currencyName]);
        fputcsv($handle, ['Exported:', $this->exportDate]);
        fputcsv($handle, ['Note:', 'Part of your FinTrack GDPR data export. All monetary values are in ' . $this->currency . '.']);
        fputcsv($handle, []); // blank separator

        // ── Column headers ──
        fputcsv($handle, $headers);

        // ── Data rows ──
        foreach ($rows as $row) {
            fputcsv($handle, $row);
        }

        rewind($handle);
        $content = stream_get_contents($handle);
        fclose($handle);

        return $content;
    }

    // ── README ────────────────────────────────────────────────────────────────

    private function buildReadme(): string
    {
        $u        = $this->user;
        $business = $u->business_name ? " / {$u->business_name}" : '';
        $date     = $this->exportDate;
        $currency = $this->currency;
        $name     = $this->currencyName;

        return <<<TXT
╔══════════════════════════════════════════════════════════════════╗
║              FinTrack — Full Account Data Export                 ║
╚══════════════════════════════════════════════════════════════════╝

  Account : {$u->name}{$business}
  Email   : {$u->email}
  Currency: {$currency} ({$name})
  Exported: {$date}

──────────────────────────────────────────────────────────────────
 FILES INCLUDED IN THIS EXPORT
──────────────────────────────────────────────────────────────────

  01_profile.csv                Your account and business profile
  02_clients.csv                All client records
  03_projects.csv               All projects
  04_project_milestones.csv     Project milestones
  05_categories.csv             Income & expense categories
  06_income.csv                 All income transactions
  07_expenses.csv               All expense records
  08_invoices.csv               All invoices
  09_invoice_items.csv          Invoice line items
  10_quotes.csv                 All quotes / estimates
  11_quote_items.csv            Quote line items
  12_payments.csv               Recorded invoice payments
  13_savings_accounts.csv       Savings accounts
  14_savings_transactions.csv   Savings deposits & withdrawals
  15_debts_receivable.csv       Money owed to you (receivables)
  16_debts_payable.csv          Money you owe (payables)
  17_recurring_transactions.csv Recurring income & expense rules

──────────────────────────────────────────────────────────────────
 FORMAT NOTES
──────────────────────────────────────────────────────────────────

  • Each CSV file begins with a branded metadata block (rows
    starting with "FinTrack", "Module:", "Account:", etc.)
    followed by a blank separator row, then column headers,
    then the data.

  • All monetary amounts are in {$currency} ({$name}).
  • All dates are formatted YYYY-MM-DD.
  • Timestamps use your configured account timezone.
  • Soft-deleted records are NOT included in this export.

──────────────────────────────────────────────────────────────────
 YOUR GDPR RIGHTS
──────────────────────────────────────────────────────────────────

  This export is provided under your right to data portability
  (Article 20, GDPR) and similar data-protection regulations.

  Right to access        — You have received a copy of all
                           personal data FinTrack holds about you.

  Right to portability   — All data is in machine-readable CSV
                           format, importable into any spreadsheet
                           or accounting tool.

  Right to erasure       — You may request permanent deletion of
                           your account and all associated data
                           via Settings › Account in the portal.

  Questions?  Contact us through the in-app support channel.

──────────────────────────────────────────────────────────────────
  Generated by FinTrack · Financial Management for Freelancers
╚══════════════════════════════════════════════════════════════════╝

TXT;
    }

    // ── Individual CSV builders ───────────────────────────────────────────────

    private function buildProfileCsv(): string
    {
        $u = $this->user;

        $headers = ['Field', 'Value'];
        $rows = [
            ['Full Name',          $u->name],
            ['Email',              $u->email],
            ['Phone',              $u->phone ?? ''],
            ['Business Name',      $u->business_name ?? ''],
            ['Tax / VAT Number',   $u->tax_number ?? ''],
            ['Business Address',   $u->business_address ?? ''],
            ['Preferred Currency', $u->currency_code ?? 'USD'],
            ['Timezone',           $u->timezone ?? 'UTC'],
            ['Two-Factor Auth',    $u->two_factor_enabled ? 'Enabled' : 'Disabled'],
            ['Account Status',     $u->status ?? 'active'],
            ['Member Since',       $u->created_at?->format('Y-m-d') ?? ''],
        ];

        return $this->makeCsv('Profile', $headers, $rows);
    }

    private function buildClientsCsv(): string
    {
        $clients = Client::where('user_id', $this->user->id)
            ->orderBy('name')
            ->get();

        $headers = [
            'ID', 'Name', 'Company', 'Email', 'Phone',
            'Address', 'Tax Number', 'Status', 'Notes', 'Created At',
        ];

        $rows = $clients->map(fn($c) => [
            $c->id,
            $c->name,
            $c->company_name ?? '',
            $c->email ?? '',
            $c->phone ?? '',
            $c->address ?? '',
            $c->tax_number ?? '',
            $c->status,
            $c->notes ?? '',
            $c->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Clients', $headers, $rows);
    }

    private function buildProjectsCsv(): string
    {
        $projects = Project::with('client')
            ->where('user_id', $this->user->id)
            ->orderBy('title')
            ->get();

        $headers = [
            'ID', 'Title', 'Client', 'Description', 'Budget',
            'Start Date', 'Deadline', 'Status', 'Progress %', 'Created At',
        ];

        $rows = $projects->map(fn($p) => [
            $p->id,
            $p->title,
            $p->client?->name ?? '',
            $p->description ?? '',
            $p->budget ?? '',
            $p->start_date ?? '',
            $p->deadline ?? '',
            $p->status,
            $p->progress_percent ?? 0,
            $p->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Projects', $headers, $rows);
    }

    private function buildMilestonesCsv(): string
    {
        $milestones = ProjectMilestone::with('project')
            ->whereHas('project', fn($q) => $q->where('user_id', $this->user->id))
            ->orderBy('due_date')
            ->get();

        $headers = [
            'ID', 'Project', 'Title', 'Description',
            'Due Date', 'Amount', 'Status', 'Created At',
        ];

        $rows = $milestones->map(fn($m) => [
            $m->id,
            $m->project?->title ?? '',
            $m->title,
            $m->description ?? '',
            $m->due_date ?? '',
            $m->amount,
            $m->status,
            $m->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Project Milestones', $headers, $rows);
    }

    private function buildCategoriesCsv(): string
    {
        $categories = Category::where('user_id', $this->user->id)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $headers = [
            'ID', 'Name', 'Type', 'Color', 'Monthly Budget', 'Created At',
        ];

        $rows = $categories->map(fn($c) => [
            $c->id,
            $c->name,
            $c->type,
            $c->color ?? '',
            $c->monthly_budget ?? '',
            $c->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Categories', $headers, $rows);
    }

    private function buildIncomeCsv(): string
    {
        $incomes = Income::with(['client', 'project', 'category', 'invoice'])
            ->where('user_id', $this->user->id)
            ->orderBy('income_date')
            ->get();

        $headers = [
            'ID', 'Date', 'Amount', 'Category', 'Client', 'Project',
            'Invoice #', 'Payment Method', 'Reference', 'Notes', 'Created At',
        ];

        $rows = $incomes->map(fn($i) => [
            $i->id,
            $i->income_date,
            $i->amount,
            $i->category?->name ?? '',
            $i->client?->name ?? '',
            $i->project?->title ?? '',
            $i->invoice?->invoice_number ?? '',
            $i->payment_method ?? '',
            $i->reference_number ?? '',
            $i->notes ?? '',
            $i->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Income', $headers, $rows);
    }

    private function buildExpensesCsv(): string
    {
        $expenses = Expense::with(['project', 'category'])
            ->where('user_id', $this->user->id)
            ->orderBy('expense_date')
            ->get();

        $headers = [
            'ID', 'Date', 'Amount', 'Category', 'Vendor', 'Project',
            'Payment Method', 'Reference', 'Has Receipt', 'Notes', 'Created At',
        ];

        $rows = $expenses->map(fn($e) => [
            $e->id,
            $e->expense_date,
            $e->amount,
            $e->category?->name ?? '',
            $e->vendor_name ?? '',
            $e->project?->title ?? '',
            $e->payment_method ?? '',
            $e->reference_number ?? '',
            $e->receipt_path ? 'Yes' : 'No',
            $e->notes ?? '',
            $e->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Expenses', $headers, $rows);
    }

    private function buildInvoicesCsv(): string
    {
        $invoices = Invoice::with(['client', 'project'])
            ->where('user_id', $this->user->id)
            ->orderBy('issue_date')
            ->get();

        $headers = [
            'ID', 'Invoice #', 'Client', 'Project', 'Issue Date', 'Due Date',
            'Subtotal', 'Tax', 'Discount', 'Total', 'Paid Amount', 'Outstanding',
            'Status', 'Notes', 'Created At',
        ];

        $rows = $invoices->map(fn($inv) => [
            $inv->id,
            $inv->invoice_number,
            $inv->client?->name ?? '',
            $inv->project?->title ?? '',
            $inv->issue_date,
            $inv->due_date,
            $inv->subtotal,
            $inv->tax_amount,
            $inv->discount_amount,
            $inv->total_amount,
            $inv->paid_amount,
            max(0, $inv->total_amount - $inv->paid_amount),
            $inv->status,
            $inv->notes ?? '',
            $inv->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Invoices', $headers, $rows);
    }

    private function buildInvoiceItemsCsv(): string
    {
        $items = InvoiceItem::with('invoice')
            ->whereHas('invoice', fn($q) => $q->where('user_id', $this->user->id))
            ->get();

        $headers = [
            'ID', 'Invoice #', 'Description', 'Quantity', 'Unit Price', 'Line Total',
        ];

        $rows = $items->map(fn($item) => [
            $item->id,
            $item->invoice?->invoice_number ?? '',
            $item->description,
            $item->quantity,
            $item->unit_price,
            $item->line_total,
        ])->toArray();

        return $this->makeCsv('Invoice Items', $headers, $rows);
    }

    private function buildQuotesCsv(): string
    {
        $quotes = Quote::with(['client', 'project'])
            ->where('user_id', $this->user->id)
            ->orderBy('issue_date')
            ->get();

        $headers = [
            'ID', 'Quote #', 'Client', 'Project', 'Issue Date', 'Valid Until',
            'Subtotal', 'Tax', 'Discount', 'Total', 'Status', 'Notes', 'Created At',
        ];

        $rows = $quotes->map(fn($q) => [
            $q->id,
            $q->quote_number,
            $q->client?->name ?? '',
            $q->project?->title ?? '',
            $q->issue_date,
            $q->valid_until,
            $q->subtotal,
            $q->tax_amount,
            $q->discount_amount,
            $q->total_amount,
            $q->status,
            $q->notes ?? '',
            $q->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Quotes', $headers, $rows);
    }

    private function buildQuoteItemsCsv(): string
    {
        $items = QuoteItem::with('quote')
            ->whereHas('quote', fn($q) => $q->where('user_id', $this->user->id))
            ->get();

        $headers = [
            'ID', 'Quote #', 'Description', 'Quantity', 'Unit Price', 'Line Total',
        ];

        $rows = $items->map(fn($item) => [
            $item->id,
            $item->quote?->quote_number ?? '',
            $item->description,
            $item->quantity,
            $item->unit_price,
            $item->line_total,
        ])->toArray();

        return $this->makeCsv('Quote Items', $headers, $rows);
    }

    private function buildPaymentsCsv(): string
    {
        $payments = Payment::with('invoice')
            ->whereHas('invoice', fn($q) => $q->where('user_id', $this->user->id))
            ->orderBy('payment_date')
            ->get();

        $headers = [
            'ID', 'Invoice #', 'Amount', 'Payment Date',
            'Payment Method', 'Reference', 'Created At',
        ];

        $rows = $payments->map(fn($p) => [
            $p->id,
            $p->invoice?->invoice_number ?? '',
            $p->amount,
            $p->payment_date,
            $p->payment_method ?? '',
            $p->reference_number ?? '',
            $p->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Payments', $headers, $rows);
    }

    private function buildSavingsAccountsCsv(): string
    {
        $accounts = SavingsAccount::where('user_id', $this->user->id)
            ->orderBy('name')
            ->get();

        $headers = [
            'ID', 'Name', 'Description', 'Target Amount',
            'Current Balance', 'Progress %', 'Status', 'Created At',
        ];

        $rows = $accounts->map(fn($a) => [
            $a->id,
            $a->name,
            $a->description ?? '',
            $a->target_amount ?? '',
            $a->current_balance,
            ($a->target_amount > 0)
                ? round(($a->current_balance / $a->target_amount) * 100, 1) . '%'
                : '',
            $a->status,
            $a->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Savings Accounts', $headers, $rows);
    }

    private function buildSavingsTransactionsCsv(): string
    {
        $txns = SavingsTransaction::with('savingsAccount')
            ->where('user_id', $this->user->id)
            ->orderBy('transaction_date')
            ->get();

        $headers = [
            'ID', 'Account', 'Type', 'Amount', 'Date', 'Notes', 'Created At',
        ];

        $rows = $txns->map(fn($t) => [
            $t->id,
            $t->savingsAccount?->name ?? '',
            $t->type,
            $t->amount,
            $t->transaction_date,
            $t->notes ?? '',
            $t->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Savings Transactions', $headers, $rows);
    }

    private function buildDebtsReceivableCsv(): string
    {
        $debts = DebtsReceivable::with(['client', 'invoice'])
            ->where('user_id', $this->user->id)
            ->orderBy('due_date')
            ->get();

        $headers = [
            'ID', 'Client', 'Invoice #', 'Original Amount', 'Paid Amount',
            'Outstanding', 'Due Date', 'Status', 'Notes', 'Created At',
        ];

        $rows = $debts->map(fn($d) => [
            $d->id,
            $d->client?->name ?? '',
            $d->invoice?->invoice_number ?? '',
            $d->original_amount,
            $d->paid_amount,
            max(0, $d->original_amount - $d->paid_amount),
            $d->due_date,
            $d->status,
            $d->notes ?? '',
            $d->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Debts Receivable', $headers, $rows);
    }

    private function buildDebtsPayableCsv(): string
    {
        $debts = DebtsPayable::where('user_id', $this->user->id)
            ->orderBy('due_date')
            ->get();

        $headers = [
            'ID', 'Vendor', 'Original Amount', 'Paid Amount',
            'Outstanding', 'Due Date', 'Status', 'Notes', 'Created At',
        ];

        $rows = $debts->map(fn($d) => [
            $d->id,
            $d->vendor_name,
            $d->original_amount,
            $d->paid_amount,
            max(0, $d->original_amount - $d->paid_amount),
            $d->due_date,
            $d->status,
            $d->notes ?? '',
            $d->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Debts Payable', $headers, $rows);
    }

    private function buildRecurringCsv(): string
    {
        $recurring = RecurringTransaction::with(['category', 'client', 'project'])
            ->where('user_id', $this->user->id)
            ->orderBy('name')
            ->get();

        $headers = [
            'ID', 'Name', 'Type', 'Amount', 'Frequency', 'Start Date',
            'Next Due Date', 'End Date', 'Last Generated', 'Category',
            'Client', 'Project', 'Vendor', 'Payment Method', 'Active',
            'Notes', 'Created At',
        ];

        $rows = $recurring->map(fn($r) => [
            $r->id,
            $r->name,
            $r->type,
            $r->amount,
            $r->frequency,
            $r->start_date?->format('Y-m-d') ?? '',
            $r->next_due_date?->format('Y-m-d') ?? '',
            $r->end_date?->format('Y-m-d') ?? '',
            $r->last_generated_at?->format('Y-m-d') ?? '',
            $r->category?->name ?? '',
            $r->client?->name ?? '',
            $r->project?->title ?? '',
            $r->vendor_name ?? '',
            $r->payment_method ?? '',
            $r->is_active ? 'Yes' : 'No',
            $r->notes ?? '',
            $r->created_at?->format('Y-m-d') ?? '',
        ])->toArray();

        return $this->makeCsv('Recurring Transactions', $headers, $rows);
    }
}
