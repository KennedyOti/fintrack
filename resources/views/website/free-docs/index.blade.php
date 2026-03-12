@extends('layouts.app')

@section('title', 'Free Business Docs — Invoice, Quote, PO & More | FinTrack')
@section('meta-description', 'Create professional invoices, quotations, receipts, proforma invoices, purchase orders and delivery notes online for free. No signup required. Download as PDF or share with a link.')

@push('styles')
<link rel="stylesheet" href="{{ asset('assets/css/free-docs.css') }}">
@endpush

@section('content')

    {{-- ── Hero section ───────────────────────────────────────────── --}}
    <section class="fd-hero">
        <div class="container">
            <div class="fd-hero-badge">
                <i class="fa-solid fa-bolt"></i>
                100% Free &nbsp;&bull;&nbsp; No Sign-up Required &nbsp;&bull;&nbsp; PDF Download
            </div>

            <h1>
                Professional Business Documents<br>
                <span>In Minutes. For Free.</span>
            </h1>

            <p>
                Create stunning invoices, quotations, receipts, proforma invoices, purchase orders
                and delivery notes with full customisation — choose your template, colours and font,
                upload your logo, add line items and download a pixel-perfect PDF or share a secure link.
            </p>

            {{-- ── Document type cards ──────────────────────────────── --}}
            <div class="fd-doc-cards">

                {{-- Invoice --}}
                <a href="{{ route('free-docs.builder', 'invoice') }}" class="fd-doc-card"
                   style="--accent-col: #22D3EE;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(34,211,238,.12); color:#22D3EE;">
                        <i class="fa-solid fa-file-invoice-dollar"></i>
                    </div>
                    <h3>Invoice</h3>
                    <p>Bill clients for goods or services. Track amounts due and payment terms. Accepted everywhere.</p>
                    <span class="fd-doc-card-btn">
                        Create Invoice <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                {{-- Quotation --}}
                <a href="{{ route('free-docs.builder', 'quote') }}" class="fd-doc-card"
                   style="--accent-col: #22C55E;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(34,197,94,.12); color:#22C55E;">
                        <i class="fa-solid fa-file-lines"></i>
                    </div>
                    <h3>Quotation</h3>
                    <p>Send price estimates to prospective clients before work begins. Set expiry dates and terms.</p>
                    <span class="fd-doc-card-btn" style="border-color:#22C55E; color:#22C55E;">
                        Create Quote <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                {{-- Receipt --}}
                <a href="{{ route('free-docs.builder', 'receipt') }}" class="fd-doc-card"
                   style="--accent-col: #F59E0B;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(245,158,11,.12); color:#F59E0B;">
                        <i class="fa-solid fa-receipt"></i>
                    </div>
                    <h3>Sales Receipt</h3>
                    <p>Provide payment confirmation to customers after a completed sale. Simple and professional.</p>
                    <span class="fd-doc-card-btn" style="border-color:#F59E0B; color:#F59E0B;">
                        Create Receipt <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                {{-- Proforma Invoice --}}
                <a href="{{ route('free-docs.builder', 'proforma') }}" class="fd-doc-card"
                   style="--accent-col: #8B5CF6;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(139,92,246,.12); color:#8B5CF6;">
                        <i class="fa-solid fa-file-circle-check"></i>
                    </div>
                    <h3>Proforma Invoice</h3>
                    <p>Send a preliminary invoice before final billing. Ideal for customs, budgeting and advance approvals.</p>
                    <span class="fd-doc-card-btn" style="border-color:#8B5CF6; color:#8B5CF6;">
                        Create Proforma <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                {{-- Purchase Order --}}
                <a href="{{ route('free-docs.builder', 'purchase_order') }}" class="fd-doc-card"
                   style="--accent-col: #F43F5E;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(244,63,94,.12); color:#F43F5E;">
                        <i class="fa-solid fa-cart-flatbed"></i>
                    </div>
                    <h3>Purchase Order</h3>
                    <p>Issue official purchase orders to suppliers. Specify items, quantities, pricing and expected delivery dates.</p>
                    <span class="fd-doc-card-btn" style="border-color:#F43F5E; color:#F43F5E;">
                        Create PO <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

                {{-- Delivery Note --}}
                <a href="{{ route('free-docs.builder', 'delivery_note') }}" class="fd-doc-card"
                   style="--accent-col: #0E7490;">
                    <div class="fd-doc-card-icon"
                         style="background:rgba(14,116,144,.12); color:#0E7490;">
                        <i class="fa-solid fa-truck"></i>
                    </div>
                    <h3>Delivery Note</h3>
                    <p>Accompany shipments with a professional delivery note. Track quantities ordered vs. delivered by item.</p>
                    <span class="fd-doc-card-btn" style="border-color:#0E7490; color:#0E7490;">
                        Create Delivery Note <i class="fa-solid fa-arrow-right"></i>
                    </span>
                </a>

            </div>
        </div>
    </section>

    {{-- ── Features strip ─────────────────────────────────────────── --}}
    <section class="fd-features">
        <div class="container">
            <div class="text-center mb-5">
                <h2 style="font-family:'Space Grotesk',sans-serif; font-size:1.9rem; font-weight:800; color:#e2eaf7; margin-bottom:10px;">
                    Everything you need to look professional
                </h2>
                <p style="color:#8fa5c3; font-size:1rem; max-width:560px; margin:0 auto; line-height:1.7;">
                    Our free builder rivals paid tools — more customisation, zero friction, zero cost.
                </p>
            </div>

            <div class="fd-features-grid">
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-palette"></i></div>
                    <h4>4 Professional Templates</h4>
                    <p>Streamline, Classic, Minimal, Bold — all designed to global standards.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-paintbrush"></i></div>
                    <h4>Full Customisation</h4>
                    <p>Change colours, fonts, upload your logo and make every document on-brand.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-globe"></i></div>
                    <h4>48 Currencies</h4>
                    <p>Bill in USD, EUR, GBP, KES, NGN, AED and 44 more currencies worldwide.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-eye"></i></div>
                    <h4>Live Preview</h4>
                    <p>See exactly how your document will look as you type — no surprises at all.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-file-pdf"></i></div>
                    <h4>PDF Download</h4>
                    <p>Download a crisp, print-ready A4 PDF with one click. Free and unlimited.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-link"></i></div>
                    <h4>Shareable Links</h4>
                    <p>Generate a secure 60-day link to share your document with any client digitally.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-calculator"></i></div>
                    <h4>Tax &amp; Discounts</h4>
                    <p>Add VAT/GST, percentage or fixed discounts, and shipping charges automatically.</p>
                </div>
                <div class="fd-feat">
                    <div class="fd-feat-icon"><i class="fa-solid fa-lock"></i></div>
                    <h4>No Account Needed</h4>
                    <p>Build and download without registering. Your data stays in your browser session.</p>
                </div>
            </div>
        </div>
    </section>

    {{-- ── CTA section ─────────────────────────────────────────────── --}}
    <section style="padding:80px 0; text-align:center; background:#070b15;">
        <div class="container">
            <h2 style="font-family:'Space Grotesk',sans-serif; font-size:2rem; font-weight:800; color:#e2eaf7; margin-bottom:14px;">
                Ready to send your first invoice?
            </h2>
            <p style="color:#8fa5c3; margin-bottom:36px; font-size:1rem; line-height:1.7;">
                Pick a document type and get started in seconds. No account. No credit card. Ever.
            </p>

            <div style="display:flex; gap:12px; justify-content:center; flex-wrap:wrap;">
                <a href="{{ route('free-docs.builder', 'invoice') }}"
                   class="fd-btn fd-btn-primary"
                   style="font-size:14px; padding:12px 28px;">
                    <i class="fa-solid fa-file-invoice-dollar"></i> Create Invoice
                </a>
                <a href="{{ route('free-docs.builder', 'proforma') }}"
                   class="fd-btn fd-btn-outline"
                   style="font-size:14px; padding:12px 28px;">
                    <i class="fa-solid fa-file-circle-check"></i> Proforma Invoice
                </a>
                <a href="{{ route('free-docs.builder', 'purchase_order') }}"
                   class="fd-btn fd-btn-outline"
                   style="font-size:14px; padding:12px 28px;">
                    <i class="fa-solid fa-cart-flatbed"></i> Purchase Order
                </a>
                <a href="{{ route('free-docs.builder', 'delivery_note') }}"
                   class="fd-btn fd-btn-outline"
                   style="font-size:14px; padding:12px 28px;">
                    <i class="fa-solid fa-truck"></i> Delivery Note
                </a>
            </div>

            <div style="margin-top:52px; padding-top:40px; border-top:1px solid #1e2a3d;">
                <p style="color:#8fa5c3; font-size:13px; margin-bottom:10px;">
                    Want more? FinTrack gives you a full financial dashboard — recurring invoices,
                    client management, expense tracking, savings goals and detailed reports.
                </p>
                <a href="{{ route('register') }}"
                   style="color:#22d3ee; font-size:13.5px; font-weight:700;">
                    Sign up free — it only takes a minute
                    <i class="fa-solid fa-arrow-right" style="margin-left:4px;"></i>
                </a>
            </div>
        </div>
    </section>

@endsection
