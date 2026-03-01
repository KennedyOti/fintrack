@extends('layouts.app')

@section('title', 'Verify Your Identity — FinTrack')

@section('content')
<div class="auth-page auth-page-only">
    <div class="auth-card two-factor-card">

        {{-- Header --}}
        <div class="auth-card-header">
            <div class="brand">
                <img src="{{ asset('assets/images/logo.png') }}" alt="FinTrack">
            </div>
            <div class="tf-shield-badge">
                <i class="fas fa-shield-halved"></i>
            </div>
            <h4>Two-Step Verification</h4>
            <p>We sent a 6-digit code to <strong>{{ $maskedEmail }}</strong></p>
        </div>

        {{-- Body --}}
        <div class="auth-card-body">

            @if ($errors->any())
                <div class="tf-alert tf-alert-danger">
                    <i class="fas fa-circle-xmark"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            @if (session('status'))
                <div class="tf-alert tf-alert-success">
                    <i class="fas fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            {{-- Timer bar --}}
            <div class="tf-timer-section">
                <div class="tf-timer-label">
                    <i class="fas fa-clock"></i>
                    <span>Code expires in <span id="timerDisplay" class="tf-timer-value">10:00</span></span>
                </div>
                <div class="tf-timer-bar">
                    <div class="tf-timer-fill" id="timerFill"></div>
                </div>
            </div>

            {{-- OTP Form --}}
            <form method="POST" action="{{ route('two-factor.store') }}" id="otpForm" class="auth-form" novalidate>
                @csrf

                {{-- Hidden field populated by JS before every submit --}}
                <input type="hidden" name="otp" id="otpHidden" value="{{ old('otp') }}">

                <div class="tf-otp-section">
                    <label class="tf-otp-label">Enter your verification code</label>

                    <div class="tf-otp-inputs" id="otpInputs">
                        @for ($i = 0; $i < 6; $i++)
                            <input
                                type="tel"
                                inputmode="numeric"
                                maxlength="1"
                                pattern="[0-9]"
                                class="tf-otp-digit @error('otp') tf-otp-digit--error @enderror"
                                data-index="{{ $i }}"
                                autocomplete="{{ $i === 0 ? 'one-time-code' : 'off' }}"
                                aria-label="Digit {{ $i + 1 }} of 6"
                            >
                        @endfor
                    </div>

                    @error('otp')
                        <div class="tf-otp-error" id="otpError">
                            <i class="fas fa-triangle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn auth-btn auth-btn-primary w-100 tf-verify-btn" id="verifyBtn">
                    <span class="tf-btn-text">
                        <i class="fas fa-shield-halved"></i> Verify &amp; Sign In
                    </span>
                    <span class="tf-btn-loading d-none">
                        <span class="tf-spinner"></span> Verifying&hellip;
                    </span>
                </button>
            </form>

            {{-- Resend section --}}
            <div class="tf-resend-section">
                <span class="tf-resend-text">Didn't receive the code?</span>
                <button type="button" id="resendBtn" class="tf-resend-btn" disabled>
                    Resend code <span id="resendCountdown" class="tf-countdown">(60s)</span>
                </button>
            </div>

            <div class="tf-security-note">
                <i class="fas fa-lock"></i>
                <span>Never share this code with anyone. FinTrack will never ask for it.</span>
            </div>

            <div class="tf-back-link">
                <a href="{{ route('login') }}">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            </div>

        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── DOM refs ────────────────────────────────────────────────────────
    var digits      = Array.from(document.querySelectorAll('.tf-otp-digit'));
    var hiddenInput = document.getElementById('otpHidden');
    var otpForm     = document.getElementById('otpForm');
    var verifyBtn   = document.getElementById('verifyBtn');
    var resendBtn   = document.getElementById('resendBtn');
    var resendCD    = document.getElementById('resendCountdown');
    var timerDisp   = document.getElementById('timerDisplay');
    var timerFill   = document.getElementById('timerFill');

    var EXPIRY_SECONDS  = {{ $expiresAt ? max(0, (int) now()->diffInSeconds($expiresAt, false)) : 600 }};
    var RESEND_COOLDOWN = 60;

    // ── Sync all digit values into the hidden input ─────────────────────
    function syncHidden() {
        hiddenInput.value = digits.map(function(d) { return d.value; }).join('');
    }

    // ── Focus helper ───────────────────────────────────────────────────
    function moveFocus(index) {
        if (index >= 0 && index < digits.length) {
            digits[index].focus();
            digits[index].select();
        }
    }

    // ── Wire up each digit box ─────────────────────────────────────────
    digits.forEach(function(input, idx) {

        // Select all text on focus
        input.addEventListener('focus', function() {
            setTimeout(function() { input.select(); }, 0);
        });

        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                e.preventDefault();
                if (input.value !== '') {
                    input.value = '';
                    input.classList.remove('tf-otp-digit--filled');
                    syncHidden();
                } else {
                    moveFocus(idx - 1);
                }
                return;
            }
            if (e.key === 'ArrowLeft')  { e.preventDefault(); moveFocus(idx - 1); return; }
            if (e.key === 'ArrowRight') { e.preventDefault(); moveFocus(idx + 1); return; }
            if (e.key === 'Enter')      { e.preventDefault(); submitForm(); return; }
            // Allow only digit keys — block letters etc.
            if (e.key.length === 1 && !/[0-9]/.test(e.key)) {
                e.preventDefault();
            }
        });

        input.addEventListener('input', function() {
            // Strip non-digits, keep only last character
            var val = input.value.replace(/\D/g, '');
            input.value = val ? val.slice(-1) : '';
            input.classList.toggle('tf-otp-digit--filled', input.value !== '');
            syncHidden();
            if (input.value !== '') {
                moveFocus(idx + 1);
            }
            if (isComplete()) {
                setTimeout(submitForm, 100);
            }
        });

        // Handle paste (paste full code into first box)
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            var pasted = (e.clipboardData || window.clipboardData)
                .getData('text').replace(/\D/g, '').slice(0, 6);
            if (!pasted) return;
            pasted.split('').forEach(function(ch, i) {
                if (digits[i]) {
                    digits[i].value = ch;
                    digits[i].classList.add('tf-otp-digit--filled');
                }
            });
            syncHidden();
            var nextEmpty = Math.min(pasted.length, 5);
            moveFocus(nextEmpty);
            if (pasted.length === 6) {
                setTimeout(submitForm, 150);
            }
        });
    });

    function isComplete() {
        return hiddenInput.value.length === 6 && /^\d{6}$/.test(hiddenInput.value);
    }

    // ── Form submit ────────────────────────────────────────────────────
    // Always sync before any submit (covers both manual button click and auto-submit)
    otpForm.addEventListener('submit', function() {
        syncHidden();
        if (isComplete()) {
            showLoading();
        }
    });

    function submitForm() {
        syncHidden();
        if (!isComplete()) return;
        showLoading();
        otpForm.submit();
    }

    function showLoading() {
        var btnText = verifyBtn.querySelector('.tf-btn-text');
        var btnLoad = verifyBtn.querySelector('.tf-btn-loading');
        if (btnText) btnText.classList.add('d-none');
        if (btnLoad) btnLoad.classList.remove('d-none');
        verifyBtn.disabled = true;
    }

    // Focus first digit on load
    moveFocus(0);

    // ── Expiry countdown timer ──────────────────────────────────────────
    var remaining = EXPIRY_SECONDS;
    var total     = remaining;

    function formatTime(s) {
        var m   = String(Math.floor(s / 60)).padStart(2, '0');
        var sec = String(s % 60).padStart(2, '0');
        return m + ':' + sec;
    }

    function updateTimer() {
        if (!timerDisp || !timerFill) return;
        timerDisp.textContent = remaining > 0 ? formatTime(remaining) : 'Expired';
        var pct = total > 0 ? (remaining / total) * 100 : 0;
        timerFill.style.width = pct + '%';

        timerDisp.className = 'tf-timer-value';
        timerFill.className = 'tf-timer-fill';

        if (remaining <= 60) {
            timerDisp.classList.add('tf-timer-value--danger');
            timerFill.classList.add('tf-timer-fill--danger');
        } else if (remaining <= 120) {
            timerDisp.classList.add('tf-timer-value--warning');
            timerFill.classList.add('tf-timer-fill--warning');
        }

        if (remaining <= 0) {
            clearInterval(timerInterval);
            verifyBtn.disabled = true;
            var btnText = verifyBtn.querySelector('.tf-btn-text');
            if (btnText) btnText.innerHTML = '<i class="fas fa-clock"></i> Code Expired — Request New';
            insertAlert('danger', 'Your code has expired. Please click <strong>Resend code</strong> to get a new one.');
        }
    }

    var timerInterval = setInterval(function() { remaining--; updateTimer(); }, 1000);
    updateTimer();

    // ── Resend countdown ────────────────────────────────────────────────
    var resendRemaining = RESEND_COOLDOWN;

    function tickResend() {
        if (resendRemaining <= 0) {
            clearInterval(resendInterval);
            resendBtn.disabled = false;
            resendCD.textContent = '';
            return;
        }
        resendCD.textContent = '(' + resendRemaining + 's)';
        resendRemaining--;
    }

    var resendInterval = setInterval(tickResend, 1000);
    tickResend();

    resendBtn.addEventListener('click', function() {
        resendBtn.disabled = true;
        resendBtn.innerHTML = 'Sending&hellip;';

        fetch('{{ route("two-factor.resend") }}', {
            method : 'POST',
            headers: {
                'X-CSRF-TOKEN' : document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept'       : 'application/json',
                'Content-Type' : 'application/json',
            },
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.success) {
                // Reset expiry timer
                remaining = data.expires_in || 600;
                total     = remaining;
                clearInterval(timerInterval);
                timerInterval = setInterval(function() { remaining--; updateTimer(); }, 1000);
                updateTimer();

                // Clear digit inputs
                digits.forEach(function(d) {
                    d.value = '';
                    d.disabled = false;
                    d.classList.remove('tf-otp-digit--filled', 'tf-otp-digit--error');
                });
                hiddenInput.value = '';
                verifyBtn.disabled = false;
                var btnText = verifyBtn.querySelector('.tf-btn-text');
                var btnLoad = verifyBtn.querySelector('.tf-btn-loading');
                if (btnText) { btnText.innerHTML = '<i class="fas fa-shield-halved"></i> Verify &amp; Sign In'; btnText.classList.remove('d-none'); }
                if (btnLoad) btnLoad.classList.add('d-none');

                // Restart resend cooldown
                resendRemaining = RESEND_COOLDOWN;
                resendBtn.innerHTML = 'Resend code <span id="resendCountdown" class="tf-countdown">(' + resendRemaining + 's)</span>';
                resendCD = resendBtn.querySelector('.tf-countdown');
                clearInterval(resendInterval);
                resendInterval = setInterval(function() {
                    if (resendRemaining <= 0) {
                        clearInterval(resendInterval);
                        resendBtn.disabled = false;
                        if (resendCD) resendCD.textContent = '';
                        return;
                    }
                    if (resendCD) resendCD.textContent = '(' + resendRemaining + 's)';
                    resendRemaining--;
                }, 1000);

                showToast('success', data.message);
                moveFocus(0);
            } else {
                var wait = data.wait || RESEND_COOLDOWN;
                resendRemaining = wait;
                resendBtn.innerHTML = 'Resend code <span class="tf-countdown">(' + wait + 's)</span>';
                resendCD = resendBtn.querySelector('.tf-countdown');
                clearInterval(resendInterval);
                resendInterval = setInterval(function() {
                    if (resendRemaining <= 0) {
                        clearInterval(resendInterval);
                        resendBtn.disabled = false;
                        if (resendCD) resendCD.textContent = '';
                        return;
                    }
                    if (resendCD) resendCD.textContent = '(' + resendRemaining + 's)';
                    resendRemaining--;
                }, 1000);
                showToast('error', data.message || 'Failed to resend code.');
            }
        })
        .catch(function() {
            resendBtn.disabled = false;
            resendBtn.innerHTML = 'Resend code';
            showToast('error', 'Network error. Please try again.');
        });
    });

    // ── Toast ───────────────────────────────────────────────────────────
    function showToast(type, message) {
        var toast = document.createElement('div');
        toast.className = 'tf-toast tf-toast--' + type;
        toast.innerHTML = '<i class="fas fa-' + (type === 'success' ? 'circle-check' : 'circle-xmark') + '"></i><span>' + message + '</span>';
        document.body.appendChild(toast);
        requestAnimationFrame(function() { toast.classList.add('tf-toast--visible'); });
        setTimeout(function() {
            toast.classList.remove('tf-toast--visible');
            setTimeout(function() { toast.remove(); }, 400);
        }, 3500);
    }

    function insertAlert(type, html) {
        var existing = document.querySelector('.tf-alert');
        if (existing) existing.remove();
        var alert = document.createElement('div');
        alert.className = 'tf-alert tf-alert-' + type;
        alert.innerHTML = '<i class="fas fa-circle-xmark"></i><span>' + html + '</span>';
        otpForm.insertAdjacentElement('beforebegin', alert);
    }

});
</script>
@endsection
