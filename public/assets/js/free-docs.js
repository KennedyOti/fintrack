/* ============================================================
   Free Business Docs — Builder JS
   Real-time preview, line items, PDF/share actions
   ============================================================ */

'use strict';

/* ── Currencies ─────────────────────────────────────────────── */
const CURRENCIES = [
  { code:'USD', name:'US Dollar',            symbol:'$',    pos:'before' },
  { code:'EUR', name:'Euro',                 symbol:'€',    pos:'before' },
  { code:'GBP', name:'British Pound',        symbol:'£',    pos:'before' },
  { code:'JPY', name:'Japanese Yen',         symbol:'¥',    pos:'before' },
  { code:'CAD', name:'Canadian Dollar',      symbol:'CA$',  pos:'before' },
  { code:'AUD', name:'Australian Dollar',    symbol:'A$',   pos:'before' },
  { code:'CHF', name:'Swiss Franc',          symbol:'CHF ', pos:'before' },
  { code:'CNY', name:'Chinese Yuan',         symbol:'¥',    pos:'before' },
  { code:'INR', name:'Indian Rupee',         symbol:'₹',    pos:'before' },
  { code:'KES', name:'Kenyan Shilling',      symbol:'KSh ', pos:'before' },
  { code:'NGN', name:'Nigerian Naira',       symbol:'₦',    pos:'before' },
  { code:'ZAR', name:'South African Rand',   symbol:'R ',   pos:'before' },
  { code:'GHS', name:'Ghanaian Cedi',        symbol:'GH₵',  pos:'before' },
  { code:'UGX', name:'Ugandan Shilling',     symbol:'USh ', pos:'before' },
  { code:'TZS', name:'Tanzanian Shilling',   symbol:'TSh ', pos:'before' },
  { code:'EGP', name:'Egyptian Pound',       symbol:'E£',   pos:'before' },
  { code:'MAD', name:'Moroccan Dirham',      symbol:' MAD', pos:'after'  },
  { code:'AED', name:'UAE Dirham',           symbol:'AED ', pos:'before' },
  { code:'SAR', name:'Saudi Riyal',          symbol:'﷼',    pos:'before' },
  { code:'QAR', name:'Qatari Riyal',         symbol:'QR ',  pos:'before' },
  { code:'BRL', name:'Brazilian Real',       symbol:'R$',   pos:'before' },
  { code:'MXN', name:'Mexican Peso',         symbol:'MX$',  pos:'before' },
  { code:'ARS', name:'Argentine Peso',       symbol:'$',    pos:'before' },
  { code:'COP', name:'Colombian Peso',       symbol:'$',    pos:'before' },
  { code:'CLP', name:'Chilean Peso',         symbol:'$',    pos:'before' },
  { code:'SEK', name:'Swedish Krona',        symbol:' kr',  pos:'after'  },
  { code:'NOK', name:'Norwegian Krone',      symbol:' kr',  pos:'after'  },
  { code:'DKK', name:'Danish Krone',         symbol:' kr',  pos:'after'  },
  { code:'PLN', name:'Polish Zloty',         symbol:' zł',  pos:'after'  },
  { code:'HUF', name:'Hungarian Forint',     symbol:' Ft',  pos:'after'  },
  { code:'CZK', name:'Czech Koruna',         symbol:' Kč',  pos:'after'  },
  { code:'PHP', name:'Philippine Peso',      symbol:'₱',    pos:'before' },
  { code:'IDR', name:'Indonesian Rupiah',    symbol:'Rp ',  pos:'before' },
  { code:'MYR', name:'Malaysian Ringgit',    symbol:'RM ',  pos:'before' },
  { code:'SGD', name:'Singapore Dollar',     symbol:'S$',   pos:'before' },
  { code:'THB', name:'Thai Baht',            symbol:'฿',    pos:'before' },
  { code:'PKR', name:'Pakistani Rupee',      symbol:'₨',    pos:'before' },
  { code:'BDT', name:'Bangladeshi Taka',     symbol:'৳',    pos:'before' },
  { code:'NZD', name:'New Zealand Dollar',   symbol:'NZ$',  pos:'before' },
  { code:'HKD', name:'Hong Kong Dollar',     symbol:'HK$',  pos:'before' },
  { code:'KWD', name:'Kuwaiti Dinar',        symbol:'KD ',  pos:'before' },
  { code:'BHD', name:'Bahraini Dinar',       symbol:'BD ',  pos:'before' },
  { code:'OMR', name:'Omani Rial',           symbol:'OMR ', pos:'before' },
  { code:'JOD', name:'Jordanian Dinar',      symbol:'JD ',  pos:'before' },
  { code:'ILS', name:'Israeli Shekel',       symbol:'₪',    pos:'before' },
  { code:'TRY', name:'Turkish Lira',         symbol:'₺',    pos:'before' },
  { code:'RUB', name:'Russian Ruble',        symbol:'₽',    pos:'before' },
  { code:'UAH', name:'Ukrainian Hryvnia',    symbol:'₴',    pos:'before' },
];

/* ── Default state ──────────────────────────────────────────── */
function defaultState(type) {
  const today = new Date();
  const due   = new Date(today); due.setDate(due.getDate() + 14);
  const fmt   = d => d.toISOString().slice(0, 10);

  const prefixes = { invoice:'INV', quote:'QT', receipt:'RCP', proforma:'PRO', purchase_order:'PO', delivery_note:'DN' };
  const num = `${prefixes[type] || 'DOC'}-${today.getFullYear()}-001`;

  return {
    type,
    template:  'streamline',
    font:      'helvetica',
    colors:    { primary: '#0B2A4A', secondary: '#334155', accent: '#22D3EE' },
    currency:  CURRENCIES[0],
    from: { name:'', email:'', phone:'', address:'', city:'', state:'', zip:'', country:'', logo:'' },
    to:   { company:'', name:'', email:'', phone:'', address:'', city:'', state:'', zip:'', country:'' },
    ship_to: { company:'', name:'', email:'', phone:'', address:'', city:'', state:'', zip:'', country:'' },
    authorized_by: '',
    details: {
      number:     num,
      date:       fmt(today),
      due_date:   fmt(due),
      po_number:  '',
      reference:  '',
      carrier:    '',
      tracking:   '',
    },
    items: [
      { description: type === 'delivery_note' ? 'Item Description' : 'Professional Services',
        qty:1, rate:0, amount:0, delivered_qty:1, unit:'pcs' }
    ],
    tax:      { enabled:false, label:'Tax',      rate:0 },
    discount: { enabled:false, type:'percentage', value:0 },
    shipping: { enabled:false, amount:0 },
    paid:     { enabled: type === 'receipt', amount:0 },
    notes:    '',
    terms:    '',
    payment_info: '',
  };
}

/* ── App ────────────────────────────────────────────────────── */
let state;

function initBuilder(docType) {
  state = defaultState(docType);
  populateCurrencySelect();
  bindFormEvents();
  bindToolbarEvents();
  bindItemEvents();
  updatePreview();
  initMobileTabs();
}

/* ── Populate currency dropdown ─────────────────────────────── */
function populateCurrencySelect() {
  const sel = document.getElementById('currencySelect');
  if (!sel) return;
  CURRENCIES.forEach(c => {
    const opt = document.createElement('option');
    opt.value   = c.code;
    opt.textContent = `${c.code} — ${c.name}`;
    if (c.code === 'USD') opt.selected = true;
    sel.appendChild(opt);
  });
}

/* ── Toolbar events ─────────────────────────────────────────── */
function bindToolbarEvents() {
  // Template
  on('templateSelect', 'change', e => { state.template = e.target.value; updatePreview(); });
  // Font
  on('fontSelect', 'change', e => { state.font = e.target.value; updatePreview(); });
  // Currency
  on('currencySelect', 'change', e => {
    state.currency = CURRENCIES.find(c => c.code === e.target.value) || CURRENCIES[0];
    updatePreview();
    recalcItems();
  });
  // Colors
  on('colorPrimary',   'input', e => { state.colors.primary   = e.target.value; updatePreview(); });
  on('colorSecondary', 'input', e => { state.colors.secondary = e.target.value; updatePreview(); });
  on('colorAccent',    'input', e => { state.colors.accent    = e.target.value; updatePreview(); });
  // Download PDF
  on('btnDownloadPdf', 'click', downloadPdf);
  // Get share link
  on('btnShareLink', 'click', getShareLink);
}

/* ── Form field bindings ────────────────────────────────────── */
function bindFormEvents() {
  // From
  bindField('fromName',     v => state.from.name    = v);
  bindField('fromPerson',   v => state.from.person  = v);
  bindField('fromEmail',    v => state.from.email   = v);
  bindField('fromPhone',    v => state.from.phone   = v);
  bindField('fromAddress',  v => state.from.address = v);
  bindField('fromCity',     v => state.from.city    = v);
  bindField('fromState',    v => state.from.state   = v);
  bindField('fromZip',      v => state.from.zip     = v);
  bindField('fromCountry',  v => state.from.country = v);
  // To
  bindField('toCompany',  v => state.to.company = v);
  bindField('toName',     v => state.to.name    = v);
  bindField('toEmail',    v => state.to.email   = v);
  bindField('toPhone',    v => state.to.phone   = v);
  bindField('toAddress',  v => state.to.address = v);
  bindField('toCity',     v => state.to.city    = v);
  bindField('toState',    v => state.to.state   = v);
  bindField('toZip',      v => state.to.zip     = v);
  bindField('toCountry',  v => state.to.country = v);
  // Details
  bindField('docNumber',   v => state.details.number    = v);
  bindField('docDate',     v => state.details.date      = v);
  bindField('docDueDate',  v => state.details.due_date  = v);
  bindField('docPo',       v => state.details.po_number = v);
  bindField('docRef',      v => state.details.reference = v);
  // Ship To (purchase_order)
  bindField('shipCompany', v => state.ship_to.company = v);
  bindField('shipName',    v => state.ship_to.name    = v);
  bindField('shipEmail',   v => state.ship_to.email   = v);
  bindField('shipPhone',   v => state.ship_to.phone   = v);
  bindField('shipAddress', v => state.ship_to.address = v);
  bindField('shipCity',    v => state.ship_to.city    = v);
  bindField('shipState',   v => state.ship_to.state   = v);
  bindField('shipZip',     v => state.ship_to.zip     = v);
  bindField('shipCountry', v => state.ship_to.country = v);
  // Authorized By (purchase_order)
  bindField('docAuthorizedBy', v => { state.authorized_by = v; updatePreview(); });
  // Carrier / Tracking (delivery_note)
  bindField('docCarrier',  v => { state.details.carrier  = v; updatePreview(); });
  bindField('docTracking', v => { state.details.tracking = v; updatePreview(); });
  // Additional
  bindField('docNotes',       v => state.notes        = v);
  bindField('docTerms',       v => state.terms        = v);
  bindField('docPaymentInfo', v => state.payment_info = v);

  // Tax toggle
  bindToggle('toggleTax', 'taxFields', v => state.tax.enabled = v);
  bindField('taxLabel', v => { state.tax.label = v; updatePreview(); });
  bindField('taxRate',  v => { state.tax.rate  = parseFloat(v)||0; recalcItems(); });

  // Discount toggle
  bindToggle('toggleDiscount', 'discountFields', v => state.discount.enabled = v);
  bindField('discountType',  v => { state.discount.type  = v; recalcItems(); }, 'change');
  bindField('discountValue', v => { state.discount.value = parseFloat(v)||0; recalcItems(); });

  // Shipping toggle
  bindToggle('toggleShipping', 'shippingFields', v => state.shipping.enabled = v);
  bindField('shippingAmount', v => { state.shipping.amount = parseFloat(v)||0; recalcItems(); });

  // Paid toggle (receipt)
  const togglePaid = document.getElementById('togglePaid');
  if (togglePaid) {
    bindToggle('togglePaid', 'paidFields', v => { state.paid.enabled = v; recalcItems(); });
    bindField('paidAmount', v => { state.paid.amount = parseFloat(v)||0; recalcItems(); });
  }

  // Logo upload
  const logoInput = document.getElementById('logoInput');
  if (logoInput) {
    logoInput.addEventListener('change', e => {
      const file = e.target.files[0];
      if (!file) return;
      if (file.size > 2 * 1024 * 1024) { showToast('Logo must be under 2 MB', 'error'); return; }
      const reader = new FileReader();
      reader.onload = ev => {
        state.from.logo = ev.target.result;
        const prev = document.getElementById('logoPreview');
        const placeholder = document.getElementById('logoPlaceholder');
        if (prev) { prev.src = ev.target.result; prev.style.display = 'block'; }
        if (placeholder) placeholder.style.display = 'none';
        updatePreview();
      };
      reader.readAsDataURL(file);
    });
  }

  // Remove logo
  on('logoRemove', 'click', e => {
    e.stopPropagation();
    state.from.logo = '';
    const prev = document.getElementById('logoPreview');
    const placeholder = document.getElementById('logoPlaceholder');
    const input = document.getElementById('logoInput');
    if (prev) { prev.src=''; prev.style.display='none'; }
    if (placeholder) placeholder.style.display = '';
    if (input) input.value = '';
    updatePreview();
  });

  // Section collapse
  document.querySelectorAll('.fd-section-head').forEach(head => {
    head.addEventListener('click', () => {
      const body   = head.nextElementSibling;
      const toggle = head.querySelector('.fd-section-toggle');
      if (!body) return;
      body.classList.toggle('collapsed');
      if (toggle) toggle.classList.toggle('collapsed', body.classList.contains('collapsed'));
    });
  });
}

/* ── Line items ─────────────────────────────────────────────── */
function bindItemEvents() {
  on('addItemBtn', 'click', addItem);
  renderItemRows();
}

function addItem() {
  state.items.push({ description:'', qty:1, rate:0, amount:0, delivered_qty:1, unit:'pcs' });
  renderItemRows();
  updatePreview();
}

function removeItem(idx) {
  if (state.items.length === 1) { showToast('At least one line item is required', 'error'); return; }
  state.items.splice(idx, 1);
  renderItemRows();
  updatePreview();
}

function renderItemRows() {
  const tbody = document.getElementById('itemsBody');
  if (!tbody) return;
  tbody.innerHTML = '';
  const isDN = state.type === 'delivery_note';

  state.items.forEach((item, i) => {
    const tr = document.createElement('tr');
    tr.className = 'fd-item-row';
    if (isDN) {
      tr.innerHTML = `
        <td style="width:40%">
          <input class="fd-item-input" data-field="description" data-idx="${i}"
                 value="${esc(item.description)}" placeholder="Item description…">
        </td>
        <td style="width:16%">
          <input class="fd-item-input num" type="text" inputmode="decimal" data-field="qty" data-idx="${i}"
                 value="${item.qty}" placeholder="1">
        </td>
        <td style="width:16%">
          <input class="fd-item-input num" type="text" inputmode="decimal" data-field="delivered_qty" data-idx="${i}"
                 value="${item.delivered_qty ?? item.qty}" placeholder="1">
        </td>
        <td style="width:20%">
          <input class="fd-item-input" data-field="unit" data-idx="${i}"
                 value="${esc(item.unit ?? 'pcs')}" placeholder="pcs">
        </td>
        <td style="width:8%">
          <button class="fd-item-del" data-idx="${i}" title="Remove item">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </td>`;
    } else {
      tr.innerHTML = `
        <td style="width:44%">
          <input class="fd-item-input" data-field="description" data-idx="${i}"
                 value="${esc(item.description)}" placeholder="Item description…">
        </td>
        <td style="width:14%">
          <input class="fd-item-input num" type="text" inputmode="decimal" data-field="qty" data-idx="${i}"
                 value="${item.qty}" placeholder="1">
        </td>
        <td style="width:18%">
          <input class="fd-item-input num" type="text" inputmode="decimal" data-field="rate" data-idx="${i}"
                 value="${item.rate}" placeholder="0.00">
        </td>
        <td style="width:16%">
          <span class="fd-item-amount" data-amount-idx="${i}">${fmtCurrency(item.amount)}</span>
        </td>
        <td style="width:8%">
          <button class="fd-item-del" data-idx="${i}" title="Remove item">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </td>`;
    }
    tbody.appendChild(tr);
  });

  // Bind item inputs
  tbody.querySelectorAll('.fd-item-input').forEach(input => {
    input.addEventListener('input', e => {
      const idx   = parseInt(e.target.dataset.idx);
      const field = e.target.dataset.field;
      if (field === 'description' || field === 'unit') {
        state.items[idx][field] = e.target.value;
        updatePreview();
      } else if (field === 'delivered_qty') {
        state.items[idx].delivered_qty = parseFloat(e.target.value) || 0;
        updatePreview();
      } else {
        state.items[idx][field] = parseFloat(e.target.value) || 0;
        recalcItemAmounts();
      }
    });
  });

  tbody.querySelectorAll('.fd-item-del').forEach(btn => {
    btn.addEventListener('click', e => removeItem(parseInt(e.currentTarget.dataset.idx)));
  });
}

// Full recalc + re-render rows (used when adding/removing items or changing currency)
function recalcItems() {
  state.items.forEach(item => {
    item.amount = (parseFloat(item.qty)||0) * (parseFloat(item.rate)||0);
  });
  renderItemRows();
  renderTotals();
  renderPreviewOnly();
}

// Light recalc — only updates amount cells, does NOT re-render inputs (preserves focus)
function recalcItemAmounts() {
  state.items.forEach((item, i) => {
    item.amount = (parseFloat(item.qty)||0) * (parseFloat(item.rate)||0);
    const amountEl = document.querySelector(`[data-amount-idx="${i}"]`);
    if (amountEl) amountEl.textContent = fmtCurrency(item.amount);
  });
  renderTotals();
  renderPreviewOnly();
}

function calcTotals() {
  const subtotal = state.items.reduce((s,i) => s + (i.amount||0), 0);
  let discount = 0;
  if (state.discount.enabled) {
    discount = state.discount.type === 'percentage'
      ? subtotal * (state.discount.value / 100)
      : state.discount.value;
  }
  const afterDiscount = subtotal - discount;
  const tax      = state.tax.enabled      ? afterDiscount * (state.tax.rate / 100) : 0;
  const shipping = state.shipping.enabled ? (state.shipping.amount || 0) : 0;
  const total    = afterDiscount + tax + shipping;
  const paid     = state.paid.enabled ? (state.paid.amount || 0) : 0;
  const due      = total - paid;
  return { subtotal, discount, afterDiscount, tax, shipping, total, paid, due };
}

function renderTotals() {
  const t = calcTotals();
  setText('summSubtotal', fmtCurrency(t.subtotal));
  toggleEl('summDiscountRow', state.discount.enabled);
  if (state.discount.enabled) {
    const lbl = state.discount.type === 'percentage'
      ? `Discount (${state.discount.value}%)`
      : 'Discount';
    setText('summDiscountLabel', lbl);
    setText('summDiscount', '- ' + fmtCurrency(t.discount));
  }
  toggleEl('summTaxRow', state.tax.enabled);
  if (state.tax.enabled) {
    setText('summTaxLabel', `${state.tax.label||'Tax'} (${state.tax.rate}%)`);
    setText('summTax', fmtCurrency(t.tax));
  }
  toggleEl('summShippingRow', state.shipping.enabled);
  if (state.shipping.enabled) setText('summShipping', fmtCurrency(t.shipping));
  setText('summTotal', fmtCurrency(t.total));
  toggleEl('summPaidRow', state.paid.enabled);
  if (state.paid.enabled) setText('summPaid', fmtCurrency(t.paid));
  toggleEl('summDueRow', state.paid.enabled);
  if (state.paid.enabled) setText('summDue', fmtCurrency(t.due));
}

/* ── Preview rendering ──────────────────────────────────────── */

// Full update: recalc amounts + update form totals display + render preview
function updatePreview() {
  state.items.forEach(item => {
    item.amount = (parseFloat(item.qty)||0) * (parseFloat(item.rate)||0);
  });
  renderTotals();
  renderPreviewOnly();
}

// Render preview only (no side effects on form DOM)
function renderPreviewOnly() {
  const container = document.getElementById('previewDoc');
  if (!container) return;
  container.innerHTML = renderTemplate(state);
}

function renderTemplate(s) {
  switch (s.template) {
    case 'classic':    return renderClassic(s);
    case 'minimal':    return renderMinimal(s);
    case 'bold':       return renderBold(s);
    default:           return renderStreamline(s);
  }
}

/* ── Helpers ────────────────────────────────────────────────── */
function fmtCurrency(amount) {
  const c   = state.currency;
  const num = parseFloat(amount || 0).toLocaleString('en-US', { minimumFractionDigits:2, maximumFractionDigits:2 });
  return c.pos === 'after' ? num + c.symbol : c.symbol + num;
}
function fmtDate(d) {
  if (!d) return '';
  try { return new Date(d + 'T00:00:00').toLocaleDateString('en-US', { year:'numeric', month:'long', day:'numeric' }); }
  catch { return d; }
}
function esc(s) {
  if (!s) return '';
  return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function addr(obj, sep = '<br>') {
  const parts = [obj.address, [obj.city, obj.state].filter(Boolean).join(', '), obj.zip, obj.country].filter(Boolean);
  return parts.join(sep);
}
function typeLabel(type) {
  const m = {
    invoice:        'INVOICE',
    quote:          'QUOTATION',
    receipt:        'RECEIPT',
    proforma:       'PROFORMA INVOICE',
    purchase_order: 'PURCHASE ORDER',
    delivery_note:  'DELIVERY NOTE',
  };
  return m[type] || type.toUpperCase();
}
function dueDateLabel(type) {
  if (type === 'quote' || type === 'proforma') return 'Valid Until';
  if (type === 'receipt')        return 'Receipt Date';
  if (type === 'purchase_order') return 'Expected Delivery';
  if (type === 'delivery_note')  return 'Delivery Date';
  return 'Due Date';
}
function toSectionLabel(type) {
  if (type === 'quote')          return 'Prepared For';
  if (type === 'purchase_order') return 'Vendor / Supplier';
  if (type === 'delivery_note')  return 'Deliver To';
  return 'Bill To';
}

function deliveryNoteTableHtml(s, headerBg, headerColor) {
  const rows = s.items.map(item => `
    <tr>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;">${esc(item.description) || '<em style="color:#aaa">Item description</em>'}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:right;">${item.qty}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:right;">${item.delivered_qty ?? item.qty}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:center;">${esc(item.unit || 'pcs')}</td>
    </tr>`).join('');
  return `
    <table width="100%" cellspacing="0" style="border-collapse:collapse;">
      <thead>
        <tr style="background:${headerBg};color:${headerColor};">
          <th style="padding:10px;text-align:left;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Description</th>
          <th style="padding:10px;text-align:right;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Qty Ordered</th>
          <th style="padding:10px;text-align:right;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Qty Delivered</th>
          <th style="padding:10px;text-align:center;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Unit</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
    </table>`;
}

function itemsTableHtml(s, headerBg, headerColor, accentColor) {
  if (s.type === 'delivery_note') return deliveryNoteTableHtml(s, headerBg, headerColor);
  const t = calcTotals();
  let rows = s.items.map(item => `
    <tr>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;">${esc(item.description) || '<em style="color:#aaa">Item description</em>'}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:right;">${item.qty}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:right;">${fmtCurrency(item.rate)}</td>
      <td style="padding:9px 10px;border-bottom:1px solid #f0f0f0;font-size:13px;text-align:right;font-weight:600;">${fmtCurrency(item.amount)}</td>
    </tr>`).join('');

  let totalsHtml = `
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:12.5px;color:#555;">Subtotal</td>
        <td style="padding:7px 10px;text-align:right;font-size:12.5px;">${fmtCurrency(t.subtotal)}</td></tr>`;
  if (s.discount.enabled && t.discount > 0) totalsHtml += `
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:12.5px;color:#555;">${s.discount.type==='percentage'?`Discount (${s.discount.value}%)`:'Discount'}</td>
        <td style="padding:7px 10px;text-align:right;font-size:12.5px;color:#dc2626;">-${fmtCurrency(t.discount)}</td></tr>`;
  if (s.tax.enabled) totalsHtml += `
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:12.5px;color:#555;">${esc(s.tax.label)||'Tax'} (${s.tax.rate}%)</td>
        <td style="padding:7px 10px;text-align:right;font-size:12.5px;">${fmtCurrency(t.tax)}</td></tr>`;
  if (s.shipping.enabled) totalsHtml += `
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:12.5px;color:#555;">Shipping</td>
        <td style="padding:7px 10px;text-align:right;font-size:12.5px;">${fmtCurrency(t.shipping)}</td></tr>`;
  totalsHtml += `
    <tr><td colspan="3" style="padding:10px 10px;text-align:right;font-size:14px;font-weight:700;border-top:2px solid ${accentColor};">
          ${s.type==='receipt'?'Amount Paid':'Total Amount'}</td>
        <td style="padding:10px 10px;text-align:right;font-size:14px;font-weight:700;color:${accentColor};border-top:2px solid ${accentColor};">${fmtCurrency(t.total)}</td></tr>`;
  if (s.paid.enabled && s.type !== 'receipt') {
    totalsHtml += `
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:12.5px;color:#555;">Amount Paid</td>
        <td style="padding:7px 10px;text-align:right;font-size:12.5px;color:#16a34a;">${fmtCurrency(t.paid)}</td></tr>
    <tr><td colspan="3" style="padding:7px 10px;text-align:right;font-size:13px;font-weight:700;">Balance Due</td>
        <td style="padding:7px 10px;text-align:right;font-size:13px;font-weight:700;color:#dc2626;">${fmtCurrency(t.due)}</td></tr>`;
  }

  return `
    <table width="100%" cellspacing="0" style="border-collapse:collapse;">
      <thead>
        <tr style="background:${headerBg};color:${headerColor};">
          <th style="padding:10px;text-align:left;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Description</th>
          <th style="padding:10px;text-align:right;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Qty</th>
          <th style="padding:10px;text-align:right;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Rate</th>
          <th style="padding:10px;text-align:right;font-size:11.5px;font-weight:700;letter-spacing:.5px;text-transform:uppercase;">Amount</th>
        </tr>
      </thead>
      <tbody>${rows}</tbody>
      <tfoot>${totalsHtml}</tfoot>
    </table>`;
}

function footerHtml(s) {
  let out = '';
  if (s.notes) out += `<p style="font-size:12px;color:#444;margin:0 0 8px;"><strong style="color:#222;">Notes:</strong> ${esc(s.notes)}</p>`;
  if (s.terms) out += `<p style="font-size:12px;color:#444;margin:0 0 8px;"><strong style="color:#222;">Terms & Conditions:</strong> ${esc(s.terms)}</p>`;
  if (s.payment_info) {
    const piLabel = s.type === 'delivery_note' ? 'Delivery Instructions' : (s.type === 'purchase_order' ? 'Payment Terms' : 'Payment Info');
    out += `<p style="font-size:12px;color:#444;margin:0 0 8px;"><strong style="color:#222;">${piLabel}:</strong> ${esc(s.payment_info)}</p>`;
  }
  if (s.type === 'purchase_order' && s.authorized_by) {
    out += `<div style="text-align:right;margin-top:24px;">
      <div style="display:inline-block;text-align:center;min-width:190px;">
        <div style="border-top:1px solid #334155;padding-top:6px;margin-top:32px;font-size:12.5px;font-weight:600;">${esc(s.authorized_by)}</div>
        <div style="font-size:10.5px;color:#888;margin-top:2px;">Authorized Signature</div>
      </div>
    </div>`;
  }
  return out;
}

function logoHtml(logo, size = 70) {
  if (!logo) return '';
  return `<img src="${logo}" style="max-height:${size}px;max-width:${size*2}px;object-fit:contain;display:block;">`;
}

/* ── Template: Streamline ───────────────────────────────────── */
function renderStreamline(s) {
  const p = s.colors.primary;
  const a = s.colors.accent;
  const fontStack = fontCss(s.font);
  const title = typeLabel(s.type);

  return `<div style="font-family:${fontStack};color:#1a1a2e;background:#fff;min-height:900px;position:relative;">
    <!-- Left accent bar -->
    <div style="position:absolute;left:0;top:0;bottom:0;width:6px;background:${p};"></div>
    <div style="padding:36px 36px 36px 48px;">
      <!-- Header -->
      <table width="100%" cellspacing="0" style="margin-bottom:28px;">
        <tr>
          <td style="vertical-align:top;">
            ${logoHtml(s.from.logo, 65)}
            ${!s.from.logo ? `<div style="font-size:20px;font-weight:800;color:${p};">${esc(s.from.name)||'Your Business'}</div>` : ''}
            ${s.from.logo && s.from.name ? `<div style="font-size:13px;font-weight:700;color:${p};margin-top:6px;">${esc(s.from.name)}</div>` : ''}
          </td>
          <td style="text-align:right;vertical-align:top;">
            <div style="font-size:28px;font-weight:800;color:${p};letter-spacing:-1px;">${title}</div>
            <div style="font-size:12px;color:#666;margin-top:4px;">#${esc(s.details.number)||'—'}</div>
          </td>
        </tr>
      </table>

      <!-- From info -->
      <div style="font-size:12px;color:#555;margin-bottom:20px;line-height:1.6;">
        ${[s.from.email, s.from.phone].filter(Boolean).map(v=>`<span style="margin-right:14px;">${esc(v)}</span>`).join('')}
        ${addr(s.from) ? `<span>${addr(s.from, ' · ')}</span>` : ''}
      </div>

      <!-- Meta bar -->
      <div style="background:#f8f9fa;border-radius:8px;padding:14px 18px;margin-bottom:24px;">
        <table width="100%" cellspacing="0"><tr>
          <td style="vertical-align:top;padding-right:20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">${toSectionLabel(s.type)}</div>
            <div style="font-size:13px;font-weight:700;color:#1a1a2e;">${esc(s.to.company)||esc(s.to.name)||'—'}</div>
            ${s.to.company && s.to.name ? `<div style="font-size:12px;color:#555;">${esc(s.to.name)}</div>` : ''}
            <div style="font-size:12px;color:#555;">${addr(s.to, ', ')}</div>
            ${s.to.email ? `<div style="font-size:12px;color:#555;">${esc(s.to.email)}</div>` : ''}
          </td>
          ${s.type === 'purchase_order' && (s.ship_to?.company || s.ship_to?.name || s.ship_to?.address) ? `
          <td style="vertical-align:top;padding:0 20px;border-left:1px solid #e5e7eb;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">Ship To</div>
            <div style="font-size:13px;font-weight:700;color:#1a1a2e;">${esc(s.ship_to.company)||esc(s.ship_to.name)||'—'}</div>
            <div style="font-size:12px;color:#555;">${addr(s.ship_to, ', ')}</div>
          </td>` : ''}
          <td style="vertical-align:top;padding:0 20px;border-left:1px solid #e5e7eb;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">Issue Date</div>
            <div style="font-size:13px;font-weight:600;">${fmtDate(s.details.date)||'—'}</div>
          </td>
          <td style="vertical-align:top;padding-left:20px;border-left:1px solid #e5e7eb;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">${dueDateLabel(s.type)}</div>
            <div style="font-size:13px;font-weight:600;color:${s.type==='invoice'?'#dc2626':'#1a1a2e'};">${fmtDate(s.details.due_date)||'—'}</div>
          </td>
          ${s.details.po_number ? `<td style="vertical-align:top;padding-left:20px;border-left:1px solid #e5e7eb;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">${s.type === 'delivery_note' ? 'Order Ref' : 'PO Number'}</div>
            <div style="font-size:13px;font-weight:600;">${esc(s.details.po_number)}</div>
          </td>` : ''}
          ${s.type === 'delivery_note' && s.details.carrier ? `<td style="vertical-align:top;padding-left:20px;border-left:1px solid #e5e7eb;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#888;margin-bottom:4px;">Carrier</div>
            <div style="font-size:13px;font-weight:600;">${esc(s.details.carrier)}</div>
          </td>` : ''}
        </tr></table>
      </div>

      <!-- Items -->
      ${itemsTableHtml(s, p, '#fff', a)}

      <!-- Footer -->
      <div style="margin-top:24px;border-top:1px solid #e5e7eb;padding-top:18px;">
        ${footerHtml(s)}
      </div>

      <!-- Bottom brand line -->
      <div style="margin-top:30px;text-align:center;font-size:10.5px;color:#bbb;">
        Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke
      </div>
    </div>
  </div>`;
}

/* ── Template: Classic ──────────────────────────────────────── */
function renderClassic(s) {
  const p = s.colors.primary;
  const a = s.colors.accent;
  const fontStack = fontCss(s.font);
  const title = typeLabel(s.type);

  return `<div style="font-family:${fontStack};color:#1a1a2e;background:#fff;min-height:900px;">
    <!-- Header -->
    <div style="background:${p};color:#fff;padding:32px 36px;">
      <table width="100%" cellspacing="0"><tr>
        <td style="vertical-align:middle;">
          ${logoHtml(s.from.logo, 60)}
          ${!s.from.logo ? `<div style="font-size:22px;font-weight:800;color:#fff;">${esc(s.from.name)||'Your Business'}</div>` : ''}
          ${s.from.logo && s.from.name ? `<div style="font-size:14px;font-weight:700;color:rgba(255,255,255,.9);margin-top:4px;">${esc(s.from.name)}</div>` : ''}
          <div style="font-size:12px;color:rgba(255,255,255,.75);margin-top:4px;line-height:1.5;">
            ${[s.from.email,s.from.phone].filter(Boolean).join(' &nbsp;|&nbsp; ')}
          </div>
        </td>
        <td style="text-align:right;vertical-align:middle;">
          <div style="font-size:32px;font-weight:800;color:#fff;letter-spacing:-1px;">${title}</div>
          <div style="font-size:13px;color:rgba(255,255,255,.8);margin-top:6px;"># ${esc(s.details.number)||'—'}</div>
        </td>
      </tr></table>
    </div>

    <div style="padding:28px 36px;">
      <!-- From/To/Details row -->
      <table width="100%" cellspacing="0" style="margin-bottom:24px;">
        <tr>
          <td style="vertical-align:top;width:40%;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:${p};margin-bottom:6px;">From</div>
            <div style="font-size:13px;line-height:1.6;color:#333;">
              ${s.from.address ? addr(s.from) : '<em style="color:#aaa">Your address</em>'}
            </div>
          </td>
          <td style="vertical-align:top;width:${s.type === 'purchase_order' ? '25%' : '35%'};padding-left:20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:${p};margin-bottom:6px;">${toSectionLabel(s.type)}</div>
            <div style="font-size:13px;font-weight:700;">${esc(s.to.company)||esc(s.to.name)||'—'}</div>
            ${s.to.company && s.to.name ? `<div style="font-size:12.5px;">${esc(s.to.name)}</div>` : ''}
            <div style="font-size:12.5px;color:#444;line-height:1.6;">${addr(s.to)}</div>
            ${s.to.email ? `<div style="font-size:12px;color:#555;">${esc(s.to.email)}</div>` : ''}
          </td>
          ${s.type === 'purchase_order' && (s.ship_to?.company || s.ship_to?.name || s.ship_to?.address) ? `
          <td style="vertical-align:top;width:25%;padding-left:20px;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:${p};margin-bottom:6px;">Ship To</div>
            <div style="font-size:13px;font-weight:700;">${esc(s.ship_to.company)||esc(s.ship_to.name)||'—'}</div>
            <div style="font-size:12.5px;color:#444;line-height:1.6;">${addr(s.ship_to)}</div>
          </td>` : ''}
          <td style="vertical-align:top;width:25%;text-align:right;">
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:${p};margin-bottom:8px;">Details</div>
            <table style="margin-left:auto;font-size:12px;color:#444;"><tbody>
              <tr><td style="padding:2px 8px 2px 0;color:#888;">Date</td><td style="font-weight:600;">${fmtDate(s.details.date)||'—'}</td></tr>
              <tr><td style="padding:2px 8px 2px 0;color:#888;">${dueDateLabel(s.type)}</td><td style="font-weight:600;">${fmtDate(s.details.due_date)||'—'}</td></tr>
              ${s.details.po_number?`<tr><td style="padding:2px 8px 2px 0;color:#888;">PO#</td><td>${esc(s.details.po_number)}</td></tr>`:''}
              ${s.details.reference?`<tr><td style="padding:2px 8px 2px 0;color:#888;">Ref</td><td>${esc(s.details.reference)}</td></tr>`:''}
            </tbody></table>
          </td>
        </tr>
      </table>

      <!-- Items -->
      ${itemsTableHtml(s, '#f1f5f9', '#1a1a2e', a)}

      <!-- Footer -->
      <div style="margin-top:24px;">${footerHtml(s)}</div>

      <div style="margin-top:30px;text-align:center;font-size:10.5px;color:#ccc;">
        Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke
      </div>
    </div>
  </div>`;
}

/* ── Template: Minimal ──────────────────────────────────────── */
function renderMinimal(s) {
  const a = s.colors.accent;
  const p = s.colors.primary;
  const fontStack = fontCss(s.font);
  const title = typeLabel(s.type);

  return `<div style="font-family:${fontStack};color:#1a1a2e;background:#fff;min-height:900px;padding:44px 48px;">
    <!-- Top row -->
    <table width="100%" cellspacing="0" style="margin-bottom:36px;"><tr>
      <td style="vertical-align:top;">
        ${logoHtml(s.from.logo, 55)}
        <div style="margin-top:${s.from.logo ? 10 : 0}px;">
          <div style="font-size:15px;font-weight:700;color:#0f172a;">${esc(s.from.name)||'Your Business'}</div>
          <div style="font-size:12px;color:#64748b;margin-top:2px;line-height:1.5;">
            ${[s.from.email,s.from.phone,addr(s.from,' ')].filter(Boolean).join('<br>')}
          </div>
        </div>
      </td>
      <td style="text-align:right;vertical-align:top;">
        <div style="font-size:36px;font-weight:900;color:${a};letter-spacing:-2px;">${title}</div>
        <div style="font-size:12px;color:#94a3b8;margin-top:4px;">${esc(s.details.number)||''}</div>
      </td>
    </tr></table>

    <!-- Divider -->
    <div style="border-top:2px solid ${a};margin-bottom:24px;"></div>

    <!-- Recipient + dates -->
    <table width="100%" cellspacing="0" style="margin-bottom:30px;"><tr>
      <td style="vertical-align:top;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:6px;">${toSectionLabel(s.type)}</div>
        <div style="font-size:14px;font-weight:700;">${esc(s.to.company)||esc(s.to.name)||'—'}</div>
        ${s.to.company && s.to.name ? `<div style="font-size:12.5px;color:#555;">${esc(s.to.name)}</div>` : ''}
        <div style="font-size:12px;color:#64748b;">${addr(s.to)}</div>
        ${s.to.email?`<div style="font-size:12px;color:#64748b;">${esc(s.to.email)}</div>`:''}
        ${s.type === 'purchase_order' && (s.ship_to?.company || s.ship_to?.name || s.ship_to?.address) ? `
          <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-top:12px;margin-bottom:6px;">Ship To</div>
          <div style="font-size:13px;font-weight:700;">${esc(s.ship_to.company)||esc(s.ship_to.name)||'—'}</div>
          <div style="font-size:12px;color:#64748b;">${addr(s.ship_to)}</div>` : ''}
      </td>
      <td style="text-align:right;vertical-align:top;">
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:6px;">Date Issued</div>
        <div style="font-size:13px;font-weight:600;margin-bottom:12px;">${fmtDate(s.details.date)||'—'}</div>
        <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;color:#94a3b8;margin-bottom:6px;">${dueDateLabel(s.type)}</div>
        <div style="font-size:13px;font-weight:600;">${fmtDate(s.details.due_date)||'—'}</div>
      </td>
    </tr></table>

    <!-- Items -->
    ${itemsTableHtml(s, a, '#fff', p)}

    <!-- Footer -->
    <div style="margin-top:28px;padding-top:18px;border-top:1px solid #f1f5f9;">
      ${footerHtml(s)}
    </div>

    <div style="margin-top:36px;text-align:center;font-size:10px;color:#cbd5e1;letter-spacing:.5px;">
      GENERATED WITH FINTRACK FREE BUSINESS DOCS &nbsp;·&nbsp; FINTRACK.CO.KE
    </div>
  </div>`;
}

/* ── Template: Bold ─────────────────────────────────────────── */
function renderBold(s) {
  const p = s.colors.primary;
  const a = s.colors.accent;
  const fontStack = fontCss(s.font);
  const title = typeLabel(s.type);
  const t = calcTotals();

  return `<div style="font-family:${fontStack};color:#1a1a2e;background:#fff;min-height:900px;">
    <!-- Hero header -->
    <div style="background:linear-gradient(135deg, ${p} 0%, ${s.colors.secondary||p} 100%);padding:40px 36px;">
      <table width="100%" cellspacing="0"><tr>
        <td style="vertical-align:middle;">
          ${logoHtml(s.from.logo, 65)}
          ${!s.from.logo ? `<div style="font-size:21px;font-weight:800;color:#fff;">${esc(s.from.name)||'Your Business'}</div>` : ''}
          ${s.from.logo && s.from.name ? `<div style="font-size:13px;font-weight:700;color:rgba(255,255,255,.9);margin-top:6px;">${esc(s.from.name)}</div>` : ''}
          <div style="font-size:11.5px;color:rgba(255,255,255,.7);margin-top:4px;line-height:1.6;">
            ${[s.from.email,s.from.phone].filter(Boolean).join(' &nbsp;&bull;&nbsp; ')}
          </div>
        </td>
        <td style="text-align:right;vertical-align:middle;">
          <div style="background:rgba(255,255,255,.12);display:inline-block;padding:8px 20px;border-radius:8px;margin-bottom:10px;">
            <div style="font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:1px;color:rgba(255,255,255,.7);">${title}</div>
          </div>
          ${s.type !== 'delivery_note' ? `<div style="font-size:32px;font-weight:900;color:${a};line-height:1;">${fmtCurrency(t.total)}</div>` : ''}
          <div style="font-size:11px;color:rgba(255,255,255,.6);margin-top:6px;">
            # ${esc(s.details.number)||'—'} &nbsp;&bull;&nbsp; ${fmtDate(s.details.date)||'—'}
          </div>
        </td>
      </tr></table>
    </div>

    <!-- Info strip -->
    <div style="background:#f8fafc;border-bottom:1px solid #e2e8f0;padding:16px 36px;">
      <table width="100%" cellspacing="0"><tr>
        <td style="vertical-align:top;width:50%;">
          <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-bottom:5px;">${toSectionLabel(s.type)}</div>
          <div style="font-size:13.5px;font-weight:700;">${esc(s.to.company)||esc(s.to.name)||'—'}</div>
          ${s.to.company && s.to.name ? `<div style="font-size:12px;color:#555;">${esc(s.to.name)}</div>` : ''}
          <div style="font-size:12px;color:#64748b;">${addr(s.to,', ')}</div>
          ${s.type === 'purchase_order' && (s.ship_to?.company || s.ship_to?.name || s.ship_to?.address) ? `
            <div style="font-size:10px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#94a3b8;margin-top:10px;margin-bottom:5px;">Ship To</div>
            <div style="font-size:13px;font-weight:700;">${esc(s.ship_to.company)||esc(s.ship_to.name)||'—'}</div>
            <div style="font-size:12px;color:#64748b;">${addr(s.ship_to,', ')}</div>` : ''}
        </td>
        <td style="vertical-align:top;text-align:right;">
          <table style="margin-left:auto;font-size:12px;"><tbody>
            <tr><td style="padding:2px 10px 2px 0;color:#888;">${dueDateLabel(s.type)}</td>
                <td style="font-weight:700;color:${s.type==='invoice'?'#dc2626':'#1a1a2e'};">${fmtDate(s.details.due_date)||'—'}</td></tr>
            ${s.details.po_number?`<tr><td style="padding:2px 10px 2px 0;color:#888;">PO#</td><td>${esc(s.details.po_number)}</td></tr>`:''}
            ${s.details.reference?`<tr><td style="padding:2px 10px 2px 0;color:#888;">Ref</td><td>${esc(s.details.reference)}</td></tr>`:''}
          </tbody></table>
        </td>
      </tr></table>
    </div>

    <!-- Items -->
    <div style="padding:24px 36px;">
      ${itemsTableHtml(s, p, '#fff', a)}
      <div style="margin-top:24px;">${footerHtml(s)}</div>
      <div style="margin-top:30px;text-align:center;font-size:10.5px;color:#cbd5e1;">
        Generated with FinTrack Free Business Docs &nbsp;·&nbsp; fintrack.co.ke
      </div>
    </div>
  </div>`;
}

/* ── Font CSS mapping ───────────────────────────────────────── */
function fontCss(font) {
  const map = {
    helvetica:  'Helvetica, Arial, sans-serif',
    georgia:    'Georgia, "Times New Roman", serif',
    courier:    '"Courier New", Courier, monospace',
    trebuchet:  '"Trebuchet MS", Helvetica, sans-serif',
    verdana:    'Verdana, Geneva, sans-serif',
  };
  return map[font] || map.helvetica;
}

/* ── PDF generation ─────────────────────────────────────────── */
function downloadPdf() {
  const btn = document.getElementById('btnDownloadPdf');
  if (btn) { btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Generating…'; btn.disabled = true; }

  const filename = state.type.toUpperCase() + '-' + (state.details.number || '001') + '.pdf';

  fetch(window.FD_PDF_URL, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': window.FD_CSRF,
      'Accept': 'application/pdf',
    },
    body: JSON.stringify({ type: state.type, template: state.template, doc: state }),
  })
  .then(r => {
    if (!r.ok) return r.text().then(t => { throw new Error(t || 'PDF generation failed'); });
    return r.blob();
  })
  .then(blob => {
    const url = URL.createObjectURL(blob);
    const a   = document.createElement('a');
    a.href     = url;
    a.download = filename;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
  })
  .catch(err => {
    console.error(err);
    showToast('PDF generation failed. Please try again.', 'error');
  })
  .finally(() => {
    if (btn) { btn.innerHTML = '<i class="fa-solid fa-download"></i> Download PDF'; btn.disabled = false; }
  });
}

/* ── Share link ─────────────────────────────────────────────── */
function getShareLink() {
  const btn = document.getElementById('btnShareLink');
  if (btn) { btn.innerHTML = '<i class="fa-solid fa-spinner"></i> Saving…'; btn.disabled = true; }

  fetch(window.FD_SAVE_URL, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': window.FD_CSRF },
    body: JSON.stringify({ type: state.type, template: state.template, doc: state }),
  })
  .then(r => r.json())
  .then(data => {
    if (data.url) {
      const input = document.getElementById('shareLinkInput');
      if (input) input.value = data.url;
      openModal();
    } else {
      showToast(data.error || 'Could not generate link', 'error');
    }
  })
  .catch(() => showToast('Network error. Please try again.', 'error'))
  .finally(() => {
    if (btn) { btn.innerHTML = '<i class="fa-solid fa-link"></i> Get Link'; btn.disabled = false; }
  });
}

/* ── Modal ──────────────────────────────────────────────────── */
function openModal() {
  const modal = document.getElementById('shareModal');
  if (modal) modal.classList.add('open');
}
function closeModal() {
  const modal = document.getElementById('shareModal');
  if (modal) modal.classList.remove('open');
}
function copyLink() {
  const input = document.getElementById('shareLinkInput');
  if (!input) return;
  input.select();
  navigator.clipboard.writeText(input.value).then(() => {
    const btn = document.getElementById('copyLinkBtn');
    if (btn) { btn.textContent = 'Copied!'; btn.classList.add('copied'); }
    setTimeout(() => {
      if (btn) { btn.textContent = 'Copy'; btn.classList.remove('copied'); }
    }, 2500);
  });
}

/* ── Mobile tabs ────────────────────────────────────────────── */
function initMobileTabs() {
  const tabs = document.querySelectorAll('.fd-mobile-tab');
  const form = document.getElementById('formPanel');
  const preview = document.getElementById('previewPanel');
  tabs.forEach(tab => {
    tab.addEventListener('click', () => {
      tabs.forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const target = tab.dataset.target;
      if (form) form.style.display = target === 'form' ? '' : 'none';
      if (preview) {
        preview.style.display = target === 'preview' ? 'flex' : 'none';
        preview.classList.toggle('mobile-visible', target === 'preview');
      }
    });
  });
}

/* ── DOM helpers ────────────────────────────────────────────── */
function on(id, event, handler) {
  const el = document.getElementById(id);
  if (el) el.addEventListener(event, handler);
}
function bindField(id, setter, event = 'input') {
  const el = document.getElementById(id);
  if (!el) return;
  el.addEventListener(event, e => { setter(e.target.value); updatePreview(); });
}
function bindToggle(toggleId, fieldsId, setter) {
  const toggle = document.getElementById(toggleId);
  const fields = document.getElementById(fieldsId);
  if (!toggle) return;
  toggle.addEventListener('change', e => {
    setter(e.target.checked);
    if (fields) fields.style.display = e.target.checked ? '' : 'none';
    recalcItems();
  });
  if (fields) fields.style.display = toggle.checked ? '' : 'none';
}
function setText(id, val) {
  const el = document.getElementById(id);
  if (el) el.textContent = val;
}
function toggleEl(id, show) {
  const el = document.getElementById(id);
  if (el) el.style.display = show ? '' : 'none';
}

/* ── Toast ──────────────────────────────────────────────────── */
function showToast(msg, type = 'success') {
  let toast = document.getElementById('fdToast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'fdToast';
    toast.className = 'fd-toast';
    document.body.appendChild(toast);
  }
  toast.className = `fd-toast ${type}`;
  const icon = type === 'error' ? 'fa-circle-xmark' : 'fa-circle-check';
  toast.innerHTML = `<i class="fa-solid ${icon}"></i> ${msg}`;
  toast.classList.add('show');
  setTimeout(() => toast.classList.remove('show'), 3500);
}

/* ── Expose globals ─────────────────────────────────────────── */
window.initBuilder  = initBuilder;
window.addItem      = addItem;
window.openModal    = openModal;
window.closeModal   = closeModal;
window.copyLink     = copyLink;
window.showToast    = showToast;
