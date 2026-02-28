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
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone"
                                           value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-building me-2" style="color:var(--ft-teal);"></i>Business Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control" id="business_name" name="business_name"
                                       value="{{ old('business_name', $user->business_name) }}" placeholder="Your Business Name">
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
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
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

                            {{-- How it works banner --}}
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

                            {{-- Table header --}}
                            <div class="notif-settings-header">
                                <div class="notif-settings-label">Alert Type</div>
                                <div class="notif-settings-col text-center">In-App</div>
                                <div class="notif-settings-col text-center">Email</div>
                                <div class="notif-settings-col text-center">Timing</div>
                            </div>

                            {{-- ── Invoice Overdue ── --}}
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

                            {{-- ── Quote Expiring ── --}}
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

                            {{-- ── Debt Due ── --}}
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

                            {{-- ── Savings Goal ── --}}
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

                            {{-- ── Project Deadline ── --}}
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

                            {{-- ── Budget Alert ── --}}
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

                // Show a toast-style alert
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

                // Refresh bell badge
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
</script>
@endsection
