@extends('layouts.portal')

@section('title', 'Settings - FinTrack')

@section('content')
<div class="page-header">
    <div>
        <h1 class="page-title">Settings</h1>
        <p class="page-subtitle">Manage your account, business info, and preferences</p>
    </div>
</div>

<div class="row">
    {{-- ── Left nav ── --}}
    <div class="col-lg-3 mb-4">
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush rounded" id="settingsTabs" role="tablist">
                    <a href="#profile"       class="list-group-item list-group-item-action {{ $activeTab === 'profile'        ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-user me-2"></i> Profile
                    </a>
                    <a href="#business"      class="list-group-item list-group-item-action {{ $activeTab === 'business'       ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-building me-2"></i> Business
                    </a>
                    <a href="#documents"     class="list-group-item list-group-item-action {{ $activeTab === 'documents'      ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-file-invoice me-2"></i> Documents
                    </a>
                    <a href="#preferences"   class="list-group-item list-group-item-action {{ $activeTab === 'preferences'    ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-cog me-2"></i> Preferences
                    </a>
                    <a href="#currency"      class="list-group-item list-group-item-action {{ $activeTab === 'currency'       ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-coins me-2"></i> Currency
                    </a>
                    <a href="#notifications" class="list-group-item list-group-item-action {{ $activeTab === 'notifications'  ? 'active' : '' }}"
                       data-bs-toggle="tab" role="tab">
                        <i class="fas fa-bell me-2"></i> Notifications
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ── Tab panes ── --}}
    <div class="col-lg-9">
        <div class="tab-content">

            {{-- ════ Profile ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'profile' ? 'show active' : '' }}" id="profile" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user me-2" style="color:var(--ft-teal);"></i>Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name"
                                           value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Personal Phone</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
                                    <div class="form-text">Used for account contact only. Add a business phone on the Business tab.</div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ════ Business ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'business' ? 'show active' : '' }}" id="business" role="tabpanel">

                {{-- Logo card --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-image me-2" style="color:var(--ft-teal);"></i>Business Logo</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted" style="font-size:13px;">Your logo will appear on all quotes and invoices. PNG or JPG, max 2 MB.</p>

                        <div class="d-flex align-items-center gap-4 flex-wrap mb-3">
                            {{-- Current logo preview --}}
                            <div id="logoPreviewWrap" style="width:140px;height:80px;border:2px dashed var(--border);border-radius:8px;display:flex;align-items:center;justify-content:center;overflow:hidden;background:#f8fafc;">
                                @if($user->logo_path && Storage::disk('public')->exists($user->logo_path))
                                    <img id="logoPreview" src="{{ Storage::url($user->logo_path) }}" alt="Logo" style="max-width:130px;max-height:72px;object-fit:contain;">
                                @else
                                    <div id="logoPlaceholder" style="text-align:center;color:#94a3b8;">
                                        <i class="fas fa-image fa-2x mb-1"></i>
                                        <div style="font-size:11px;">No logo</div>
                                    </div>
                                    <img id="logoPreview" src="" alt="Logo" style="max-width:130px;max-height:72px;object-fit:contain;display:none;">
                                @endif
                            </div>

                            <div class="d-flex flex-column gap-2">
                                <form action="{{ route('settings.logo.upload') }}" method="POST" enctype="multipart/form-data" id="logoUploadForm">
                                    @csrf
                                    <label class="btn btn-outline-primary btn-sm mb-0" style="cursor:pointer;">
                                        <i class="fas fa-upload me-1"></i>{{ $user->logo_path ? 'Replace Logo' : 'Upload Logo' }}
                                        <input type="file" name="logo" id="logoInput" accept="image/*" style="display:none;" onchange="previewLogo(this)">
                                    </label>
                                </form>

                                @if($user->logo_path)
                                <form action="{{ route('settings.logo.remove') }}" method="POST" id="logoRemoveForm">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-outline-danger btn-sm"
                                            onclick="return confirm('Remove your business logo?')">
                                        <i class="fas fa-trash me-1"></i>Remove Logo
                                    </button>
                                </form>
                                @endif
                            </div>
                        </div>

                        {{-- Confirm-upload button (shown after file selected) --}}
                        <div id="uploadConfirmArea" style="display:none;">
                            <button type="button" class="btn btn-primary btn-sm" onclick="document.getElementById('logoUploadForm').submit();">
                                <i class="fas fa-save me-1"></i>Save Logo
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="cancelLogoSelect()">Cancel</button>
                        </div>

                        @error('logo')
                            <div class="text-danger mt-2" style="font-size:13px;">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                {{-- Business info card --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-building me-2" style="color:var(--ft-teal);"></i>Business Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="business_name" class="form-label">Business Name</label>
                                    <input type="text" class="form-control" id="business_name" name="business_name"
                                           value="{{ old('business_name', $user->business_name) }}" placeholder="Your Business Name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="business_phone" class="form-label">Business Phone</label>
                                    <input type="text" class="form-control" id="business_phone" name="business_phone"
                                           value="{{ old('business_phone', $user->business_phone) }}" placeholder="+1 234 567 8900">
                                    <div class="form-text">Shown on quotes and invoices.</div>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="tax_number" class="form-label">Tax Number / VAT ID</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number"
                                       value="{{ old('tax_number', $user->tax_number) }}" placeholder="XX-XXXXXXX">
                            </div>
                            <div class="mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <textarea class="form-control" id="business_address" name="business_address" rows="3"
                                          placeholder="Enter your business address">{{ old('business_address', $user->business_address) }}</textarea>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Business Info
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ════ Documents ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'documents' ? 'show active' : '' }}" id="documents" role="tabpanel">
                @php $ds = $user->getDocSettings(); @endphp
                <form action="{{ route('settings.documents.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Layout templates --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-layout me-2" style="color:var(--ft-teal);"></i>Layout Template</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-3" style="font-size:13px;">Choose how your quotes and invoices are arranged.</p>
                            <div class="row g-3">
                                {{-- Classic --}}
                                <div class="col-md-4">
                                    <label class="doc-layout-card {{ $ds['layout'] === 'classic' ? 'selected' : '' }}">
                                        <input type="radio" name="layout" value="classic" {{ $ds['layout'] === 'classic' ? 'checked' : '' }}>
                                        <div class="doc-layout-preview classic-preview">
                                            <div class="dlp-header" style="background:var(--doc-primary, #0B2A4A);">
                                                <div class="dlp-biz">Business</div>
                                                <div class="dlp-doc">INVOICE</div>
                                            </div>
                                            <div class="dlp-body">
                                                <div class="dlp-row">
                                                    <div class="dlp-box" style="border-left:2px solid var(--doc-accent, #0E7490);">From</div>
                                                    <div class="dlp-box">To</div>
                                                    <div class="dlp-box" style="border-left:2px solid var(--doc-primary, #0B2A4A);">Details</div>
                                                </div>
                                                <div class="dlp-items"></div>
                                                <div class="dlp-summary"></div>
                                            </div>
                                        </div>
                                        <div class="doc-layout-label">
                                            <strong>Classic</strong>
                                            <div class="text-muted" style="font-size:11px;">Bold header · From/To/Details row · Items · Summary</div>
                                        </div>
                                    </label>
                                </div>
                                {{-- Modern --}}
                                <div class="col-md-4">
                                    <label class="doc-layout-card {{ $ds['layout'] === 'modern' ? 'selected' : '' }}">
                                        <input type="radio" name="layout" value="modern" {{ $ds['layout'] === 'modern' ? 'checked' : '' }}>
                                        <div class="doc-layout-preview">
                                            <div class="dlp-header modern-hdr" style="border-bottom:3px solid var(--doc-accent, #0E7490);">
                                                <div class="dlp-logo-box">LOGO</div>
                                                <div class="dlp-doc-modern" style="color:var(--doc-primary, #0B2A4A);">INVOICE</div>
                                            </div>
                                            <div class="dlp-body">
                                                <div class="dlp-row">
                                                    <div class="dlp-box">From</div>
                                                    <div class="dlp-box">To</div>
                                                    <div class="dlp-box">Details</div>
                                                </div>
                                                <div class="dlp-items"></div>
                                                <div class="dlp-summary"></div>
                                            </div>
                                        </div>
                                        <div class="doc-layout-label">
                                            <strong>Modern</strong>
                                            <div class="text-muted" style="font-size:11px;">White header with logo · Accent border · Clean sections</div>
                                        </div>
                                    </label>
                                </div>
                                {{-- Minimal --}}
                                <div class="col-md-4">
                                    <label class="doc-layout-card {{ $ds['layout'] === 'minimal' ? 'selected' : '' }}">
                                        <input type="radio" name="layout" value="minimal" {{ $ds['layout'] === 'minimal' ? 'checked' : '' }}>
                                        <div class="doc-layout-preview">
                                            <div class="dlp-header minimal-hdr">
                                                <div class="dlp-biz" style="color:#111;">Business</div>
                                                <div class="dlp-doc" style="color:var(--doc-accent, #0E7490);">INVOICE</div>
                                            </div>
                                            <div class="dlp-body">
                                                <div class="dlp-row">
                                                    <div class="dlp-box">From</div>
                                                    <div class="dlp-box">To</div>
                                                    <div class="dlp-box">Details</div>
                                                </div>
                                                <div class="dlp-items"></div>
                                                <div class="dlp-summary"></div>
                                            </div>
                                        </div>
                                        <div class="doc-layout-label">
                                            <strong>Minimal</strong>
                                            <div class="text-muted" style="font-size:11px;">Borderless white · Typography-first · Simple lines</div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Colors --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-palette me-2" style="color:var(--ft-teal);"></i>Brand Colours</h5>
                        </div>
                        <div class="card-body">
                            <p class="text-muted mb-4" style="font-size:13px;">These colours are applied to your PDF documents. Click any swatch to pick a custom colour.</p>
                            <div class="row g-4">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Primary Colour</label>
                                    <div class="form-text mb-2">Header background, table headers</div>
                                    <div class="color-picker-wrap">
                                        <input type="color" class="color-thumb" id="primary_color_picker"
                                               value="{{ $ds['primary_color'] }}"
                                               oninput="syncColor('primary_color', this.value)">
                                        <input type="text" class="form-control form-control-sm color-hex-input"
                                               id="primary_color_hex" name="primary_color"
                                               value="{{ $ds['primary_color'] }}"
                                               oninput="syncColorFromHex('primary_color', this.value)"
                                               maxlength="7" placeholder="#0B2A4A">
                                    </div>
                                    <div class="color-swatches mt-2">
                                        @foreach(['#0B2A4A','#1e3a5f','#1a1a2e','#2d3748','#374151','#7c3aed','#991b1b','#065f46'] as $c)
                                            <span class="color-swatch" style="background:{{ $c }};" onclick="syncColor('primary_color','{{ $c }}')"></span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Accent Colour</label>
                                    <div class="form-text mb-2">Separator bars, left borders on info boxes</div>
                                    <div class="color-picker-wrap">
                                        <input type="color" class="color-thumb" id="accent_color_picker"
                                               value="{{ $ds['accent_color'] }}"
                                               oninput="syncColor('accent_color', this.value)">
                                        <input type="text" class="form-control form-control-sm color-hex-input"
                                               id="accent_color_hex" name="accent_color"
                                               value="{{ $ds['accent_color'] }}"
                                               oninput="syncColorFromHex('accent_color', this.value)"
                                               maxlength="7" placeholder="#0E7490">
                                    </div>
                                    <div class="color-swatches mt-2">
                                        @foreach(['#0E7490','#0284c7','#059669','#d97706','#dc2626','#7c3aed','#db2777','#0f766e'] as $c)
                                            <span class="color-swatch" style="background:{{ $c }};" onclick="syncColor('accent_color','{{ $c }}')"></span>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold">Highlight Colour</label>
                                    <div class="form-text mb-2">Document type label (INVOICE / QUOTE)</div>
                                    <div class="color-picker-wrap">
                                        <input type="color" class="color-thumb" id="highlight_color_picker"
                                               value="{{ $ds['highlight_color'] }}"
                                               oninput="syncColor('highlight_color', this.value)">
                                        <input type="text" class="form-control form-control-sm color-hex-input"
                                               id="highlight_color_hex" name="highlight_color"
                                               value="{{ $ds['highlight_color'] }}"
                                               oninput="syncColorFromHex('highlight_color', this.value)"
                                               maxlength="7" placeholder="#22D3EE">
                                    </div>
                                    <div class="color-swatches mt-2">
                                        @foreach(['#22D3EE','#38bdf8','#34d399','#fbbf24','#fb7185','#a78bfa','#f472b6','#ffffff'] as $c)
                                            <span class="color-swatch" style="background:{{ $c }};border:1px solid #e2e8f0;" onclick="syncColor('highlight_color','{{ $c }}')"></span>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Live preview strip --}}
                            <div class="mt-4">
                                <label class="form-label fw-semibold mb-2">Preview</label>
                                <div id="colorPreviewStrip" style="border-radius:8px;overflow:hidden;border:1px solid #e2e8f0;">
                                    <div id="previewHeader" style="padding:12px 16px;display:flex;justify-content:space-between;align-items:center;background:{{ $ds['primary_color'] }};">
                                        <span style="color:#fff;font-weight:700;font-size:14px;">{{ $user->business_name ?? 'Your Business' }}</span>
                                        <span id="previewLabel" style="font-weight:700;font-size:18px;letter-spacing:2px;color:{{ $ds['highlight_color'] }};">INVOICE</span>
                                    </div>
                                    <div id="previewBar" style="height:3px;background:{{ $ds['accent_color'] }};"></div>
                                    <div style="padding:10px 16px;background:#f8fafc;display:flex;gap:8px;">
                                        <div style="flex:1;background:#fff;border-radius:4px;padding:8px;border-left:3px solid {{ $ds['accent_color'] }};font-size:11px;">Prepared By</div>
                                        <div style="flex:1;background:#fff;border-radius:4px;padding:8px;border-left:3px solid {{ $ds['accent_color'] }};font-size:11px;">Prepared For</div>
                                        <div style="flex:1;background:#fff;border-radius:4px;padding:8px;border-left:3px solid {{ $ds['primary_color'] }};font-size:11px;">Details</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Font --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-font me-2" style="color:var(--ft-teal);"></i>Font Style</h5>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="font-option-card {{ $ds['font'] === 'sans' ? 'selected' : '' }}">
                                        <input type="radio" name="font" value="sans" {{ $ds['font'] === 'sans' ? 'checked' : '' }}>
                                        <div class="font-sample" style="font-family:Arial,sans-serif;">Aa</div>
                                        <div class="font-label"><strong>Sans Serif</strong><br><span class="text-muted" style="font-size:11px;">Clean &amp; modern (DejaVu Sans)</span></div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-option-card {{ $ds['font'] === 'serif' ? 'selected' : '' }}">
                                        <input type="radio" name="font" value="serif" {{ $ds['font'] === 'serif' ? 'checked' : '' }}>
                                        <div class="font-sample" style="font-family:Georgia,serif;">Aa</div>
                                        <div class="font-label"><strong>Serif</strong><br><span class="text-muted" style="font-size:11px;">Traditional &amp; elegant (DejaVu Serif)</span></div>
                                    </label>
                                </div>
                                <div class="col-md-4">
                                    <label class="font-option-card {{ $ds['font'] === 'mono' ? 'selected' : '' }}">
                                        <input type="radio" name="font" value="mono" {{ $ds['font'] === 'mono' ? 'checked' : '' }}>
                                        <div class="font-sample" style="font-family:Courier,monospace;">Aa</div>
                                        <div class="font-label"><strong>Monospace</strong><br><span class="text-muted" style="font-size:11px;">Technical &amp; precise (Courier)</span></div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Logo + Footer note --}}
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-header bg-white py-3">
                            <h5 class="mb-0"><i class="fas fa-sliders me-2" style="color:var(--ft-teal);"></i>Other Options</h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" role="switch"
                                           id="show_logo" name="show_logo" value="1"
                                           {{ $ds['show_logo'] ? 'checked' : '' }}>
                                    <label class="form-check-label" for="show_logo">
                                        <strong>Show logo on documents</strong>
                                        @if(!$user->logo_path)
                                            <span class="badge bg-warning text-dark ms-1" style="font-size:10px;">No logo uploaded</span>
                                        @endif
                                    </label>
                                </div>
                                <div class="form-text">When enabled, your business logo appears in the document header.</div>
                            </div>
                            <div class="mb-3">
                                <label for="footer_note" class="form-label fw-semibold">Footer Note</label>
                                <input type="text" class="form-control" id="footer_note" name="footer_note"
                                       value="{{ old('footer_note', $ds['footer_note']) }}"
                                       placeholder="Thank you for your business." maxlength="300">
                                <div class="form-text">Shown at the bottom of every quote and invoice PDF.</div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i>Save Document Settings
                        </button>
                    </div>
                </form>
            </div>

            {{-- ════ Preferences ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'preferences' ? 'show active' : '' }}" id="preferences" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-cog me-2" style="color:var(--ft-teal);"></i>Regional Preferences</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        <option value="UTC"                  {{ old('timezone', $user->timezone ?? 'UTC') === 'UTC'                  ? 'selected' : '' }}>UTC</option>
                                        <option value="Africa/Nairobi"       {{ old('timezone', $user->timezone) === 'Africa/Nairobi'       ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                        <option value="Africa/Lagos"         {{ old('timezone', $user->timezone) === 'Africa/Lagos'         ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                                        <option value="Africa/Johannesburg"  {{ old('timezone', $user->timezone) === 'Africa/Johannesburg'  ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option>
                                        <option value="Africa/Cairo"         {{ old('timezone', $user->timezone) === 'Africa/Cairo'         ? 'selected' : '' }}>Africa/Cairo (EET)</option>
                                        <option value="America/New_York"     {{ old('timezone', $user->timezone) === 'America/New_York'     ? 'selected' : '' }}>America/New_York (EST)</option>
                                        <option value="America/Los_Angeles"  {{ old('timezone', $user->timezone) === 'America/Los_Angeles'  ? 'selected' : '' }}>America/Los_Angeles (PST)</option>
                                        <option value="America/Chicago"      {{ old('timezone', $user->timezone) === 'America/Chicago'      ? 'selected' : '' }}>America/Chicago (CST)</option>
                                        <option value="Europe/London"        {{ old('timezone', $user->timezone) === 'Europe/London'        ? 'selected' : '' }}>Europe/London (GMT)</option>
                                        <option value="Europe/Paris"         {{ old('timezone', $user->timezone) === 'Europe/Paris'         ? 'selected' : '' }}>Europe/Paris (CET)</option>
                                        <option value="Europe/Berlin"        {{ old('timezone', $user->timezone) === 'Europe/Berlin'        ? 'selected' : '' }}>Europe/Berlin (CET)</option>
                                        <option value="Asia/Dubai"           {{ old('timezone', $user->timezone) === 'Asia/Dubai'           ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                                        <option value="Asia/Singapore"       {{ old('timezone', $user->timezone) === 'Asia/Singapore'       ? 'selected' : '' }}>Asia/Singapore (SGT)</option>
                                        <option value="Asia/Tokyo"           {{ old('timezone', $user->timezone) === 'Asia/Tokyo'           ? 'selected' : '' }}>Asia/Tokyo (JST)</option>
                                        <option value="Asia/Kolkata"         {{ old('timezone', $user->timezone) === 'Asia/Kolkata'         ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                        <option value="Australia/Sydney"     {{ old('timezone', $user->timezone) === 'Australia/Sydney'     ? 'selected' : '' }}>Australia/Sydney (AEST)</option>
                                    </select>
                                    <small class="text-muted">Affects how dates and times are displayed</small>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ════ Currency ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'currency' ? 'show active' : '' }}" id="currency" role="tabpanel">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-coins me-2" style="color:var(--ft-teal);"></i>Currency Settings</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Your financial data will be displayed in your selected currency. This affects all reports, invoices, and dashboards.
                            </div>
                            <div class="mb-4">
                                <label for="currency_code" class="form-label">Select Your Currency</label>
                                <select class="form-select form-select-lg" id="currency_code" name="currency_code" required>
                                    <option value="">Choose a currency...</option>
                                    @foreach($currencies as $code => $details)
                                    <option value="{{ $code }}" {{ old('currency_code', $user->currency_code ?? 'USD') === $code ? 'selected' : '' }}>
                                        {{ $details['symbol'] }} {{ $code }} — {{ $details['name'] }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="card bg-light border-0 mb-4">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-eye me-2"></i>Preview</h6>
                                    <hr>
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <p class="text-muted mb-1">Large Amount</p>
                                            <h4 class="mb-0">{{ CurrencyHelper::format(12345.67) }}</h4>
                                        </div>
                                        <div class="col-4">
                                            <p class="text-muted mb-1">Small Amount</p>
                                            <h4 class="mb-0">{{ CurrencyHelper::format(99.99) }}</h4>
                                        </div>
                                        <div class="col-4">
                                            <p class="text-muted mb-1">Zero</p>
                                            <h4 class="mb-0">{{ CurrencyHelper::format(0) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label class="form-label">Popular Currencies</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['USD', 'EUR', 'GBP', 'KES', 'NGN', 'ZAR', 'INR', 'JPY'] as $code)
                                    <button type="button"
                                            class="btn btn-outline-secondary quick-currency {{ old('currency_code', $user->currency_code ?? 'USD') === $code ? 'active' : '' }}"
                                            data-currency="{{ $code }}">
                                        {{ $currencies[$code]['symbol'] }} {{ $code }}
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Currency
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            {{-- ════ Notifications ════ --}}
            <div class="tab-pane fade {{ $activeTab === 'notifications' ? 'show active' : '' }}" id="notifications" role="tabpanel">
                <form action="{{ route('settings.notifications.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="card border-0 shadow-sm mb-3">
                        <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between flex-wrap gap-2">
                            <h5 class="mb-0">
                                <i class="fas fa-bell me-2" style="color:var(--ft-teal);"></i>Notification Preferences
                            </h5>
                            <div class="d-flex gap-2 flex-wrap">
                                <button type="button" class="btn btn-sm btn-primary" id="checkNowBtn">
                                    <i class="fas fa-rotate me-1"></i>Check Now
                                </button>
                                <a href="{{ route('notifications.index') }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="fas fa-list me-1"></i>View all
                                </a>
                            </div>
                        </div>
                        <div class="card-body p-0">

                            <div class="px-4 py-3" style="background:rgba(14,116,144,.05);border-bottom:1px solid var(--border);">
                                <div class="d-flex gap-3 align-items-start">
                                    <i class="fas fa-info-circle mt-1" style="color:var(--ft-teal);flex-shrink:0;"></i>
                                    <div style="font-size:13px;color:var(--text-muted);">
                                        <strong style="color:var(--text-h);">How notifications work:</strong>
                                        FinTrack checks your data daily and generates alerts automatically.
                                        Toggle <strong>In-App</strong> to see alerts in the bell icon, and <strong>Email</strong>
                                        to receive them at <strong>{{ $user->email }}</strong>.
                                        Click <strong>Check Now</strong> to generate notifications immediately.
                                    </div>
                                </div>
                            </div>

                            <div class="notif-settings-header">
                                <div class="notif-settings-label">Alert Type</div>
                                <div class="notif-settings-col text-center">In-App</div>
                                <div class="notif-settings-col text-center">Email</div>
                                <div class="notif-settings-col text-center">Timing</div>
                            </div>

                            <div class="notif-settings-row">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(244,63,94,.12);">
                                        <i class="fas fa-file-invoice-dollar" style="color:#F43F5E;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Invoice Overdue</div>
                                        <div class="notif-settings-desc">Alert when an invoice passes its due date and is unpaid</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="invoice_overdue_app" name="invoice_overdue_app" value="1"
                                               {{ $notifSettings->invoice_overdue_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="invoice_overdue_email" name="invoice_overdue_email" value="1"
                                               {{ $notifSettings->invoice_overdue_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <span class="badge" style="background:rgba(244,63,94,.12);color:#F43F5E;font-size:11px;">Daily</span>
                                </div>
                            </div>

                            <div class="notif-settings-row">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(245,158,11,.12);">
                                        <i class="fas fa-file-contract" style="color:#F59E0B;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Quote Expiring Soon</div>
                                        <div class="notif-settings-desc">Alert when a sent quote is close to its expiry date</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="quote_expiring_app" name="quote_expiring_app" value="1"
                                               {{ $notifSettings->quote_expiring_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="quote_expiring_email" name="quote_expiring_email" value="1"
                                               {{ $notifSettings->quote_expiring_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="input-group input-group-sm notif-days-input">
                                        <input type="number" class="form-control form-control-sm text-center"
                                               name="quote_expiring_days"
                                               value="{{ old('quote_expiring_days', $notifSettings->quote_expiring_days) }}"
                                               min="1" max="30" style="width:56px;">
                                        <span class="input-group-text" style="font-size:11px;">days before</span>
                                    </div>
                                </div>
                            </div>

                            <div class="notif-settings-row">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(249,115,22,.12);">
                                        <i class="fas fa-handshake" style="color:#F97316;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Debt Payment Due Soon</div>
                                        <div class="notif-settings-desc">Alert when a payable debt is approaching its due date</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="debt_due_app" name="debt_due_app" value="1"
                                               {{ $notifSettings->debt_due_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="debt_due_email" name="debt_due_email" value="1"
                                               {{ $notifSettings->debt_due_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="input-group input-group-sm notif-days-input">
                                        <input type="number" class="form-control form-control-sm text-center"
                                               name="debt_due_days"
                                               value="{{ old('debt_due_days', $notifSettings->debt_due_days) }}"
                                               min="1" max="30" style="width:56px;">
                                        <span class="input-group-text" style="font-size:11px;">days before</span>
                                    </div>
                                </div>
                            </div>

                            <div class="notif-settings-row">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(34,197,94,.12);">
                                        <i class="fas fa-piggy-bank" style="color:#22C55E;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Savings Goal Reached</div>
                                        <div class="notif-settings-desc">Celebrate when a savings account hits its target amount</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="savings_goal_app" name="savings_goal_app" value="1"
                                               {{ $notifSettings->savings_goal_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="savings_goal_email" name="savings_goal_email" value="1"
                                               {{ $notifSettings->savings_goal_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <span class="badge" style="background:rgba(34,197,94,.12);color:#22C55E;font-size:11px;">Instant</span>
                                </div>
                            </div>

                            <div class="notif-settings-row">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(139,92,246,.12);">
                                        <i class="fas fa-diagram-project" style="color:#8B5CF6;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Project Deadline Approaching</div>
                                        <div class="notif-settings-desc">Alert when an active project's deadline is near</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="project_deadline_app" name="project_deadline_app" value="1"
                                               {{ $notifSettings->project_deadline_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="project_deadline_email" name="project_deadline_email" value="1"
                                               {{ $notifSettings->project_deadline_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="input-group input-group-sm notif-days-input">
                                        <input type="number" class="form-control form-control-sm text-center"
                                               name="project_deadline_days"
                                               value="{{ old('project_deadline_days', $notifSettings->project_deadline_days) }}"
                                               min="1" max="30" style="width:56px;">
                                        <span class="input-group-text" style="font-size:11px;">days before</span>
                                    </div>
                                </div>
                            </div>

                            <div class="notif-settings-row" style="border-bottom:none;">
                                <div class="notif-settings-label">
                                    <div class="notif-settings-icon" style="background:rgba(239,68,68,.12);">
                                        <i class="fas fa-chart-pie" style="color:#EF4444;"></i>
                                    </div>
                                    <div>
                                        <div class="notif-settings-name">Budget Alert</div>
                                        <div class="notif-settings-desc">Alert when an expense category exceeds its monthly budget</div>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="budget_alert_app" name="budget_alert_app" value="1"
                                               {{ $notifSettings->budget_alert_app ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <div class="form-check form-switch d-inline-flex">
                                        <input class="form-check-input" type="checkbox" role="switch"
                                               id="budget_alert_email" name="budget_alert_email" value="1"
                                               {{ $notifSettings->budget_alert_email ? 'checked' : '' }}>
                                    </div>
                                </div>
                                <div class="notif-settings-col text-center">
                                    <span class="badge" style="background:rgba(239,68,68,.12);color:#EF4444;font-size:11px;">Daily</span>
                                </div>
                            </div>

                        </div>
                        <div class="card-footer bg-white border-top d-flex justify-content-between align-items-center py-3">
                            <span class="text-muted" style="font-size:12px;">
                                <i class="fas fa-clock me-1"></i>Auto-generated daily at 7:00 AM &nbsp;·&nbsp; Use "Check Now" to refresh immediately
                            </span>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Save Preferences
                            </button>
                        </div>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>

<style>
/* ── Document layout cards ─────────────────────────── */
.doc-layout-card {
    display: block;
    cursor: pointer;
    border: 2px solid var(--border);
    border-radius: 10px;
    overflow: hidden;
    transition: border-color .15s, box-shadow .15s;
}
.doc-layout-card:hover { border-color: var(--ft-teal); }
.doc-layout-card input[type=radio] { display: none; }
.doc-layout-card.selected,
.doc-layout-card:has(input:checked) {
    border-color: var(--ft-teal);
    box-shadow: 0 0 0 3px rgba(14,116,144,.15);
}
.doc-layout-preview {
    background: #f1f5f9;
    padding: 8px;
    min-height: 120px;
}
.dlp-header {
    border-radius: 4px;
    padding: 8px 10px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 6px;
    background: #0B2A4A;
}
.dlp-biz { color: #fff; font-size: 9px; font-weight: 700; }
.dlp-doc { color: #22D3EE; font-size: 11px; font-weight: 700; letter-spacing: 1px; }
.modern-hdr { background: #fff !important; border-bottom: 2px solid #0E7490; }
.dlp-logo-box { width: 28px; height: 16px; background: #e2e8f0; border-radius: 3px; font-size: 7px; color: #94a3b8; display: flex; align-items:center; justify-content:center; }
.dlp-doc-modern { font-size: 11px; font-weight: 700; letter-spacing: 1px; }
.minimal-hdr { background: #fff !important; border-bottom: 1px solid #e2e8f0; }
.dlp-body { padding: 4px 0; }
.dlp-row { display: flex; gap: 4px; margin-bottom: 5px; }
.dlp-box { flex: 1; background: #fff; border-radius: 3px; padding: 5px 4px; font-size: 7px; color: #64748b; border-left: 2px solid #e2e8f0; }
.dlp-items { height: 24px; background: #fff; border-radius: 3px; margin-bottom: 5px; }
.dlp-summary { height: 18px; background: #fff; border-radius: 3px; width: 55%; margin-left: auto; }
.doc-layout-label { padding: 10px 12px 12px; background: #fff; }

/* ── Font option cards ──────────────────────────────── */
.font-option-card {
    display: block;
    cursor: pointer;
    border: 2px solid var(--border);
    border-radius: 10px;
    padding: 16px;
    text-align: center;
    transition: border-color .15s, box-shadow .15s;
}
.font-option-card:hover { border-color: var(--ft-teal); }
.font-option-card input[type=radio] { display: none; }
.font-option-card.selected,
.font-option-card:has(input:checked) {
    border-color: var(--ft-teal);
    box-shadow: 0 0 0 3px rgba(14,116,144,.15);
}
.font-sample { font-size: 36px; line-height: 1; margin-bottom: 8px; color: var(--text-h); }
.font-label { font-size: 13px; }

/* ── Color pickers ──────────────────────────────────── */
.color-picker-wrap { display: flex; align-items: center; gap: 8px; }
.color-thumb {
    width: 36px; height: 36px;
    padding: 2px; border: 1px solid var(--border);
    border-radius: 6px; cursor: pointer;
    flex-shrink: 0;
}
.color-hex-input { font-family: monospace; font-size: 13px; }
.color-swatches { display: flex; gap: 6px; flex-wrap: wrap; }
.color-swatch {
    width: 22px; height: 22px;
    border-radius: 4px;
    cursor: pointer;
    transition: transform .1s;
}
.color-swatch:hover { transform: scale(1.2); }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Activate correct tab from URL query param ──────────────────────────
    var activeTabId = '{{ $activeTab }}';
    var tabEl = document.querySelector('#settingsTabs a[href="#' + activeTabId + '"]');
    if (tabEl) {
        var tab = new bootstrap.Tab(tabEl);
        tab.show();
    }

    // Persist active tab on tab click
    document.querySelectorAll('#settingsTabs a[data-bs-toggle="tab"]').forEach(function (link) {
        link.addEventListener('shown.bs.tab', function () {
            var id = link.getAttribute('href').replace('#', '');
            history.replaceState(null, '', '?tab=' + id);
        });
    });

    // ── Layout card selection ──────────────────────────────────────────────
    document.querySelectorAll('.doc-layout-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.doc-layout-card').forEach(function (c) { c.classList.remove('selected'); });
            card.classList.add('selected');
        });
    });

    // ── Font card selection ────────────────────────────────────────────────
    document.querySelectorAll('.font-option-card').forEach(function (card) {
        card.addEventListener('click', function () {
            document.querySelectorAll('.font-option-card').forEach(function (c) { c.classList.remove('selected'); });
            card.classList.add('selected');
        });
    });

    // ── Quick currency buttons ─────────────────────────────────────────────
    var quickCurrencyBtns = document.querySelectorAll('.quick-currency');
    var currencySelect    = document.getElementById('currency_code');

    quickCurrencyBtns.forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (currencySelect) currencySelect.value = this.dataset.currency;
            quickCurrencyBtns.forEach(function (b) { b.classList.remove('active'); });
            this.classList.add('active');
        });
    });

    if (currencySelect) {
        currencySelect.addEventListener('change', function () {
            quickCurrencyBtns.forEach(function (btn) {
                btn.classList.toggle('active', btn.dataset.currency === currencySelect.value);
            });
        });
    }

    // ── Check Now button ───────────────────────────────────────────────────
    var checkNowBtn = document.getElementById('checkNowBtn');
    if (checkNowBtn) {
        checkNowBtn.addEventListener('click', function () {
            var btn     = checkNowBtn;
            var origHtml = btn.innerHTML;
            btn.disabled  = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Checking...';

            fetch('{{ route('notifications.generateNow') }}', {
                method:  'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept':       'application/json',
                    'Content-Type': 'application/json',
                },
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                btn.disabled  = false;
                btn.innerHTML = origHtml;

                var alertClass = data.new_count > 0 ? 'alert-success' : 'alert-info';
                var icon       = data.new_count > 0 ? 'fa-circle-check' : 'fa-info-circle';
                var div        = document.createElement('div');
                div.className  = 'alert ' + alertClass + ' alert-dismissible fade show';
                div.role       = 'alert';
                div.innerHTML  = '<i class="fas ' + icon + '" style="flex-shrink:0;"></i>'
                               + '<span>' + data.message + '</span>'
                               + '<button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>';

                var container = document.querySelector('.page-header');
                if (container) container.insertAdjacentElement('afterend', div);

                if (data.new_count > 0) {
                    var badge = document.querySelector('.notif-badge');
                    if (badge) {
                        badge.textContent = data.new_count > 99 ? '99+' : data.new_count;
                    } else {
                        var bellBtn = document.getElementById('notifBellBtn');
                        if (bellBtn) {
                            var span = document.createElement('span');
                            span.className = 'notif-badge';
                            span.textContent = data.new_count;
                            bellBtn.appendChild(span);
                        }
                    }
                }

                setTimeout(function () {
                    if (div.isConnected) bootstrap.Alert.getOrCreateInstance(div).close();
                }, 6000);
            })
            .catch(function () {
                btn.disabled  = false;
                btn.innerHTML = origHtml;
                alert('Something went wrong. Please try again.');
            });
        });
    }
});

// ── Logo preview ────────────────────────────────────────────────────────────
function previewLogo(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function (e) {
            var preview = document.getElementById('logoPreview');
            var placeholder = document.getElementById('logoPlaceholder');
            preview.src = e.target.result;
            preview.style.display = 'block';
            if (placeholder) placeholder.style.display = 'none';
            document.getElementById('uploadConfirmArea').style.display = 'block';
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function cancelLogoSelect() {
    var input = document.getElementById('logoInput');
    input.value = '';
    document.getElementById('uploadConfirmArea').style.display = 'none';
}

// ── Color pickers ────────────────────────────────────────────────────────────
function syncColor(field, value) {
    var picker = document.getElementById(field + '_picker');
    var hex    = document.getElementById(field + '_hex');
    if (picker) picker.value = value;
    if (hex)    hex.value    = value;
    updateColorPreview();
}

function syncColorFromHex(field, value) {
    if (/^#[0-9A-Fa-f]{6}$/.test(value)) {
        var picker = document.getElementById(field + '_picker');
        if (picker) picker.value = value;
        updateColorPreview();
    }
}

function updateColorPreview() {
    var primary   = document.getElementById('primary_color_hex')   ? document.getElementById('primary_color_hex').value   : '#0B2A4A';
    var accent    = document.getElementById('accent_color_hex')    ? document.getElementById('accent_color_hex').value    : '#0E7490';
    var highlight = document.getElementById('highlight_color_hex') ? document.getElementById('highlight_color_hex').value : '#22D3EE';

    var header = document.getElementById('previewHeader');
    var bar    = document.getElementById('previewBar');
    var label  = document.getElementById('previewLabel');

    if (header) header.style.background = primary;
    if (bar)    bar.style.background    = accent;
    if (label)  label.style.color       = highlight;
}
</script>
@endsection
