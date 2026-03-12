<?php

namespace App\Http\Controllers;

use App\Models\FreeDoc;
use App\Models\FreeDocAnalytic;
use App\Services\FreeDocsAnalyticsService;
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
        FreeDocsAnalyticsService::track(FreeDocAnalytic::EVENT_PAGE_VIEW);
        return view('website.free-docs.index');
    }

    // ── Builder page ─────────────────────────────────────────────────────────
    public function builder(string $type)
    {
        abort_if(!in_array($type, self::VALID_TYPES), 404);
        FreeDocsAnalyticsService::track(FreeDocAnalytic::EVENT_BUILDER_OPEN, ['doc_type' => $type]);
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

        FreeDocsAnalyticsService::track(
            FreeDocAnalytic::EVENT_PDF_GENERATED,
            FreeDocsAnalyticsService::extractDocMeta($doc, $type, $template)
        );

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

        FreeDoc::create([
            'type'          => $data['type'],
            'token'         => $token,
            'document_data' => array_merge($data['doc'], ['template' => $data['template']]),
            'expires_at'    => now()->addDays(60),
        ]);

        FreeDocsAnalyticsService::track(
            FreeDocAnalytic::EVENT_DOC_SAVED,
            FreeDocsAnalyticsService::extractDocMeta($data['doc'], $data['type'], $data['template'])
        );

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

        FreeDocsAnalyticsService::track(FreeDocAnalytic::EVENT_DOC_VIEWED, ['doc_type' => $doc->type]);

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

        FreeDocsAnalyticsService::track(FreeDocAnalytic::EVENT_PDF_FROM_SHARE, ['doc_type' => $type]);

        $filename = strtoupper(str_replace('_', '-', $type)) . '-' . ($docData['details']['number'] ?? '001') . '.pdf';

        $pdf = Pdf::loadView("website.free-docs.pdf.{$template}", [
            'doc'  => $docData,
            'type' => $type,
        ])->setPaper('a4', 'portrait');

        return $pdf->download($filename);
    }
}
