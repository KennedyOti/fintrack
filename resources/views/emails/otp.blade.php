<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your FinTrack Login Code</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            font-size: 14px;
            background-color: #F1F5F9;
            color: #334155;
            line-height: 1.6;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 32px auto;
            padding: 0 16px;
        }
        .email-card {
            background: #ffffff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 4px 24px rgba(15, 30, 60, 0.12);
        }
        /* Header */
        .email-header {
            background: linear-gradient(135deg, #0B2A4A 0%, #0E7490 100%);
            padding: 32px 40px;
            text-align: center;
        }
        .email-brand {
            font-size: 26px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
            margin-bottom: 4px;
        }
        .email-brand span { color: #22D3EE; }
        .email-tagline {
            color: rgba(255, 255, 255, 0.65);
            font-size: 12px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        /* Shield icon */
        .shield-wrap {
            width: 72px;
            height: 72px;
            background: rgba(255, 255, 255, 0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 24px auto 0;
            border: 2px solid rgba(255, 255, 255, 0.2);
        }
        .shield-wrap svg {
            width: 36px;
            height: 36px;
            fill: #22D3EE;
        }
        /* Body */
        .email-body {
            padding: 40px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 10px;
        }
        .intro {
            color: #64748B;
            font-size: 14px;
            margin-bottom: 32px;
            line-height: 1.7;
        }
        /* OTP Box */
        .otp-container {
            background: linear-gradient(135deg, #F0F9FF 0%, #E0F2FE 100%);
            border: 2px solid #BAE6FD;
            border-radius: 16px;
            padding: 32px;
            text-align: center;
            margin-bottom: 28px;
        }
        .otp-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #0E7490;
            margin-bottom: 14px;
        }
        .otp-code {
            font-family: 'Courier New', 'Lucida Console', monospace;
            font-size: 44px;
            font-weight: 800;
            letter-spacing: 12px;
            color: #0B2A4A;
            text-indent: 12px;  /* compensate for letter-spacing on last char */
            display: block;
            line-height: 1;
        }
        .otp-expiry {
            margin-top: 14px;
            font-size: 12px;
            color: #64748B;
        }
        .otp-expiry strong {
            color: #0E7490;
        }
        /* Timer bar */
        .timer-bar {
            height: 4px;
            background: #E2E8F0;
            border-radius: 2px;
            margin-top: 16px;
            overflow: hidden;
        }
        .timer-fill {
            height: 100%;
            width: 100%;
            background: linear-gradient(90deg, #0B2A4A, #0E7490);
            border-radius: 2px;
        }
        /* Steps */
        .steps-section {
            margin-bottom: 28px;
        }
        .steps-label {
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            color: #94A3B8;
            margin-bottom: 12px;
        }
        .step {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 10px;
        }
        .step-num {
            width: 22px;
            height: 22px;
            background: #0B2A4A;
            color: #fff;
            border-radius: 50%;
            font-size: 11px;
            font-weight: 700;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }
        .step-text {
            font-size: 13px;
            color: #475569;
            line-height: 1.5;
        }
        /* Security notice */
        .security-notice {
            background: #FFF7ED;
            border: 1px solid #FED7AA;
            border-left: 4px solid #F59E0B;
            border-radius: 8px;
            padding: 14px 16px;
            margin-bottom: 28px;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }
        .notice-icon {
            font-size: 16px;
            flex-shrink: 0;
            line-height: 1.4;
        }
        .notice-text {
            font-size: 12.5px;
            color: #92400E;
            line-height: 1.5;
        }
        .notice-text strong { color: #78350F; }
        /* Footer */
        .email-footer {
            background: #F8FAFC;
            border-top: 1px solid #E2E8F0;
            padding: 24px 40px;
            text-align: center;
        }
        .footer-brand {
            font-size: 15px;
            font-weight: 700;
            color: #0B2A4A;
            margin-bottom: 4px;
        }
        .footer-brand span { color: #0E7490; }
        .footer-text {
            font-size: 11.5px;
            color: #94A3B8;
            line-height: 1.6;
        }
        .footer-text a {
            color: #0E7490;
            text-decoration: none;
        }
        .divider {
            height: 1px;
            background: #E2E8F0;
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-card">

        <!-- Header -->
        <div class="email-header">
            <div class="email-brand">Fin<span>Track</span></div>
            <div class="email-tagline">Financial Management Platform</div>
            <div class="shield-wrap">
                <!-- Shield icon SVG -->
                <svg viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm-1 14l-3-3 1.41-1.41L11 12.17l4.59-4.58L17 9l-6 6z"/>
                </svg>
            </div>
        </div>

        <!-- Body -->
        <div class="email-body">
            <div class="greeting">Hello, {{ $user->name }} 👋</div>
            <p class="intro">
                We received a login request for your FinTrack account. Use the verification
                code below to complete your sign-in. This code is valid for
                <strong>{{ $expiresInMinutes }} minutes</strong>.
            </p>

            <!-- OTP Display -->
            <div class="otp-container">
                <div class="otp-label">Your Verification Code</div>
                <span class="otp-code">{{ $otpCode }}</span>
                <div class="otp-expiry">
                    Expires in <strong>{{ $expiresInMinutes }} minutes</strong> &bull;
                    {{ now()->addMinutes($expiresInMinutes)->format('H:i') }} {{ config('app.timezone', 'UTC') }}
                </div>
                <div class="timer-bar">
                    <div class="timer-fill"></div>
                </div>
            </div>

            <!-- Steps -->
            <div class="steps-section">
                <div class="steps-label">How to use this code</div>
                <div class="step">
                    <div class="step-num">1</div>
                    <div class="step-text">Return to the FinTrack login verification page in your browser.</div>
                </div>
                <div class="step">
                    <div class="step-num">2</div>
                    <div class="step-text">Enter the 6-digit code exactly as shown above into the verification boxes.</div>
                </div>
                <div class="step">
                    <div class="step-num">3</div>
                    <div class="step-text">Click <strong>"Verify Code"</strong> to complete your sign-in securely.</div>
                </div>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <div class="notice-icon">⚠️</div>
                <div class="notice-text">
                    <strong>Didn't request this code?</strong> Someone may be attempting to access your account.
                    You can safely ignore this email — your account is protected. Consider changing your
                    password immediately if this is unexpected.
                </div>
            </div>

            <div class="divider"></div>
            <p style="font-size:12px; color:#94A3B8; text-align:center;">
                For security, never share this code with anyone. FinTrack will never ask for your code by phone or email.
            </p>
        </div>

        <!-- Footer -->
        <div class="email-footer">
            <div class="footer-brand">Fin<span>Track</span></div>
            <div class="footer-text">
                &copy; {{ date('Y') }} FinTrack. All rights reserved.<br>
                This is an automated security email. Please do not reply.
            </div>
        </div>

    </div>
</div>
</body>
</html>
