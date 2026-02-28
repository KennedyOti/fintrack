<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Quote;
use App\Helpers\CurrencyHelper;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class ClientViewController extends Controller
{
    // ─── Helpers ─────────────────────────────────────────────────────────────

    private function invoiceByToken(string $token): Invoice
    {
        return Invoice::where('share_token', $token)
            ->with(['client', 'project', 'items', 'payments', 'user'])
            ->firstOrFail();
    }

    private function quoteByToken(string $token): Quote
    {
        return Quote::where('share_token', $token)
            ->with(['client', 'project', 'items', 'user'])
            ->firstOrFail();
    }

    private function businessInfo($user): array
    {
        return [
            'business_name'    => $user->business_name ?? $user->name,
            'tax_number'       => $user->tax_number ?? '',
            'business_address' => $user->business_address ?? '',
            'email'            => $user->email,
            'phone'            => $user->phone ?? '',
        ];
    }

    // ─── Invoice ──────────────────────────────────────────────────────────────

    public function viewInvoice(string $token)
    {
        $invoice       = $this->invoiceByToken($token);
        $user          = $invoice->user;
        $businessInfo  = $this->businessInfo($user);
        $currencyCode  = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        return view('public.invoice', compact('invoice', 'businessInfo', 'currencyCode', 'currencySymbol'));
    }

    public function downloadInvoicePdf(string $token)
    {
        $invoice       = $this->invoiceByToken($token);
        $user          = $invoice->user;
        $businessInfo  = $this->businessInfo($user);
        $currencyCode  = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        $pdf = Pdf::loadView('portal.invoices.pdf', compact('invoice', 'businessInfo', 'currencyCode', 'currencySymbol'));

        return $pdf->download('invoice-' . $invoice->invoice_number . '.pdf');
    }

    // ─── Quote ────────────────────────────────────────────────────────────────

    public function viewQuote(string $token)
    {
        $quote         = $this->quoteByToken($token);
        $user          = $quote->user;
        $businessInfo  = $this->businessInfo($user);
        $currencyCode  = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        return view('public.quote', compact('quote', 'businessInfo', 'currencyCode', 'currencySymbol'));
    }

    public function downloadQuotePdf(string $token)
    {
        $quote         = $this->quoteByToken($token);
        $user          = $quote->user;
        $businessInfo  = $this->businessInfo($user);
        $currencyCode  = $user->currency_code ?? 'USD';
        $currencySymbol = CurrencyHelper::getSymbol($currencyCode);

        $pdf = Pdf::loadView('portal.quotes.pdf', compact('quote', 'businessInfo', 'currencyCode', 'currencySymbol'));

        return $pdf->download('quote-' . $quote->quote_number . '.pdf');
    }

    public function acceptQuote(Request $request, string $token)
    {
        $quote = Quote::where('share_token', $token)->firstOrFail();

        if (!in_array($quote->status, ['draft', 'sent'])) {
            return redirect('/view/quote/' . $token)
                ->with('error', 'This quote cannot be accepted in its current state.');
        }

        $quote->update(['status' => 'accepted']);

        return redirect('/view/quote/' . $token)
            ->with('success', 'Thank you! The quote has been accepted. The business will be in touch shortly.');
    }

    public function rejectQuote(Request $request, string $token)
    {
        $quote = Quote::where('share_token', $token)->firstOrFail();

        if (!in_array($quote->status, ['draft', 'sent'])) {
            return redirect('/view/quote/' . $token)
                ->with('error', 'This quote cannot be rejected in its current state.');
        }

        $quote->update(['status' => 'rejected']);

        return redirect('/view/quote/' . $token)
            ->with('info', 'The quote has been rejected. The business has been notified.');
    }
}
