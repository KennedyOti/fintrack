@extends('layouts.free-docs')

@section('title', ucfirst($type) . ' Builder — Free Business Docs | FinTrack')

@section('content')

{{-- ── Toolbar ──────────────────────────────────────────────────── --}}
<div class="fd-toolbar">

    {{-- Action buttons — left-most for instant access --}}
    <div class="fd-action-group">
        <button id="btnShareLink" class="fd-btn fd-btn-secondary" type="button">
            <i class="fa-solid fa-link"></i> Get Link
        </button>
        <button id="btnDownloadPdf" class="fd-btn fd-btn-primary" type="button">
            <i class="fa-solid fa-download"></i> Download PDF
        </button>
    </div>

    <div class="fd-toolbar-sep"></div>

    {{-- Document type switcher --}}
    <div class="fd-type-tabs">
        <a href="{{ route('free-docs.builder', 'invoice') }}"
           class="fd-type-tab {{ $type === 'invoice'        ? 'active' : '' }}">
            <i class="fa-solid fa-file-invoice-dollar"></i> Invoice
        </a>
        <a href="{{ route('free-docs.builder', 'quote') }}"
           class="fd-type-tab {{ $type === 'quote'          ? 'active' : '' }}">
            <i class="fa-solid fa-file-lines"></i> Quote
        </a>
        <a href="{{ route('free-docs.builder', 'receipt') }}"
           class="fd-type-tab {{ $type === 'receipt'        ? 'active' : '' }}">
            <i class="fa-solid fa-receipt"></i> Receipt
        </a>
        <a href="{{ route('free-docs.builder', 'proforma') }}"
           class="fd-type-tab {{ $type === 'proforma'       ? 'active' : '' }}">
            <i class="fa-solid fa-file-circle-check"></i> Proforma
        </a>
        <a href="{{ route('free-docs.builder', 'purchase_order') }}"
           class="fd-type-tab {{ $type === 'purchase_order' ? 'active' : '' }}">
            <i class="fa-solid fa-cart-flatbed"></i> Purchase Order
        </a>
        <a href="{{ route('free-docs.builder', 'delivery_note') }}"
           class="fd-type-tab {{ $type === 'delivery_note'  ? 'active' : '' }}">
            <i class="fa-solid fa-truck"></i> Delivery Note
        </a>
    </div>

    <div class="fd-toolbar-sep"></div>

    {{-- Template --}}
    <div class="fd-ctrl">
        <label for="templateSelect">Template</label>
        <select id="templateSelect" class="fd-select">
            <option value="streamline">Streamline</option>
            <option value="classic">Classic</option>
            <option value="minimal">Minimal</option>
            <option value="bold">Bold</option>
        </select>
    </div>

    {{-- Font --}}
    <div class="fd-ctrl">
        <label for="fontSelect">Font</label>
        <select id="fontSelect" class="fd-select">
            <option value="helvetica">Helvetica / Arial</option>
            <option value="georgia">Georgia (Serif)</option>
            <option value="trebuchet">Trebuchet MS</option>
            <option value="verdana">Verdana</option>
            <option value="courier">Courier (Mono)</option>
        </select>
    </div>

    {{-- Currency --}}
    <div class="fd-ctrl">
        <label for="currencySelect">Currency</label>
        <select id="currencySelect" class="fd-select" style="min-width:148px;">
            {{-- Populated by JS --}}
        </select>
    </div>

    <div class="fd-toolbar-sep"></div>

    {{-- Colour pickers --}}
    <div class="fd-color-group">
        <div class="fd-color-wrap">
            <label>Primary</label>
            <input type="color" id="colorPrimary" class="fd-color-input" value="#0B2A4A">
        </div>
        <div class="fd-color-wrap">
            <label>Secondary</label>
            <input type="color" id="colorSecondary" class="fd-color-input" value="#334155">
        </div>
        <div class="fd-color-wrap">
            <label>Accent</label>
            <input type="color" id="colorAccent" class="fd-color-input" value="#22D3EE">
        </div>
    </div>


</div>

{{-- ── Mobile panel tabs ────────────────────────────────────────── --}}
<div class="fd-mobile-tabs">
    <div class="fd-mobile-tab active" data-target="form">
        <i class="fa-solid fa-pen-to-square"></i> Edit
    </div>
    <div class="fd-mobile-tab" data-target="preview">
        <i class="fa-solid fa-eye"></i> Preview
    </div>
</div>

{{-- ── Builder body (two panels) ───────────────────────────────── --}}
<div class="fd-body">

    {{-- ════════════════════════════════════════════════
         LEFT  — Form panel
         ════════════════════════════════════════════════ --}}
    <div class="fd-form-panel" id="formPanel">

        {{-- ── 1 · Your Business ── --}}
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon teal"><i class="fa-solid fa-building"></i></div>
                <span class="fd-section-title">Your Business</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">

                {{-- Logo upload drop area --}}
                <div class="fd-logo-drop" id="logoDrop">
                    <input type="file" id="logoInput" accept="image/png,image/jpeg,image/gif,image/svg+xml">
                    <img id="logoPreview" class="fd-logo-preview" src="" alt="" style="display:none;">
                    <div id="logoPlaceholder" class="fd-logo-placeholder">
                        <i class="fa-solid fa-image"></i>
                        Click or drag to upload your logo
                        <div style="font-size:10.5px;margin-top:4px;opacity:.6;">PNG, JPG, SVG · Max 2 MB</div>
                    </div>
                    <button id="logoRemove" type="button" class="fd-logo-remove" title="Remove logo">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                </div>

                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="fromName">Business / Trading Name</label>
                        <input id="fromName" class="fd-input" placeholder="Acme Ltd.">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="fromPerson">Your Name / Contact</label>
                        <input id="fromPerson" class="fd-input" placeholder="Jane Doe">
                    </div>
                </div>
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="fromEmail">Email</label>
                        <input id="fromEmail" class="fd-input" type="email" placeholder="you@business.com">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="fromPhone">Phone</label>
                        <input id="fromPhone" class="fd-input" type="tel" placeholder="+1 555 000 0000">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="fromAddress">Street Address</label>
                        <input id="fromAddress" class="fd-input" placeholder="123 Main Street, Suite 4">
                    </div>
                </div>
                <div class="fd-row cols-3">
                    <div class="fd-field">
                        <label class="fd-label" for="fromCity">City</label>
                        <input id="fromCity" class="fd-input" placeholder="Nairobi">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="fromState">State / Region</label>
                        <input id="fromState" class="fd-input" placeholder="Nairobi County">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="fromZip">Postal Code</label>
                        <input id="fromZip" class="fd-input" placeholder="00100">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="fromCountry">Country</label>
                        <input id="fromCountry" class="fd-input" placeholder="Kenya">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2 · Bill To / Vendor / Deliver To ── --}}
        @php
            $toLabel = match($type) {
                'quote'          => 'Prepared For',
                'purchase_order' => 'Vendor / Supplier',
                'delivery_note'  => 'Deliver To',
                default          => 'Bill To',
            };
            $toIcon = match($type) {
                'purchase_order' => 'fa-store',
                'delivery_note'  => 'fa-location-dot',
                default          => 'fa-user-tie',
            };
            $toCompanyLabel = $type === 'purchase_order' ? 'Vendor / Supplier Name' : 'Company / Organisation';
        @endphp
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon blue"><i class="fa-solid {{ $toIcon }}"></i></div>
                <span class="fd-section-title">{{ $toLabel }}</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="toCompany">{{ $toCompanyLabel }}</label>
                        <input id="toCompany" class="fd-input" placeholder="{{ $type === 'purchase_order' ? 'Supplier Ltd.' : 'Client Corp.' }}">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="toName">Contact Name</label>
                        <input id="toName" class="fd-input" placeholder="John Smith">
                    </div>
                </div>
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="toEmail">Email</label>
                        <input id="toEmail" class="fd-input" type="email" placeholder="client@company.com">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="toPhone">Phone</label>
                        <input id="toPhone" class="fd-input" type="tel" placeholder="+44 20 0000 0000">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="toAddress">Street Address</label>
                        <input id="toAddress" class="fd-input" placeholder="456 Client Ave">
                    </div>
                </div>
                <div class="fd-row cols-3">
                    <div class="fd-field">
                        <label class="fd-label" for="toCity">City</label>
                        <input id="toCity" class="fd-input" placeholder="London">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="toState">State / Region</label>
                        <input id="toState" class="fd-input" placeholder="Greater London">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="toZip">Postal Code</label>
                        <input id="toZip" class="fd-input" placeholder="EC1A 1BB">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="toCountry">Country</label>
                        <input id="toCountry" class="fd-input" placeholder="United Kingdom">
                    </div>
                </div>
            </div>
        </div>

        {{-- ── 2b · Ship To (purchase_order only) ── --}}
        @if($type === 'purchase_order')
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon green"><i class="fa-solid fa-truck-fast"></i></div>
                <span class="fd-section-title">Ship To <span class="fd-optional" style="font-weight:400;">(optional)</span></span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="shipCompany">Company / Organisation</label>
                        <input id="shipCompany" class="fd-input" placeholder="Receiving Corp.">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="shipName">Contact Name</label>
                        <input id="shipName" class="fd-input" placeholder="Warehouse Manager">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="shipAddress">Street Address</label>
                        <input id="shipAddress" class="fd-input" placeholder="Warehouse / Delivery Address">
                    </div>
                </div>
                <div class="fd-row cols-3">
                    <div class="fd-field">
                        <label class="fd-label" for="shipCity">City</label>
                        <input id="shipCity" class="fd-input" placeholder="Nairobi">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="shipState">State / Region</label>
                        <input id="shipState" class="fd-input" placeholder="Nairobi County">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="shipZip">Postal Code</label>
                        <input id="shipZip" class="fd-input" placeholder="00100">
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="shipCountry">Country</label>
                        <input id="shipCountry" class="fd-input" placeholder="Kenya">
                    </div>
                </div>
            </div>
        </div>
        @endif

        {{-- ── 3 · Document Details ── --}}
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon purple"><i class="fa-solid fa-circle-info"></i></div>
                <span class="fd-section-title">Document Details</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            @php
                $numLabel = match($type) {
                    'invoice'        => 'Invoice Number',
                    'quote'          => 'Quote Number',
                    'receipt'        => 'Receipt Number',
                    'proforma'       => 'Proforma Number',
                    'purchase_order' => 'PO Number',
                    'delivery_note'  => 'Delivery Note No.',
                    default          => 'Document Number',
                };
                $numPrefix = match($type) {
                    'invoice'        => 'INV-',
                    'quote'          => 'QT-',
                    'receipt'        => 'RCP-',
                    'proforma'       => 'PRO-',
                    'purchase_order' => 'PO-',
                    'delivery_note'  => 'DN-',
                    default          => 'DOC-',
                };
                $dueDateLabel = match($type) {
                    'quote', 'proforma' => 'Valid Until',
                    'receipt'           => 'Receipt Date',
                    'purchase_order'    => 'Expected Delivery',
                    'delivery_note'     => 'Delivery Date',
                    default             => 'Due Date',
                };
                $poLabel = $type === 'delivery_note' ? 'Order Reference' : 'PO Number';
            @endphp
            <div class="fd-section-body">
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="docNumber">{{ $numLabel }}</label>
                        <input id="docNumber" class="fd-input" value="{{ $numPrefix }}{{ date('Y') }}-001">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="docDate">Issue Date</label>
                        <input id="docDate" class="fd-input" type="date" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="docDueDate">{{ $dueDateLabel }}</label>
                        <input id="docDueDate" class="fd-input" type="date"
                               value="{{ date('Y-m-d', strtotime('+14 days')) }}">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="docPo">{{ $poLabel }} <span class="fd-optional">(optional)</span></label>
                        <input id="docPo" class="fd-input" placeholder="{{ $type === 'delivery_note' ? 'REF-12345' : 'PO-12345' }}">
                    </div>
                </div>
                @if($type === 'delivery_note')
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="docCarrier">Carrier / Courier <span class="fd-optional">(optional)</span></label>
                        <input id="docCarrier" class="fd-input" placeholder="DHL, FedEx, Own Fleet…">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="docTracking">Tracking Number <span class="fd-optional">(optional)</span></label>
                        <input id="docTracking" class="fd-input" placeholder="1Z9999999999999999">
                    </div>
                </div>
                @endif
                @if($type === 'purchase_order')
                <div class="fd-row cols-2">
                    <div class="fd-field">
                        <label class="fd-label" for="docRef">Reference / Project <span class="fd-optional">(optional)</span></label>
                        <input id="docRef" class="fd-input" placeholder="Office Supplies Q2">
                    </div>
                    <div class="fd-field">
                        <label class="fd-label" for="docAuthorizedBy">Authorized By <span class="fd-optional">(optional)</span></label>
                        <input id="docAuthorizedBy" class="fd-input" placeholder="Jane Doe, Director">
                    </div>
                </div>
                @else
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="docRef">Reference / Project <span class="fd-optional">(optional)</span></label>
                        <input id="docRef" class="fd-input" placeholder="Website Redesign Project">
                    </div>
                </div>
                @endif
            </div>
        </div>

        {{-- ── 4 · Line Items ── --}}
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon green"><i class="fa-solid fa-list-check"></i></div>
                <span class="fd-section-title">Line Items</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">
                <table class="fd-items-table">
                    <thead>
                        @if($type === 'delivery_note')
                        <tr>
                            <th style="width:40%;">Description</th>
                            <th style="width:16%;text-align:right;">Qty Ordered</th>
                            <th style="width:16%;text-align:right;">Qty Delivered</th>
                            <th style="width:20%;text-align:center;">Unit</th>
                            <th style="width:8%;"></th>
                        </tr>
                        @else
                        <tr>
                            <th style="width:44%;">Description</th>
                            <th style="width:14%;text-align:right;">Qty</th>
                            <th style="width:18%;text-align:right;">Rate</th>
                            <th style="width:16%;text-align:right;">Amount</th>
                            <th style="width:8%;"></th>
                        </tr>
                        @endif
                    </thead>
                    <tbody id="itemsBody">
                        {{-- Rendered by JS --}}
                    </tbody>
                </table>
                <button id="addItemBtn" class="fd-add-item-btn" type="button">
                    <i class="fa-solid fa-plus"></i> Add Line Item
                </button>
            </div>
        </div>

        {{-- ── 5 · Totals & Adjustments (hidden for delivery note) ── --}}
        @if($type !== 'delivery_note')
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon amber"><i class="fa-solid fa-percent"></i></div>
                <span class="fd-section-title">Totals &amp; Adjustments</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">

                {{-- Tax --}}
                <div class="fd-toggle-row">
                    <label class="fd-toggle"><input type="checkbox" id="toggleTax"><span class="fd-toggle-slider"></span></label>
                    <span class="fd-toggle-label">Add Tax / VAT / GST</span>
                </div>
                <div id="taxFields" class="fd-toggle-fields" style="display:none;">
                    <div class="fd-row cols-2">
                        <div class="fd-field">
                            <label class="fd-label">Tax Label</label>
                            <input id="taxLabel" class="fd-input" placeholder="VAT" value="VAT">
                        </div>
                        <div class="fd-field">
                            <label class="fd-label">Rate (%)</label>
                            <input id="taxRate" class="fd-input" type="number" min="0" max="100" step="0.01" placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Discount --}}
                <div class="fd-toggle-row">
                    <label class="fd-toggle"><input type="checkbox" id="toggleDiscount"><span class="fd-toggle-slider"></span></label>
                    <span class="fd-toggle-label">Add Discount</span>
                </div>
                <div id="discountFields" class="fd-toggle-fields" style="display:none;">
                    <div class="fd-row cols-2">
                        <div class="fd-field">
                            <label class="fd-label">Type</label>
                            <select id="discountType" class="fd-select" style="width:100%;min-width:unset;">
                                <option value="percentage">Percentage (%)</option>
                                <option value="fixed">Fixed Amount</option>
                            </select>
                        </div>
                        <div class="fd-field">
                            <label class="fd-label">Value</label>
                            <input id="discountValue" class="fd-input" type="number" min="0" step="0.01" placeholder="0">
                        </div>
                    </div>
                </div>

                {{-- Shipping --}}
                <div class="fd-toggle-row">
                    <label class="fd-toggle"><input type="checkbox" id="toggleShipping"><span class="fd-toggle-slider"></span></label>
                    <span class="fd-toggle-label">Add Shipping / Delivery</span>
                </div>
                <div id="shippingFields" class="fd-toggle-fields" style="display:none;">
                    <div class="fd-row cols-1">
                        <div class="fd-field">
                            <label class="fd-label">Shipping Amount</label>
                            <input id="shippingAmount" class="fd-input" type="number" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>

                @if($type === 'invoice')
                {{-- Amount Paid / Balance Due (invoices) --}}
                <div class="fd-toggle-row">
                    <label class="fd-toggle"><input type="checkbox" id="togglePaid"><span class="fd-toggle-slider"></span></label>
                    <span class="fd-toggle-label">Show Amount Paid &amp; Balance Due</span>
                </div>
                <div id="paidFields" class="fd-toggle-fields" style="display:none;">
                    <div class="fd-row cols-1">
                        <div class="fd-field">
                            <label class="fd-label">Amount Paid</label>
                            <input id="paidAmount" class="fd-input" type="number" min="0" step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>
                @endif

                {{-- Live totals summary --}}
                <div class="fd-totals">
                    <div class="fd-total-row">
                        <span class="label">Subtotal</span>
                        <span id="summSubtotal">—</span>
                    </div>
                    <div class="fd-total-row" id="summDiscountRow" style="display:none;">
                        <span class="label" id="summDiscountLabel">Discount</span>
                        <span id="summDiscount" style="color:#f43f5e;"></span>
                    </div>
                    <div class="fd-total-row" id="summTaxRow" style="display:none;">
                        <span class="label" id="summTaxLabel">Tax</span>
                        <span id="summTax"></span>
                    </div>
                    <div class="fd-total-row" id="summShippingRow" style="display:none;">
                        <span class="label">Shipping</span>
                        <span id="summShipping"></span>
                    </div>
                    <div class="fd-total-row grand">
                        <span class="label">Total</span>
                        <span class="value" id="summTotal">—</span>
                    </div>
                    <div class="fd-total-row paid" id="summPaidRow" style="display:none;">
                        <span class="label">Amount Paid</span>
                        <span id="summPaid"></span>
                    </div>
                    <div class="fd-total-row due" id="summDueRow" style="display:none;">
                        <span class="label">Balance Due</span>
                        <span id="summDue"></span>
                    </div>
                </div>

            </div>
        </div>

        @endif {{-- /delivery_note totals guard --}}

        {{-- ── 6 · Notes, Terms & Payment ── --}}
        <div class="fd-section">
            <div class="fd-section-head">
                <div class="fd-section-icon rose"><i class="fa-solid fa-note-sticky"></i></div>
                <span class="fd-section-title">Notes, Terms &amp; Payment Info</span>
                <i class="fa-solid fa-chevron-down fd-section-toggle"></i>
            </div>
            <div class="fd-section-body">
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="docNotes">Notes / Message to Client</label>
                        <textarea id="docNotes" class="fd-textarea"
                            placeholder="Thank you for your business! Please reach out if you have any questions."></textarea>
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        <label class="fd-label" for="docTerms">Terms &amp; Conditions</label>
                        <textarea id="docTerms" class="fd-textarea"
                            placeholder="Payment due within 14 days. Late payments may incur a 2% monthly interest charge."></textarea>
                    </div>
                </div>
                <div class="fd-row cols-1">
                    <div class="fd-field">
                        @if($type === 'delivery_note')
                        <label class="fd-label" for="docPaymentInfo">Delivery Instructions</label>
                        <textarea id="docPaymentInfo" class="fd-textarea"
                            placeholder="Leave at reception. Handle with care. Keep dry."></textarea>
                        @elseif($type === 'purchase_order')
                        <label class="fd-label" for="docPaymentInfo">Payment Terms</label>
                        <textarea id="docPaymentInfo" class="fd-textarea"
                            placeholder="Net 30 days from invoice date. Payment via bank transfer."></textarea>
                        @else
                        <label class="fd-label" for="docPaymentInfo">Payment Instructions / Bank Details</label>
                        <textarea id="docPaymentInfo" class="fd-textarea"
                            placeholder="Bank: First National Bank&#10;Account Name: Acme Ltd&#10;Account No: 1234567890"></textarea>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>{{-- /fd-form-panel --}}


    {{-- ════════════════════════════════════════════════
         RIGHT — Live preview panel
         ════════════════════════════════════════════════ --}}
    <div class="fd-preview-panel" id="previewPanel">
        <div class="fd-preview-header">
            <span class="fd-preview-label">
                <i class="fa-solid fa-eye" style="color:#22d3ee;margin-right:5px;"></i>
                Live Preview
            </span>
            <span class="fd-preview-hint">A4 · PDF-accurate</span>
        </div>
        <div class="fd-preview-scale-wrap">
            <div id="previewDoc">
                {{-- JS renders the document here in real-time --}}
            </div>
        </div>
    </div>

</div>{{-- /fd-body --}}

{{-- ── Sticky Bottom Action Bar (visible on tablet/laptop, hidden on desktop) ── --}}
<div class="fd-sticky-actions" id="fdStickyActions">
    <button id="btnShareLink2" class="fd-btn fd-btn-secondary fd-sticky-btn" type="button">
        <i class="fa-solid fa-link"></i> Get Link
    </button>
    <button id="btnDownloadPdf2" class="fd-btn fd-btn-primary fd-sticky-btn" type="button">
        <i class="fa-solid fa-download"></i> Download PDF
    </button>
</div>

{{-- ── Share Link Modal ─────────────────────────────────────────── --}}
<div class="fd-modal-overlay" id="shareModal" onclick="if(event.target===this)closeModal()">
    <div class="fd-modal">
        <div class="fd-modal-title">
            <i class="fa-solid fa-link" style="color:#22d3ee;margin-right:8px;"></i>
            Shareable Link Generated
        </div>
        <div class="fd-modal-sub">
            Your document has been saved. Share this link with your client — it stays active for 60 days.
            They can view it online or download the PDF themselves.
        </div>
        <div class="fd-link-box">
            <input type="text" id="shareLinkInput" class="fd-link-input" readonly placeholder="Generating link…">
            <button id="copyLinkBtn" class="fd-copy-btn" type="button" onclick="copyLink()">Copy</button>
        </div>
        <p style="font-size:11.5px;color:#5c6470;margin-bottom:20px;">
            <i class="fa-solid fa-shield-halved" style="color:#22d3ee;margin-right:4px;"></i>
            No account needed. The link works for anyone — no sign-in required.
        </p>
        <div class="fd-modal-footer">
            <button class="fd-modal-close" type="button" onclick="closeModal()">Close</button>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
  window.FD_PDF_URL  = '{{ route('free-docs.generate-pdf') }}';
  window.FD_SAVE_URL = '{{ route('free-docs.save') }}';
  window.FD_CSRF     = '{{ csrf_token() }}';
  window.FD_TYPE     = '{{ $type }}';
</script>
<script src="{{ asset('assets/js/free-docs.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    initBuilder(window.FD_TYPE);

    // Show/hide logo remove button based on whether logo is set
    const logoInput  = document.getElementById('logoInput');
    const logoRemove = document.getElementById('logoRemove');
    if (logoInput && logoRemove) {
        logoInput.addEventListener('change', () => {
            logoRemove.style.display = logoInput.files.length ? 'flex' : 'none';
        });
        logoRemove.addEventListener('click', e => {
            e.stopPropagation();
            logoRemove.style.display = 'none';
        });
    }

    // Forward sticky bar button clicks to the original toolbar buttons
    const stickyShare   = document.getElementById('btnShareLink2');
    const stickyDownload = document.getElementById('btnDownloadPdf2');
    if (stickyShare)    stickyShare.addEventListener('click', () => document.getElementById('btnShareLink').click());
    if (stickyDownload) stickyDownload.addEventListener('click', () => document.getElementById('btnDownloadPdf').click());
});
</script>
@endpush
