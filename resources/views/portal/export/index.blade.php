@extends('layouts.portal')

@section('title', 'Export My Data — FinTrack')

@section('styles')
<style>
/* ── Export page ─────────────────────────────────────────────────────────── */
.export-hero {
    background: linear-gradient(135deg, var(--ft-navy) 0%, var(--ft-blue) 60%, #0a4a7a 100%);
    border-radius: var(--r-lg);
    padding: 36px 40px;
    color: #fff;
    position: relative;
    overflow: hidden;
    margin-bottom: 24px;
}
.export-hero::before {
    content: '';
    position: absolute;
    top: -60px; right: -60px;
    width: 260px; height: 260px;
    background: rgba(14,116,144,.18);
    border-radius: 50%;
    pointer-events: none;
}
.export-hero::after {
    content: '';
    position: absolute;
    bottom: -80px; right: 80px;
    width: 180px; height: 180px;
    background: rgba(34,211,238,.08);
    border-radius: 50%;
    pointer-events: none;
}
.export-hero-icon {
    width: 56px; height: 56px;
    background: rgba(255,255,255,.12);
    border-radius: var(--r-md);
    display: flex; align-items: center; justify-content: center;
    font-size: 24px;
    margin-bottom: 16px;
    backdrop-filter: blur(4px);
    border: 1px solid rgba(255,255,255,.15);
}
.export-hero h1 {
    font-size: 22px; font-weight: 700;
    margin: 0 0 6px;
    color: #fff;
}
.export-hero p {
    font-size: 13.5px;
    color: rgba(255,255,255,.75);
    margin: 0;
    max-width: 560px;
    line-height: 1.65;
}
.export-badges {
    display: flex; flex-wrap: wrap; gap: 8px;
    margin-top: 18px;
}
.export-badge {
    display: inline-flex; align-items: center; gap: 6px;
    background: rgba(255,255,255,.1);
    border: 1px solid rgba(255,255,255,.18);
    border-radius: 20px;
    padding: 4px 12px;
    font-size: 12px;
    color: rgba(255,255,255,.9);
    backdrop-filter: blur(4px);
}
.export-badge i { font-size: 10px; color: var(--ft-cyan); }

/* ── Stats row ───────────────────────────────────────────────────────────── */
.export-stats {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 16px;
    margin-bottom: 24px;
}
@media (max-width: 900px) { .export-stats { grid-template-columns: repeat(2,1fr); } }
@media (max-width: 520px)  { .export-stats { grid-template-columns: 1fr 1fr; } }

.export-stat-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--r-md);
    padding: 18px 20px;
    display: flex; align-items: center; gap: 14px;
    box-shadow: var(--shd-sm);
}
.export-stat-icon {
    width: 40px; height: 40px; flex-shrink: 0;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px;
}
.export-stat-label { font-size: 11.5px; color: var(--text-muted); font-weight: 500; margin-bottom: 2px; }
.export-stat-value { font-size: 18px; font-weight: 700; color: var(--text-h); line-height: 1; }

/* ── Two-column layout ───────────────────────────────────────────────────── */
.export-grid {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 20px;
    align-items: start;
}
@media (max-width: 960px) { .export-grid { grid-template-columns: 1fr; } }

/* ── Section card ────────────────────────────────────────────────────────── */
.export-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shd-sm);
    overflow: hidden;
}
.export-card-header {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 10px;
}
.export-card-header-icon {
    width: 32px; height: 32px;
    border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px; flex-shrink: 0;
}
.export-card-header h2 {
    font-size: 14px; font-weight: 700;
    color: var(--text-h);
    margin: 0;
}
.export-card-header p {
    font-size: 12px; color: var(--text-muted);
    margin: 0;
}

/* ── Module list ─────────────────────────────────────────────────────────── */
.module-list { list-style: none; margin: 0; padding: 0; }
.module-item {
    display: flex; align-items: center; gap: 14px;
    padding: 13px 22px;
    border-bottom: 1px solid var(--border-light);
    transition: background var(--t);
}
.module-item:last-child { border-bottom: none; }
.module-item:hover { background: #f8fafc; }
.module-icon {
    width: 34px; height: 34px; flex-shrink: 0;
    border-radius: 9px;
    display: flex; align-items: center; justify-content: center;
    font-size: 13px;
}
.module-info { flex: 1; min-width: 0; }
.module-name { font-size: 13px; font-weight: 600; color: var(--text-h); }
.module-desc { font-size: 11.5px; color: var(--text-muted); margin-top: 1px; }
.module-filename {
    font-size: 11px; font-family: 'Courier New', monospace;
    color: var(--ft-teal); background: rgba(14,116,144,.07);
    padding: 2px 7px; border-radius: 4px;
    white-space: nowrap;
}
.module-count {
    font-size: 12px; font-weight: 600;
    color: var(--text-muted);
    min-width: 36px; text-align: right;
}

/* ── Download card ───────────────────────────────────────────────────────── */
.download-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shd-sm);
    overflow: hidden;
    position: sticky;
    top: 76px;
}
.download-card-top {
    background: linear-gradient(135deg, var(--ft-navy), var(--ft-blue));
    padding: 24px;
    text-align: center;
}
.download-card-zip {
    width: 64px; height: 64px;
    background: rgba(255,255,255,.1);
    border-radius: 16px;
    display: flex; align-items: center; justify-content: center;
    font-size: 28px;
    margin: 0 auto 14px;
    border: 1px solid rgba(255,255,255,.15);
}
.download-card-top h3 {
    font-size: 15px; font-weight: 700;
    color: #fff; margin: 0 0 6px;
}
.download-card-top p {
    font-size: 12px; color: rgba(255,255,255,.65);
    margin: 0;
}
.download-card-body { padding: 20px 22px; }

.btn-export {
    display: flex; align-items: center; justify-content: center;
    gap: 8px;
    width: 100%;
    padding: 12px 20px;
    background: linear-gradient(135deg, var(--ft-teal), #0a6382);
    color: #fff;
    border: none;
    border-radius: var(--r-sm);
    font-size: 14px; font-weight: 600;
    cursor: pointer;
    transition: all var(--t);
    text-decoration: none;
}
.btn-export:hover {
    background: linear-gradient(135deg, #0a6382, var(--ft-navy));
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 4px 16px rgba(14,116,144,.35);
}
.btn-export:active { transform: translateY(0); }
.btn-export i { font-size: 15px; }

.format-list { margin: 0; padding: 0; list-style: none; }
.format-item {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 9px 0;
    border-bottom: 1px solid var(--border-light);
    font-size: 12.5px;
    color: var(--text-body);
}
.format-item:last-child { border-bottom: none; }
.format-item i { color: var(--ft-teal); margin-top: 2px; font-size: 11px; flex-shrink: 0; }

/* ── GDPR section ────────────────────────────────────────────────────────── */
.gdpr-card {
    background: var(--bg-card);
    border: 1px solid var(--border);
    border-radius: var(--r-lg);
    box-shadow: var(--shd-sm);
    overflow: hidden;
    margin-top: 20px;
}
.gdpr-header {
    display: flex; align-items: center; gap: 12px;
    padding: 18px 22px;
    border-bottom: 1px solid var(--border);
    background: linear-gradient(135deg, rgba(139,92,246,.06), rgba(14,116,144,.04));
}
.gdpr-header-icon {
    width: 36px; height: 36px;
    background: rgba(139,92,246,.1);
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 15px; color: var(--ft-violet);
    flex-shrink: 0;
}
.gdpr-header h2 {
    font-size: 14px; font-weight: 700;
    color: var(--text-h); margin: 0;
}
.gdpr-header p {
    font-size: 12px; color: var(--text-muted); margin: 0;
}
.gdpr-rights { padding: 4px 0; }
.gdpr-right {
    display: flex; align-items: flex-start; gap: 14px;
    padding: 16px 22px;
    border-bottom: 1px solid var(--border-light);
}
.gdpr-right:last-child { border-bottom: none; }
.gdpr-right-icon {
    width: 36px; height: 36px; flex-shrink: 0;
    border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px;
}
.gdpr-right-content h4 {
    font-size: 13px; font-weight: 600;
    color: var(--text-h); margin: 0 0 3px;
}
.gdpr-right-content p {
    font-size: 12px; color: var(--text-muted);
    margin: 0; line-height: 1.6;
}

/* ── Loading overlay ─────────────────────────────────────────────────────── */
.export-loading {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(11,42,74,.72);
    backdrop-filter: blur(4px);
    z-index: 9999;
    align-items: center;
    justify-content: center;
    flex-direction: column;
    gap: 20px;
}
.export-loading.show { display: flex; }
.export-loading-box {
    background: #fff;
    border-radius: var(--r-lg);
    padding: 40px 48px;
    text-align: center;
    box-shadow: var(--shd-md);
    max-width: 360px;
}
.export-spinner {
    width: 56px; height: 56px;
    border: 4px solid rgba(14,116,144,.15);
    border-top-color: var(--ft-teal);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
    margin: 0 auto 18px;
}
@keyframes spin { to { transform: rotate(360deg); } }
.export-loading-title {
    font-size: 16px; font-weight: 700;
    color: var(--text-h); margin-bottom: 6px;
}
.export-loading-sub {
    font-size: 13px; color: var(--text-muted);
    line-height: 1.5;
}
.export-progress-steps {
    list-style: none; padding: 0; margin: 18px 0 0;
    text-align: left;
}
.export-progress-steps li {
    font-size: 12px; color: var(--text-muted);
    padding: 4px 0;
    display: flex; align-items: center; gap: 8px;
}
.export-progress-steps li i {
    font-size: 11px; color: var(--ft-teal);
}
</style>
@endsection

@section('content')

{{-- Loading overlay --}}
<div class="export-loading" id="exportLoading">
    <div class="export-loading-box">
        <div class="export-spinner"></div>
        <div class="export-loading-title">Preparing Your Export</div>
        <div class="export-loading-sub">We're packaging all your data into a ZIP archive. This may take a moment.</div>
        <ul class="export-progress-steps">
            <li><i class="fas fa-check"></i> Gathering your records</li>
            <li><i class="fas fa-check"></i> Generating branded CSV files</li>
            <li><i class="fas fa-check"></i> Creating secure ZIP archive</li>
            <li><i class="fas fa-arrow-down"></i> Downloading to your device</li>
        </ul>
    </div>
</div>

{{-- Page header --}}
<div class="page-header">
    <div>
        <h1 class="page-title">Export My Data</h1>
        <p class="page-subtitle">Download a complete copy of your FinTrack account data</p>
    </div>
    <div class="page-actions">
        <a href="{{ route('settings.index') }}" class="btn btn-sm btn-light">
            <i class="fas fa-gear me-1"></i>Settings
        </a>
    </div>
</div>

{{-- Hero banner --}}
<div class="export-hero">
    <div class="export-hero-icon">
        <i class="fas fa-download"></i>
    </div>
    <h1>Your Data, Your Rights</h1>
    <p>
        Export a complete, machine-readable copy of everything FinTrack holds about you —
        clients, invoices, transactions, savings, and more. Your export is packaged as a
        ZIP file containing individually branded CSV files for each module, ready to open
        in any spreadsheet application.
    </p>
    <div class="export-badges">
        <span class="export-badge"><i class="fas fa-shield-halved"></i>GDPR Compliant</span>
        <span class="export-badge"><i class="fas fa-file-csv"></i>17 CSV Files</span>
        <span class="export-badge"><i class="fas fa-file-zipper"></i>ZIP Archive</span>
        <span class="export-badge"><i class="fas fa-lock"></i>Your Data Only</span>
        <span class="export-badge"><i class="fas fa-table"></i>Spreadsheet Ready</span>
    </div>
</div>

{{-- Stats row --}}
<div class="export-stats">
    <div class="export-stat-card">
        <div class="export-stat-icon" style="background:rgba(14,116,144,.1);">
            <i class="fas fa-database" style="color:var(--ft-teal);"></i>
        </div>
        <div>
            <div class="export-stat-label">Total Records</div>
            <div class="export-stat-value">{{ number_format($totalRecords) }}</div>
        </div>
    </div>
    <div class="export-stat-card">
        <div class="export-stat-icon" style="background:rgba(34,197,94,.1);">
            <i class="fas fa-file-csv" style="color:var(--ft-emerald);"></i>
        </div>
        <div>
            <div class="export-stat-label">CSV Files</div>
            <div class="export-stat-value">17</div>
        </div>
    </div>
    <div class="export-stat-card">
        <div class="export-stat-icon" style="background:rgba(245,158,11,.1);">
            <i class="fas fa-users" style="color:var(--ft-amber);"></i>
        </div>
        <div>
            <div class="export-stat-label">Clients</div>
            <div class="export-stat-value">{{ number_format($counts['clients']) }}</div>
        </div>
    </div>
    <div class="export-stat-card">
        <div class="export-stat-icon" style="background:rgba(244,63,94,.1);">
            <i class="fas fa-file-invoice-dollar" style="color:var(--ft-rose);"></i>
        </div>
        <div>
            <div class="export-stat-label">Invoices</div>
            <div class="export-stat-value">{{ number_format($counts['invoices']) }}</div>
        </div>
    </div>
</div>

{{-- Main grid --}}
<div class="export-grid">

    {{-- Left: Module list --}}
    <div>
        <div class="export-card">
            <div class="export-card-header">
                <div class="export-card-header-icon" style="background:rgba(14,116,144,.1);">
                    <i class="fas fa-layer-group" style="color:var(--ft-teal);"></i>
                </div>
                <div>
                    <h2>Included Modules</h2>
                    <p>Every CSV in your download is individually branded with your account info</p>
                </div>
            </div>

            <ul class="module-list">

                {{-- Profile --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(11,42,74,.08);">
                        <i class="fas fa-user" style="color:var(--ft-navy);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Profile</div>
                        <div class="module-desc">Your account details and business information</div>
                    </div>
                    <code class="module-filename">01_profile.csv</code>
                    <div class="module-count">1</div>
                </li>

                {{-- Clients --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(245,158,11,.1);">
                        <i class="fas fa-users" style="color:var(--ft-amber);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Clients</div>
                        <div class="module-desc">Contact details, status and notes for all clients</div>
                    </div>
                    <code class="module-filename">02_clients.csv</code>
                    <div class="module-count">{{ number_format($counts['clients']) }}</div>
                </li>

                {{-- Projects --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(139,92,246,.1);">
                        <i class="fas fa-diagram-project" style="color:var(--ft-violet);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Projects & Milestones</div>
                        <div class="module-desc">Project details, budgets, timelines, and milestones</div>
                    </div>
                    <code class="module-filename">03–04_projects.csv</code>
                    <div class="module-count">{{ number_format($counts['projects']) }}</div>
                </li>

                {{-- Categories --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(14,116,144,.1);">
                        <i class="fas fa-tags" style="color:var(--ft-teal);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Categories</div>
                        <div class="module-desc">Income and expense categories with monthly budgets</div>
                    </div>
                    <code class="module-filename">05_categories.csv</code>
                    <div class="module-count">{{ number_format($counts['categories']) }}</div>
                </li>

                {{-- Income --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(34,197,94,.1);">
                        <i class="fas fa-arrow-trend-up" style="color:var(--ft-emerald);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Income</div>
                        <div class="module-desc">All income transactions with categories, clients and references</div>
                    </div>
                    <code class="module-filename">06_income.csv</code>
                    <div class="module-count">{{ number_format($counts['income']) }}</div>
                </li>

                {{-- Expenses --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(244,63,94,.1);">
                        <i class="fas fa-arrow-trend-down" style="color:var(--ft-rose);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Expenses</div>
                        <div class="module-desc">All expenses with vendors, categories, and payment methods</div>
                    </div>
                    <code class="module-filename">07_expenses.csv</code>
                    <div class="module-count">{{ number_format($counts['expenses']) }}</div>
                </li>

                {{-- Invoices --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(15,58,102,.1);">
                        <i class="fas fa-file-invoice-dollar" style="color:var(--ft-blue);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Invoices & Line Items</div>
                        <div class="module-desc">All invoices with totals, status, and itemised line items</div>
                    </div>
                    <code class="module-filename">08–09_invoices.csv</code>
                    <div class="module-count">{{ number_format($counts['invoices']) }}</div>
                </li>

                {{-- Quotes --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(14,116,144,.1);">
                        <i class="fas fa-file-contract" style="color:var(--ft-teal);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Quotes & Line Items</div>
                        <div class="module-desc">All quotes / estimates with itemised breakdowns</div>
                    </div>
                    <code class="module-filename">10–11_quotes.csv</code>
                    <div class="module-count">{{ number_format($counts['quotes']) }}</div>
                </li>

                {{-- Payments --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(34,197,94,.1);">
                        <i class="fas fa-circle-dollar-to-slot" style="color:var(--ft-emerald);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Payments</div>
                        <div class="module-desc">All recorded invoice payments with methods and references</div>
                    </div>
                    <code class="module-filename">12_payments.csv</code>
                    <div class="module-count">{{ number_format($counts['payments']) }}</div>
                </li>

                {{-- Savings --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(139,92,246,.1);">
                        <i class="fas fa-piggy-bank" style="color:var(--ft-violet);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Savings Accounts & Transactions</div>
                        <div class="module-desc">Savings goals, balances, and all deposit/withdrawal history</div>
                    </div>
                    <code class="module-filename">13–14_savings.csv</code>
                    <div class="module-count">{{ number_format($counts['savings_accounts']) }}</div>
                </li>

                {{-- Debts Receivable --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(34,211,238,.1);">
                        <i class="fas fa-hand-holding-dollar" style="color:#0891b2;"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Debts Receivable</div>
                        <div class="module-desc">Money owed to you — outstanding amounts and due dates</div>
                    </div>
                    <code class="module-filename">15_debts_receivable.csv</code>
                    <div class="module-count">{{ number_format($counts['debts_receivable']) }}</div>
                </li>

                {{-- Debts Payable --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(244,63,94,.08);">
                        <i class="fas fa-file-invoice" style="color:var(--ft-rose);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Debts Payable</div>
                        <div class="module-desc">Money you owe to vendors — amounts and due dates</div>
                    </div>
                    <code class="module-filename">16_debts_payable.csv</code>
                    <div class="module-count">{{ number_format($counts['debts_payable']) }}</div>
                </li>

                {{-- Recurring --}}
                <li class="module-item">
                    <div class="module-icon" style="background:rgba(245,158,11,.1);">
                        <i class="fas fa-sync-alt" style="color:var(--ft-amber);"></i>
                    </div>
                    <div class="module-info">
                        <div class="module-name">Recurring Transactions</div>
                        <div class="module-desc">Scheduled recurring income and expense rules</div>
                    </div>
                    <code class="module-filename">17_recurring_transactions.csv</code>
                    <div class="module-count">{{ number_format($counts['recurring']) }}</div>
                </li>

            </ul>
        </div>

        {{-- GDPR section --}}
        <div class="gdpr-card">
            <div class="gdpr-header">
                <div class="gdpr-header-icon">
                    <i class="fas fa-shield-halved"></i>
                </div>
                <div>
                    <h2>Your Data Rights (GDPR)</h2>
                    <p>We are committed to transparency and your right to control your personal data</p>
                </div>
            </div>
            <div class="gdpr-rights">
                <div class="gdpr-right">
                    <div class="gdpr-right-icon" style="background:rgba(34,197,94,.1);">
                        <i class="fas fa-copy" style="color:var(--ft-emerald);"></i>
                    </div>
                    <div class="gdpr-right-content">
                        <h4>Right to Access &amp; Portability</h4>
                        <p>
                            This export gives you a complete, structured, machine-readable copy
                            of all personal data FinTrack holds about you — in accordance with
                            Article 20 of the GDPR. CSVs are importable into Excel, Google Sheets,
                            or any accounting tool.
                        </p>
                    </div>
                </div>
                <div class="gdpr-right">
                    <div class="gdpr-right-icon" style="background:rgba(244,63,94,.1);">
                        <i class="fas fa-trash-can" style="color:var(--ft-rose);"></i>
                    </div>
                    <div class="gdpr-right-content">
                        <h4>Right to Erasure</h4>
                        <p>
                            You may permanently delete your account and all associated data at any
                            time. Navigate to <a href="{{ route('settings.index') }}"
                            class="text-decoration-none" style="color:var(--ft-teal);font-weight:500;">
                            Settings → Profile</a> and use the "Delete Account" option.
                            This action is irreversible.
                        </p>
                    </div>
                </div>
                <div class="gdpr-right">
                    <div class="gdpr-right-icon" style="background:rgba(139,92,246,.1);">
                        <i class="fas fa-lock" style="color:var(--ft-violet);"></i>
                    </div>
                    <div class="gdpr-right-content">
                        <h4>Data Isolation &amp; Security</h4>
                        <p>
                            Your export contains <strong>only your own data</strong>. We never
                            share financial data between users. The download link is generated
                            fresh on each request and is not stored on our servers.
                        </p>
                    </div>
                </div>
                <div class="gdpr-right">
                    <div class="gdpr-right-icon" style="background:rgba(14,116,144,.1);">
                        <i class="fas fa-clock" style="color:var(--ft-teal);"></i>
                    </div>
                    <div class="gdpr-right-content">
                        <h4>Export Frequency</h4>
                        <p>
                            To protect server performance, exports are rate-limited to
                            <strong>once every 5 minutes</strong>. Your export reflects the current
                            state of your data at the moment of download.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Download card --}}
    <div>
        <div class="download-card">
            {{-- Top gradient --}}
            <div class="download-card-top">
                <div class="download-card-zip">
                    <i class="fas fa-file-zipper" style="color:#fff;"></i>
                </div>
                <h3>fintrack-export.zip</h3>
                <p>{{ number_format($totalRecords) }} records across 17 CSV files</p>
            </div>

            {{-- Body --}}
            <div class="download-card-body">
                <form method="POST" action="{{ route('export.download') }}" id="exportForm">
                    @csrf
                    <button type="submit" class="btn-export" id="exportBtn">
                        <i class="fas fa-download"></i>
                        Download Full Export
                    </button>
                </form>

                <div style="margin-top: 18px;">
                    <div style="font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                        What's Included
                    </div>
                    <ul class="format-list">
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            README.txt with export guide and GDPR rights
                        </li>
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            17 individually branded CSV files
                        </li>
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            Metadata header in every file (account, date, currency)
                        </li>
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            All monetary values in your preferred currency ({{ Auth::user()->currency_code ?? 'USD' }})
                        </li>
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            Compatible with Excel, Google Sheets, LibreOffice
                        </li>
                        <li class="format-item">
                            <i class="fas fa-circle-check"></i>
                            Active records only (deleted records excluded)
                        </li>
                    </ul>
                </div>

                {{-- Account info --}}
                <div style="margin-top:18px;padding:14px;background:#f8fafc;border-radius:var(--r-sm);border:1px solid var(--border);">
                    <div style="font-size:11.5px;font-weight:600;color:var(--text-muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;">
                        Export Details
                    </div>
                    <div style="font-size:12.5px;color:var(--text-body);line-height:2;">
                        <div><span style="color:var(--text-muted);">Account:</span>&nbsp;
                            <strong>{{ Auth::user()->name }}</strong></div>
                        <div><span style="color:var(--text-muted);">Email:</span>&nbsp;
                            {{ Auth::user()->email }}</div>
                        @if(Auth::user()->business_name)
                        <div><span style="color:var(--text-muted);">Business:</span>&nbsp;
                            {{ Auth::user()->business_name }}</div>
                        @endif
                        <div><span style="color:var(--text-muted);">Currency:</span>&nbsp;
                            {{ Auth::user()->currency_code ?? 'USD' }}</div>
                        <div><span style="color:var(--text-muted);">Generated:</span>&nbsp;
                            <span id="exportTimestamp">{{ now()->setTimezone(Auth::user()->timezone ?? 'UTC')->format('d M Y, H:i T') }}</span>
                        </div>
                    </div>
                </div>

                {{-- Privacy note --}}
                <p style="margin-top:16px;font-size:11.5px;color:var(--text-faint);line-height:1.6;text-align:center;">
                    <i class="fas fa-lock me-1" style="color:var(--ft-teal);"></i>
                    Your export is generated fresh on demand and is never cached or stored on our servers.
                </p>
            </div>
        </div>
    </div>

</div>

@endsection

@section('scripts')
<script>
(function () {
    var form    = document.getElementById('exportForm');
    var btn     = document.getElementById('exportBtn');
    var loading = document.getElementById('exportLoading');

    if (!form) return;

    form.addEventListener('submit', function () {
        // Show loading overlay
        loading.classList.add('show');

        // Update button state
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Preparing Export…';

        // Hide loading after a generous timeout (download should have started)
        setTimeout(function () {
            loading.classList.remove('show');
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-download"></i> Download Full Export';
        }, 8000);
    });
})();
</script>
@endsection
