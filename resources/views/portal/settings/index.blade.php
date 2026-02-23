@extends('layouts.portal')

@section('title', 'Settings - FinTrack')

@section('content')
<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0">Settings</h4>
        <p class="text-muted mb-0">Manage your account and preferences</p>
    </div>
</div>

<div class="row">
    <div class="col-lg-3 mb-4">
        <!-- Settings Navigation -->
        <div class="card border-0 shadow-sm">
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="#profile" class="list-group-item list-group-item-action active" data-bs-toggle="tab">
                        <i class="fas fa-user me-2"></i> Profile
                    </a>
                    <a href="#business" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-building me-2"></i> Business
                    </a>
                    <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-cog me-2"></i> Preferences
                    </a>
                    <a href="#currency" class="list-group-item list-group-item-action" data-bs-toggle="tab">
                        <i class="fas fa-coins me-2"></i> Currency
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-9">
        <div class="tab-content">
            <!-- Profile Settings -->
            <div class="tab-pane fade show active" id="profile">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-user me-2"></i>Profile Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="text" class="form-control" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" placeholder="+1 234 567 8900">
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
            
            <!-- Business Settings -->
            <div class="tab-pane fade" id="business">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Business Information</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="mb-3">
                                <label for="business_name" class="form-label">Business Name</label>
                                <input type="text" class="form-control" id="business_name" name="business_name" value="{{ old('business_name', $user->business_name) }}" placeholder="Your Business Name">
                            </div>
                            
                            <div class="mb-3">
                                <label for="tax_number" class="form-label">Tax Number / VAT ID</label>
                                <input type="text" class="form-control" id="tax_number" name="tax_number" value="{{ old('tax_number', $user->tax_number) }}" placeholder="XX-XXXXXXX">
                            </div>
                            
                            <div class="mb-3">
                                <label for="business_address" class="form-label">Business Address</label>
                                <textarea class="form-control" id="business_address" name="business_address" rows="3" placeholder="Enter your business address">{{ old('business_address', $user->business_address) }}</textarea>
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
            
            <!-- Preferences -->
            <div class="tab-pane fade" id="preferences">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Regional Preferences</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('settings.update') }}" method="POST">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="timezone" class="form-label">Timezone</label>
                                    <select class="form-select" id="timezone" name="timezone" required>
                                        <option value="UTC" {{ old('timezone', $user->timezone ?? 'UTC') == 'UTC' ? 'selected' : '' }}>UTC</option>
                                        <option value="Africa/Nairobi" {{ old('timezone', $user->timezone) == 'Africa/Nairobi' ? 'selected' : '' }}>Africa/Nairobi (EAT)</option>
                                        <option value="Africa/Lagos" {{ old('timezone', $user->timezone) == 'Africa/Lagos' ? 'selected' : '' }}>Africa/Lagos (WAT)</option>
                                        <option value="Africa/Johannesburg" {{ old('timezone', $user->timezone) == 'Africa/Johannesburg' ? 'selected' : '' }}>Africa/Johannesburg (SAST)</option>
                                        <option value="Africa/Cairo" {{ old('timezone', $user->timezone) == 'Africa/Cairo' ? 'selected' : '' }}>Africa/Cairo (EET)</option>
                                        <option value="America/New_York" {{ old('timezone', $user->timezone) == 'America/New_York' ? 'selected' : '' }}>America/New_York (EST)</option>
                                        <option value="America/Los_Angeles" {{ old('timezone', $user->timezone) == 'America/Los_Angeles' ? 'selected' : '' }}>America/Los_Angeles (PST)</option>
                                        <option value="America/Chicago" {{ old('timezone', $user->timezone) == 'America/Chicago' ? 'selected' : '' }}>America/Chicago (CST)</option>
                                        <option value="Europe/London" {{ old('timezone', $user->timezone) == 'Europe/London' ? 'selected' : '' }}>Europe/London (GMT)</option>
                                        <option value="Europe/Paris" {{ old('timezone', $user->timezone) == 'Europe/Paris' ? 'selected' : '' }}>Europe/Paris (CET)</option>
                                        <option value="Europe/Berlin" {{ old('timezone', $user->timezone) == 'Europe/Berlin' ? 'selected' : '' }}>Europe/Berlin (CET)</option>
                                        <option value="Asia/Dubai" {{ old('timezone', $user->timezone) == 'Asia/Dubai' ? 'selected' : '' }}>Asia/Dubai (GST)</option>
                                        <option value="Asia/Singapore" {{ old('timezone', $user->timezone) == 'Asia/Singapore' ? 'selected' : '' }}>Asia/Singapore (SGT)</option>
                                        <option value="Asia/Tokyo" {{ old('timezone', $user->timezone) == 'Asia/Tokyo' ? 'selected' : '' }}>Asia/Tokyo (JST)</option>
                                        <option value="Asia/Kolkata" {{ old('timezone', $user->timezone) == 'Asia/Kolkata' ? 'selected' : '' }}>Asia/Kolkata (IST)</option>
                                        <option value="Australia/Sydney" {{ old('timezone', $user->timezone) == 'Australia/Sydney' ? 'selected' : '' }}>Australia/Sydney (AEST)</option>
                                    </select>
                                    <small class="text-muted">This affects how dates and times are displayed</small>
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
            
            <!-- Currency Settings -->
            <div class="tab-pane fade" id="currency">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3">
                        <h5 class="mb-0"><i class="fas fa-coins me-2"></i>Currency Settings</h5>
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
                                        <option value="{{ $code }}" {{ old('currency_code', $user->currency_code ?? 'USD') == $code ? 'selected' : '' }}>
                                            {{ $details['symbol'] }} {{ $code }} - {{ $details['name'] }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <!-- Currency Preview -->
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
                            
                            <!-- Quick Currency Select -->
                            <div class="mb-4">
                                <label class="form-label">Popular Currencies</label>
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach(['USD', 'EUR', 'GBP', 'KES', 'NGN', 'ZAR', 'INR', 'JPY'] as $code)
                                        <button type="button" 
                                                class="btn btn-outline-secondary quick-currency" 
                                                data-currency="{{ $code }}"
                                                {{ old('currency_code', $user->currency_code ?? 'USD') == $code ? 'active' : '' }}>
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
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Quick currency selection buttons
    const quickCurrencyBtns = document.querySelectorAll('.quick-currency');
    const currencySelect = document.getElementById('currency_code');
    
    quickCurrencyBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            currencySelect.value = this.dataset.currency;
            
            // Update active state
            quickCurrencyBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
        });
    });
    
    // Update preview when currency changes
    currencySelect.addEventListener('change', function() {
        // Update quick select buttons
        quickCurrencyBtns.forEach(btn => {
            if (btn.dataset.currency === this.value) {
                btn.classList.add('active');
            } else {
                btn.classList.remove('active');
            }
        });
    });
});
</script>
@endsection
