<?php

namespace App\Http\Controllers;

use App\Models\FreeDoc;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class FreeDocsController extends Controller
{
    private const VALID_TYPES     = ['invoice', 'quote', 'receipt', 'proforma', 'purchase_order', 'delivery_note'];
    private const VALID_TEMPLATES = ['streamline', 'classic', 'minimal', 'bold'];

    // ── Landing page ─────────────────────────────────────────────────────────
    public function index()
    {
        return view('website.free-docs.index');
    }

    // ── Builder page ─────────────────────────────────────────────────────────
    public function builder(string $type)
    {
        abort_if(!in_array($type, self::VALID_TYPES), 404);
        return view('website.free-docs.builder', compact('type'));
    }

    // ── Generate & download PDF ───────────────────────────────────────────────
    public function generatePdf(Request $request)
    {
        $data = $request->validate([
            'type'     => 'required|in:invoice,quote,receipt,proforma,purchase_order,delivery_note',
            'template' => 'required|in:streamline,classic,minimal,bold',
            'doc'      => 'required|array',
        ]);

        $doc      = $data['doc'];
        $template = $data['template'];
        $type     = $data['type'];

        $filename = strtoupper(str_replace('_', '-', $type)) . '-' . ($doc['details']['number'] ?? '001') . '.pdf';

        $pdf = Pdf::loadView("website.free-docs.pdf.{$template}", [
            'doc'  => $doc,
            'type' => $type,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }

    // ── Save doc and return a shareable link ──────────────────────────────────
    public function save(Request $request)
    {
        $data = $request->validate([
            'type'     => 'required|in:invoice,quote,receipt,proforma,purchase_order,delivery_note',
            'template' => 'required|in:streamline,classic,minimal,bold',
            'doc'      => 'required|array',
        ]);

        // Enforce a 5 MB cap on document_data (logo base64 can be large)
        if (strlen(json_encode($data['doc'])) > 5 * 1024 * 1024) {
            return response()->json(['error' => 'Document data too large. Please use a smaller logo image.'], 422);
        }

        do {
            $token = Str::random(40);
        } while (FreeDoc::where('token', $token)->exists());

        $freeDoc = FreeDoc::create([
            'type'          => $data['type'],
            'token'         => $token,
            'document_data' => array_merge($data['doc'], ['template' => $data['template']]),
            'expires_at'    => now()->addDays(60),
        ]);

        return response()->json([
            'token' => $token,
            'url'   => route('free-docs.view', $token),
        ]);
    }

    // ── Public view of a shared document ─────────────────────────────────────
    public function view(string $token)
    {
        $doc = FreeDoc::active()->where('token', $token)->firstOrFail();
        $doc->increment('view_count');

        return view('website.free-docs.view', [
            'freeDoc'  => $doc,
            'docData'  => $doc->document_data,
            'type'     => $doc->type,
            'template' => $doc->document_data['template'] ?? 'streamline',
        ]);
    }

    // ── Download PDF from a shared link ──────────────────────────────────────
    public function viewPdf(string $token)
    {
        $freeDoc = FreeDoc::active()->where('token', $token)->firstOrFail();

        $docData  = $freeDoc->document_data;
        $type     = $freeDoc->type;
        $template = $docData['template'] ?? 'streamline';

        if (!in_array($template, self::VALID_TEMPLATES)) {
            $template = 'streamline';
        }

        $filename = strtoupper(str_replace('_', '-', $type)) . '-' . ($docData['details']['number'] ?? '001') . '.pdf';

        $pdf = Pdf::loadView("website.free-docs.pdf.{$template}", [
            'doc'  => $docData,
            'type' => $type,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
