@extends('layouts.portal')

@section('title', 'Free Docs Analytics — FinTrack Admin')

@section('content')

{{-- ── Page Header ──────────────────────────────────────────────────────────── --}}
<div class="page-header">
    <div>
        <h1 class="page-title">
            <i class="fas fa-chart-mixed me-2" style="color:var(--ft-teal);font-size:20px;"></i>
            Free Docs Analytics
        </h1>
        <p class="page-subtitle">Real visitor &amp; usage data for the Free Business Docs tool</p>
    </div>
    <div class="page-actions">
        {{-- Date range filter --}}
        <form method="GET" class="d-flex gap-2 align-items-center">
            <select name="range" class="form-select form-select-sm" onchange="this.form.submit()"
                    style="width:160px;background:var(--card-bg);border-color:var(--border-color);color:var(--text-primary);">
                <option value="7"   {{ $range=='7'   ? 'selected' : '' }}>Last 7 days</option>
                <option value="30"  {{ $range=='30'  ? 'selected' : '' }}>Last 30 days</option>
                <option value="90"  {{ $range=='90'  ? 'selected' : '' }}>Last 90 days</option>
                <option value="365" {{ $range=='365' ? 'selected' : '' }}>Last 12 months</option>
                <option value="all" {{ $range=='all' ? 'selected' : '' }}>All time</option>
            </select>
        </form>
        <a href="{{ route('admin.dashboard') }}" class="btn btn-sm btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i> Dashboard
        </a>
    </div>
</div>

{{-- ── Summary Cards ────────────────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-navy">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Page Views</div>
                    <div class="stat-value">{{ number_format($totalPageViews) }}</div>
                    <div class="stat-sub"><i class="fas fa-eye me-1"></i>Landing page</div>
                </div>
                <div class="stat-badge"><i class="fas fa-eye"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-teal">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Unique Visitors</div>
                    <div class="stat-value">{{ number_format($uniqueVisitors) }}</div>
                    <div class="stat-sub"><i class="fas fa-fingerprint me-1"></i>Distinct sessions</div>
                </div>
                <div class="stat-badge"><i class="fas fa-users"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-emerald">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">PDFs Generated</div>
                    <div class="stat-value">{{ number_format($totalPdfs) }}</div>
                    <div class="stat-sub"><i class="fas fa-file-pdf me-1"></i>Direct downloads</div>
                </div>
                <div class="stat-badge"><i class="fas fa-file-pdf"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-amber">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Docs Saved</div>
                    <div class="stat-value">{{ number_format($totalSaves) }}</div>
                    <div class="stat-sub"><i class="fas fa-link me-1"></i>Share links</div>
                </div>
                <div class="stat-badge"><i class="fas fa-share-nodes"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-violet">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Shared Views</div>
                    <div class="stat-value">{{ number_format($totalSharedViews) }}</div>
                    <div class="stat-sub"><i class="fas fa-share me-1"></i>Via share link</div>
                </div>
                <div class="stat-badge"><i class="fas fa-share"></i></div>
            </div>
        </div>
    </div>

    <div class="col-6 col-xl-2">
        <div class="card stat-card sc-rose">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <div class="stat-label">Total Events</div>
                    <div class="stat-value">{{ number_format($totalEvents) }}</div>
                    <div class="stat-sub"><i class="fas fa-bolt me-1"></i>All interactions</div>
                </div>
                <div class="stat-badge"><i class="fas fa-bolt"></i></div>
            </div>
        </div>
    </div>

</div>

{{-- ── Trend Chart + Doc Types ──────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Trend chart --}}
    <div class="col-lg-8">
        <div class="card" style="padding:20px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="mb-0" style="font-weight:600;">Daily Activity Trend</h6>
                <div class="d-flex gap-3" style="font-size:11px;color:var(--text-muted);">
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#0E7490;margin-right:4px;"></span>Page Views</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#22C55E;margin-right:4px;"></span>PDFs</span>
                    <span><span style="display:inline-block;width:10px;height:10px;border-radius:2px;background:#F59E0B;margin-right:4px;"></span>Saved</span>
                </div>
            </div>
            <canvas id="trendChart" height="100"></canvas>
        </div>
    </div>

    {{-- Document type breakdown --}}
    <div class="col-lg-4">
        <div class="card" style="padding:20px;height:100%;">
            <h6 class="mb-3" style="font-weight:600;">Documents by Type</h6>
            @if($docsByType->isEmpty())
                <div class="text-center py-4" style="color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No documents yet
                </div>
            @else
                <canvas id="docTypeChart" height="180"></canvas>
                <div class="mt-3">
                    @php
                        $typeColors = [
                            'invoice'       => '#0E7490',
                            'quote'         => '#22C55E',
                            'receipt'       => '#F59E0B',
                            'proforma'      => '#8B5CF6',
                            'purchase_order'=> '#F43F5E',
                            'delivery_note' => '#22D3EE',
                        ];
                        $typeTotal = $docsByType->sum();
                    @endphp
                    @foreach($docsByType as $type => $count)
                    <div class="d-flex justify-content-between align-items-center mb-1" style="font-size:12px;">
                        <span>
                            <span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $typeColors[$type] ?? '#888' }};margin-right:6px;"></span>
                            {{ ucwords(str_replace('_',' ',$type)) }}
                        </span>
                        <span style="font-weight:600;">{{ number_format($count) }}
                            <span style="color:var(--text-muted);font-weight:400;">({{ $typeTotal > 0 ? round(($count/$typeTotal)*100,1) : 0 }}%)</span>
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

</div>

{{-- ── Countries + Feature Usage ────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Countries table --}}
    <div class="col-lg-5">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-globe me-1" style="color:var(--ft-teal);"></i>
                Visitors by Country
            </h6>
            @if($countriesRaw->isEmpty())
                <div class="text-center py-4" style="color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-map fa-2x mb-2 d-block"></i>No geo data yet
                </div>
            @else
                <div style="max-height:340px;overflow-y:auto;">
                    <table class="table table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr style="color:var(--text-muted);">
                                <th>#</th>
                                <th>Country</th>
                                <th class="text-end">Visitors</th>
                                <th class="text-end">Events</th>
                                <th class="text-end">Share</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($countriesRaw as $i => $row)
                            <tr>
                                <td style="color:var(--text-muted);">{{ $i+1 }}</td>
                                <td>
                                    @if($row->country_code && $row->country_code !== 'XX')
                                        <img src="https://flagcdn.com/16x12/{{ strtolower($row->country_code) }}.png"
                                             width="16" height="12"
                                             alt="{{ $row->country_code }}"
                                             onerror="this.style.display='none'"
                                             class="me-1">
                                    @else
                                        <i class="fas fa-server me-1" style="color:var(--text-muted);font-size:10px;"></i>
                                    @endif
                                    {{ $row->country_name ?? 'Unknown' }}
                                </td>
                                <td class="text-end" style="font-weight:600;">{{ number_format($row->visitors) }}</td>
                                <td class="text-end" style="color:var(--text-muted);">{{ number_format($row->events) }}</td>
                                <td class="text-end">
                                    <div class="d-flex align-items-center justify-content-end gap-1">
                                        <div style="width:50px;height:4px;background:var(--border-color);border-radius:2px;overflow:hidden;">
                                            <div style="width:{{ min(round(($row->visitors/$totalCountryVisitors)*100),100) }}%;height:100%;background:var(--ft-teal);border-radius:2px;"></div>
                                        </div>
                                        <span style="font-size:11px;color:var(--text-muted);min-width:30px;text-align:right;">{{ round(($row->visitors/$totalCountryVisitors)*100,1) }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Feature usage --}}
    <div class="col-lg-7">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-sliders me-1" style="color:var(--ft-teal);"></i>
                Feature Usage
                <span style="font-weight:400;font-size:12px;color:var(--text-muted);">— based on {{ number_format($featureTotal) }} documents</span>
            </h6>

            @php
                $featureLabels = [
                    'tax'          => ['Tax',            'fas fa-percent',        '#0E7490'],
                    'discount'     => ['Discount',       'fas fa-tag',            '#22C55E'],
                    'shipping'     => ['Shipping',       'fas fa-truck',          '#F59E0B'],
                    'logo'         => ['Logo Uploaded',  'fas fa-image',          '#8B5CF6'],
                    'notes'        => ['Notes',          'fas fa-sticky-note',    '#22D3EE'],
                    'terms'        => ['Terms & Cond.',  'fas fa-file-contract',  '#F43F5E'],
                    'payment_info' => ['Payment Info',   'fas fa-credit-card',    '#0B2A4A'],
                ];
            @endphp

            <div class="row g-2 mb-3">
                @foreach($featureLabels as $key => [$label, $icon, $color])
                @php $rate = $featureRates[$key] ?? ['count'=>0,'percent'=>0]; @endphp
                <div class="col-6 col-md-4">
                    <div style="background:var(--card-bg);border:1px solid var(--border-color);border-radius:8px;padding:10px;">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <i class="{{ $icon }}" style="color:{{ $color }};font-size:13px;"></i>
                            <span style="font-size:12px;font-weight:500;">{{ $label }}</span>
                        </div>
                        <div style="font-size:20px;font-weight:700;">{{ $rate['percent'] }}%</div>
                        <div style="font-size:11px;color:var(--text-muted);">{{ number_format($rate['count']) }} docs</div>
                        <div style="height:3px;background:var(--border-color);border-radius:2px;margin-top:6px;overflow:hidden;">
                            <div style="width:{{ $rate['percent'] }}%;height:100%;background:{{ $color }};border-radius:2px;"></div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <div class="d-flex gap-3 flex-wrap" style="font-size:12px;color:var(--text-muted);border-top:1px solid var(--border-color);padding-top:10px;">
                <span><i class="fas fa-list me-1"></i>Avg. {{ number_format($avgItems, 1) }} line items per doc</span>
            </div>
        </div>
    </div>

</div>

{{-- ── Template / Font / Currency ──────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Template usage --}}
    <div class="col-lg-4">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-palette me-1" style="color:var(--ft-teal);"></i>
                Template Preference
            </h6>
            @if($templateUsage->isEmpty())
                <div class="text-center py-3" style="color:var(--text-muted);font-size:13px;">No data yet</div>
            @else
                <canvas id="templateChart" height="160"></canvas>
            @endif
        </div>
    </div>

    {{-- Font usage --}}
    <div class="col-lg-4">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-font me-1" style="color:var(--ft-teal);"></i>
                Font Preference
            </h6>
            @if($fontUsage->isEmpty())
                <div class="text-center py-3" style="color:var(--text-muted);font-size:13px;">No data yet</div>
            @else
                @php $fontTotal = $fontUsage->sum() ?: 1; @endphp
                @foreach($fontUsage as $font => $cnt)
                <div class="mb-2">
                    <div class="d-flex justify-content-between" style="font-size:12px;margin-bottom:3px;">
                        <span>{{ $font ?: 'Default' }}</span>
                        <span style="font-weight:600;">{{ $cnt }} <span style="color:var(--text-muted);font-weight:400;">({{ round(($cnt/$fontTotal)*100,1) }}%)</span></span>
                    </div>
                    <div style="height:5px;background:var(--border-color);border-radius:3px;overflow:hidden;">
                        <div style="width:{{ round(($cnt/$fontTotal)*100,1) }}%;height:100%;background:var(--ft-teal);border-radius:3px;"></div>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Currency usage --}}
    <div class="col-lg-4">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-coins me-1" style="color:var(--ft-teal);"></i>
                Top Currencies
            </h6>
            @if($currencyUsage->isEmpty())
                <div class="text-center py-3" style="color:var(--text-muted);font-size:13px;">No data yet</div>
            @else
                @php $currTotal = $currencyUsage->sum() ?: 1; @endphp
                @foreach($currencyUsage as $curr => $cnt)
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:12px;">
                    <span style="font-weight:600;min-width:50px;">{{ $curr }}</span>
                    <div style="flex:1;margin:0 8px;height:5px;background:var(--border-color);border-radius:3px;overflow:hidden;">
                        <div style="width:{{ round(($cnt/$currTotal)*100,1) }}%;height:100%;background:var(--ft-emerald);border-radius:3px;"></div>
                    </div>
                    <span style="color:var(--text-muted);min-width:35px;text-align:right;">{{ $cnt }}</span>
                </div>
                @endforeach
            @endif
        </div>
    </div>

</div>

{{-- ── Referrers + Recent Events ────────────────────────────────────────────── --}}
<div class="row g-3 mb-4">

    {{-- Top referrers --}}
    <div class="col-lg-4">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-arrow-pointer me-1" style="color:var(--ft-teal);"></i>
                Top Referrers
            </h6>
            @if($referrers->isEmpty())
                <div class="text-center py-4" style="color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-link-slash fa-2x mb-2 d-block"></i>No referrer data
                </div>
            @else
                @php $refTotal = $referrers->sum() ?: 1; @endphp
                @foreach($referrers as $domain => $cnt)
                <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:12px;">
                    <span style="max-width:160px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $domain }}</span>
                    <div class="d-flex align-items-center gap-2">
                        <div style="width:60px;height:4px;background:var(--border-color);border-radius:2px;overflow:hidden;">
                            <div style="width:{{ round(($cnt/$refTotal)*100) }}%;height:100%;background:var(--ft-amber);border-radius:2px;"></div>
                        </div>
                        <span style="color:var(--text-muted);min-width:24px;text-align:right;">{{ $cnt }}</span>
                    </div>
                </div>
                @endforeach
            @endif
        </div>
    </div>

    {{-- Recent events --}}
    <div class="col-lg-8">
        <div class="card" style="padding:20px;">
            <h6 class="mb-3" style="font-weight:600;">
                <i class="fas fa-clock-rotate-left me-1" style="color:var(--ft-teal);"></i>
                Recent Events <span style="font-weight:400;font-size:12px;color:var(--text-muted);">(last 50)</span>
            </h6>
            @if($recentEvents->isEmpty())
                <div class="text-center py-4" style="color:var(--text-muted);font-size:13px;">
                    <i class="fas fa-inbox fa-2x mb-2 d-block"></i>No events recorded yet
                </div>
            @else
                <div style="max-height:340px;overflow-y:auto;">
                    <table class="table table-sm mb-0" style="font-size:12px;">
                        <thead>
                            <tr style="color:var(--text-muted);">
                                <th>Event</th>
                                <th>Doc Type</th>
                                <th>Template</th>
                                <th>Country</th>
                                <th>Time</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEvents as $ev)
                            @php
                                $evColors = [
                                    'page_view'      => '#0E7490',
                                    'builder_open'   => '#8B5CF6',
                                    'pdf_generated'  => '#22C55E',
                                    'doc_saved'      => '#F59E0B',
                                    'doc_viewed'     => '#22D3EE',
                                    'pdf_from_share' => '#F43F5E',
                                ];
                                $color = $evColors[$ev->event_type] ?? '#888';
                            @endphp
                            <tr>
                                <td>
                                    <span style="display:inline-block;padding:2px 7px;border-radius:12px;font-size:11px;font-weight:600;background:{{ $color }}22;color:{{ $color }};">
                                        {{ \App\Models\FreeDocAnalytic::eventLabel($ev->event_type) }}
                                    </span>
                                </td>
                                <td>{{ $ev->doc_type ? ucwords(str_replace('_',' ',$ev->doc_type)) : '—' }}</td>
                                <td>{{ $ev->template ? ucfirst($ev->template) : '—' }}</td>
                                <td>
                                    @if($ev->country_code && $ev->country_code !== 'XX')
                                        <img src="https://flagcdn.com/16x12/{{ strtolower($ev->country_code) }}.png"
                                             width="16" height="12" alt="{{ $ev->country_code }}"
                                             onerror="this.style.display='none'"
                                             class="me-1">
                                    @endif
                                    {{ $ev->country_name ?? '—' }}
                                </td>
                                <td style="color:var(--text-muted);white-space:nowrap;">{{ $ev->created_at->diffForHumans() }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

</div>

@endsection

@push('scripts')
<script>
(function () {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const gridColor  = isDark ? 'rgba(255,255,255,0.06)' : 'rgba(0,0,0,0.06)';
    const textColor  = isDark ? '#9CA3AF' : '#6B7280';

    Chart.defaults.color = textColor;
    Chart.defaults.borderColor = gridColor;

    // ── Trend chart ────────────────────────────────────────────────────────
    const trendDates  = @json($trendDates);
    const trendSeries = @json($trendSeries);

    const trendCtx = document.getElementById('trendChart');
    if (trendCtx) {
        new Chart(trendCtx, {
            type: 'line',
            data: {
                labels: trendDates,
                datasets: [
                    {
                        label: 'Page Views',
                        data: trendSeries['page_view'] ?? [],
                        borderColor: '#0E7490',
                        backgroundColor: 'rgba(14,116,144,0.08)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 2,
                    },
                    {
                        label: 'PDFs Downloaded',
                        data: trendSeries['pdf_generated'] ?? [],
                        borderColor: '#22C55E',
                        backgroundColor: 'rgba(34,197,94,0.08)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 2,
                    },
                    {
                        label: 'Docs Saved',
                        data: trendSeries['doc_saved'] ?? [],
                        borderColor: '#F59E0B',
                        backgroundColor: 'rgba(245,158,11,0.08)',
                        tension: 0.3,
                        fill: true,
                        pointRadius: 2,
                    },
                    {
                        label: 'Builder Opens',
                        data: trendSeries['builder_open'] ?? [],
                        borderColor: '#8B5CF6',
                        backgroundColor: 'rgba(139,92,246,0.05)',
                        tension: 0.3,
                        fill: false,
                        pointRadius: 2,
                        borderDash: [4, 4],
                    },
                ]
            },
            options: {
                responsive: true,
                interaction: { mode: 'index', intersect: false },
                plugins: { legend: { display: false } },
                scales: {
                    x: {
                        ticks: { maxTicksLimit: 10, font: { size: 11 } },
                        grid: { color: gridColor },
                    },
                    y: {
                        beginAtZero: true,
                        ticks: { font: { size: 11 }, precision: 0 },
                        grid: { color: gridColor },
                    },
                },
            }
        });
    }

    // ── Doc type doughnut ──────────────────────────────────────────────────
    const docsByType = @json($docsByType);
    const docTypeCtx = document.getElementById('docTypeChart');
    if (docTypeCtx && Object.keys(docsByType).length) {
        const typeColors = {
            invoice:        '#0E7490',
            quote:          '#22C55E',
            receipt:        '#F59E0B',
            proforma:       '#8B5CF6',
            purchase_order: '#F43F5E',
            delivery_note:  '#22D3EE',
        };
        new Chart(docTypeCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(docsByType).map(k => k.replace('_', ' ').replace(/\b\w/g, c => c.toUpperCase())),
                datasets: [{
                    data: Object.values(docsByType),
                    backgroundColor: Object.keys(docsByType).map(k => typeColors[k] ?? '#888'),
                    borderWidth: 0,
                    hoverOffset: 6,
                }]
            },
            options: {
                cutout: '65%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: {
                        label: ctx => ` ${ctx.label}: ${ctx.parsed.toLocaleString()}`
                    }}
                }
            }
        });
    }

    // ── Template bar chart ─────────────────────────────────────────────────
    const templateUsage = @json($templateUsage);
    const templateCtx   = document.getElementById('templateChart');
    if (templateCtx && Object.keys(templateUsage).length) {
        new Chart(templateCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(templateUsage).map(k => k.replace(/\b\w/g, c => c.toUpperCase())),
                datasets: [{
                    label: 'Uses',
                    data: Object.values(templateUsage),
                    backgroundColor: ['#0E7490', '#22C55E', '#F59E0B', '#8B5CF6'],
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                indexAxis: 'y',
                plugins: { legend: { display: false } },
                scales: {
                    x: { beginAtZero: true, ticks: { precision: 0, font:{size:11} }, grid:{color:gridColor} },
                    y: { ticks: { font:{size:12} }, grid:{display:false} },
                }
            }
        });
    }

})();
</script>
@endpush
