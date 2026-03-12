<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FreeDocAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FreeDocsAnalyticsController extends Controller
{
    public function index(Request $request)
    {
        // ── Date range ────────────────────────────────────────────────────────
        $range = $request->input('range', '30');
        $from  = match ($range) {
            '7'   => now()->subDays(7)->startOfDay(),
            '30'  => now()->subDays(30)->startOfDay(),
            '90'  => now()->subDays(90)->startOfDay(),
            '365' => now()->subDays(365)->startOfDay(),
            default => null, // all time
        };

        $query = FreeDocAnalytic::query();
        if ($from) {
            $query->where('created_at', '>=', $from);
        }

        // ── Summary cards ─────────────────────────────────────────────────────
        $totalEvents     = (clone $query)->count();
        $totalPageViews  = (clone $query)->where('event_type', FreeDocAnalytic::EVENT_PAGE_VIEW)->count();
        $uniqueVisitors  = (clone $query)->whereIn('event_type', [
                               FreeDocAnalytic::EVENT_PAGE_VIEW,
                               FreeDocAnalytic::EVENT_BUILDER_OPEN,
                           ])->distinct('session_hash')->count('session_hash');
        $totalPdfs       = (clone $query)->where('event_type', FreeDocAnalytic::EVENT_PDF_GENERATED)->count();
        $totalSaves      = (clone $query)->where('event_type', FreeDocAnalytic::EVENT_DOC_SAVED)->count();
        $totalSharedViews= (clone $query)->where('event_type', FreeDocAnalytic::EVENT_DOC_VIEWED)->count();

        // ── Documents by type (PDF generated + saved) ─────────────────────────
        $docsByType = (clone $query)
            ->whereIn('event_type', [FreeDocAnalytic::EVENT_PDF_GENERATED, FreeDocAnalytic::EVENT_DOC_SAVED])
            ->whereNotNull('doc_type')
            ->select('doc_type', DB::raw('COUNT(*) as total'))
            ->groupBy('doc_type')
            ->orderByDesc('total')
            ->get()
            ->mapWithKeys(fn($r) => [$r->doc_type => $r->total]);

        // ── Events per day (last N days) for trend chart ──────────────────────
        $trendDays  = min((int) $range ?: 90, 90);
        $trendFrom  = now()->subDays($trendDays - 1)->startOfDay();

        $trendRaw = FreeDocAnalytic::query()
            ->where('created_at', '>=', $trendFrom)
            ->select(
                DB::raw('DATE(created_at) as day'),
                'event_type',
                DB::raw('COUNT(*) as cnt')
            )
            ->groupBy('day', 'event_type')
            ->orderBy('day')
            ->get();

        // Build date-keyed arrays for each tracked event type
        $trendDates = [];
        $cursor = clone $trendFrom;
        while ($cursor->lte(now())) {
            $trendDates[] = $cursor->toDateString();
            $cursor->addDay();
        }

        $trendEvents = [
            FreeDocAnalytic::EVENT_PAGE_VIEW,
            FreeDocAnalytic::EVENT_BUILDER_OPEN,
            FreeDocAnalytic::EVENT_PDF_GENERATED,
            FreeDocAnalytic::EVENT_DOC_SAVED,
        ];

        $trendSeries = [];
        foreach ($trendEvents as $et) {
            $byDay = $trendRaw->where('event_type', $et)->pluck('cnt', 'day');
            $trendSeries[$et] = array_map(fn($d) => (int)($byDay[$d] ?? 0), $trendDates);
        }

        // ── Countries ─────────────────────────────────────────────────────────
        $countriesRaw = (clone $query)
            ->whereNotNull('country_name')
            ->select('country_code', 'country_name', DB::raw('COUNT(DISTINCT session_hash) as visitors'), DB::raw('COUNT(*) as events'))
            ->groupBy('country_code', 'country_name')
            ->orderByDesc('visitors')
            ->limit(50)
            ->get();

        $totalCountryVisitors = $countriesRaw->sum('visitors') ?: 1;

        // ── Template usage ────────────────────────────────────────────────────
        $templateUsage = (clone $query)
            ->whereIn('event_type', [FreeDocAnalytic::EVENT_PDF_GENERATED, FreeDocAnalytic::EVENT_DOC_SAVED])
            ->whereNotNull('template')
            ->select('template', DB::raw('COUNT(*) as total'))
            ->groupBy('template')
            ->orderByDesc('total')
            ->pluck('total', 'template');

        // ── Font usage ────────────────────────────────────────────────────────
        $fontUsage = (clone $query)
            ->whereIn('event_type', [FreeDocAnalytic::EVENT_PDF_GENERATED, FreeDocAnalytic::EVENT_DOC_SAVED])
            ->whereNotNull('font')
            ->select('font', DB::raw('COUNT(*) as total'))
            ->groupBy('font')
            ->orderByDesc('total')
            ->pluck('total', 'font');

        // ── Top currencies ────────────────────────────────────────────────────
        $currencyUsage = (clone $query)
            ->whereIn('event_type', [FreeDocAnalytic::EVENT_PDF_GENERATED, FreeDocAnalytic::EVENT_DOC_SAVED])
            ->whereNotNull('currency')
            ->select('currency', DB::raw('COUNT(*) as total'))
            ->groupBy('currency')
            ->orderByDesc('total')
            ->limit(15)
            ->pluck('total', 'currency');

        // ── Feature toggle rates (from docs with features_used JSON) ──────────
        $featureDocs = (clone $query)
            ->whereIn('event_type', [FreeDocAnalytic::EVENT_PDF_GENERATED, FreeDocAnalytic::EVENT_DOC_SAVED])
            ->whereNotNull('features_used')
            ->get(['features_used']);

        $featureTotal = $featureDocs->count() ?: 1;
        $featureNames = ['tax', 'discount', 'shipping', 'logo', 'notes', 'terms', 'payment_info'];
        $featureRates = [];

        foreach ($featureNames as $feat) {
            $count = $featureDocs->filter(fn($r) => !empty($r->features_used[$feat]))->count();
            $featureRates[$feat] = [
                'count'   => $count,
                'percent' => round(($count / $featureTotal) * 100, 1),
            ];
        }

        // Average item count
        $avgItems = $featureDocs->avg(fn($r) => $r->features_used['item_count'] ?? 0);

        // ── Top referrer domains ──────────────────────────────────────────────
        $referrers = (clone $query)
            ->whereNotNull('referrer_domain')
            ->select('referrer_domain', DB::raw('COUNT(*) as total'))
            ->groupBy('referrer_domain')
            ->orderByDesc('total')
            ->limit(10)
            ->pluck('total', 'referrer_domain');

        // ── Recent events ─────────────────────────────────────────────────────
        $recentEvents = FreeDocAnalytic::query()
            ->orderByDesc('created_at')
            ->limit(50)
            ->get();

        return view('admin.free-docs.analytics', compact(
            'range',
            'totalEvents',
            'totalPageViews',
            'uniqueVisitors',
            'totalPdfs',
            'totalSaves',
            'totalSharedViews',
            'docsByType',
            'trendDates',
            'trendSeries',
            'trendEvents',
            'countriesRaw',
            'totalCountryVisitors',
            'templateUsage',
            'fontUsage',
            'currencyUsage',
            'featureRates',
            'featureTotal',
            'avgItems',
            'referrers',
            'recentEvents'
        ));
    }
}
