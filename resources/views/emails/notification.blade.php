<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
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
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(15, 30, 60, 0.10);
        }
        .email-header {
            background: linear-gradient(135deg, #0B2A4A 0%, #0E7490 100%);
            padding: 28px 32px;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .email-brand {
            font-size: 22px;
            font-weight: 800;
            color: #ffffff;
            letter-spacing: -0.5px;
        }
        .email-brand span { color: #22D3EE; }
        .email-body { padding: 32px; }
        .greeting {
            font-size: 16px;
            font-weight: 600;
            color: #0F172A;
            margin-bottom: 20px;
        }
        .notif-box {
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 24px;
            display: flex;
            gap: 16px;
            align-items: flex-start;
        }
        .notif-icon-wrap {
            width: 48px;
            height: 48px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            font-size: 20px;
        }
        .notif-content { flex: 1; }
        .notif-title {
            font-size: 16px;
            font-weight: 700;
            color: #0F172A;
            margin-bottom: 6px;
        }
        .notif-message {
            font-size: 14px;
            color: #334155;
            line-height: 1.6;
        }
        .cta-section { text-align: center; margin-bottom: 28px; }
        .cta-btn {
            display: inline-block;
            background: linear-gradient(135deg, #0B2A4A, #0E7490);
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 28px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            letter-spacing: 0.3px;
        }
        .divider {
            border: none;
            border-top: 1px solid #E2E8F0;
            margin: 24px 0;
        }
        .help-text {
            font-size: 13px;
            color: #64748B;
            margin-bottom: 8px;
        }
        .email-footer {
            background: #F8FAFC;
            padding: 20px 32px;
            border-top: 1px solid #E2E8F0;
            text-align: center;
        }
        .footer-text { font-size: 12px; color: #94A3B8; margin-bottom: 6px; }
        .footer-link { color: #0E7490; text-decoration: none; font-size: 12px; }
        .settings-link { font-size: 12px; color: #94A3B8; margin-top: 8px; }
        .settings-link a { color: #0E7490; text-decoration: none; }

        /* Type-specific colours */
        .type-invoice_overdue  { background: rgba(244, 63, 94, 0.08); border-left: 4px solid #F43F5E; }
        .type-quote_expiring   { background: rgba(245, 158, 11, 0.08); border-left: 4px solid #F59E0B; }
        .type-debt_due         { background: rgba(249, 115, 22, 0.08); border-left: 4px solid #F97316; }
        .type-savings_goal     { background: rgba(34, 197, 94, 0.08);  border-left: 4px solid #22C55E; }
        .type-project_deadline { background: rgba(139, 92, 246, 0.08); border-left: 4px solid #8B5CF6; }
        .type-budget_alert     { background: rgba(239, 68, 68, 0.08);  border-left: 4px solid #EF4444; }
        .type-payment_received { background: rgba(16, 185, 129, 0.08); border-left: 4px solid #10B981; }
        .type-system           { background: rgba(14, 116, 144, 0.08); border-left: 4px solid #0E7490; }

        .icon-invoice_overdue  { background: rgba(244, 63, 94, 0.15);  color: #F43F5E; }
        .icon-quote_expiring   { background: rgba(245, 158, 11, 0.15); color: #F59E0B; }
        .icon-debt_due         { background: rgba(249, 115, 22, 0.15); color: #F97316; }
        .icon-savings_goal     { background: rgba(34, 197, 94, 0.15);  color: #22C55E; }
        .icon-project_deadline { background: rgba(139, 92, 246, 0.15); color: #8B5CF6; }
        .icon-budget_alert     { background: rgba(239, 68, 68, 0.15);  color: #EF4444; }
        .icon-payment_received { background: rgba(16, 185, 129, 0.15); color: #10B981; }
        .icon-system           { background: rgba(14, 116, 144, 0.15); color: #0E7490; }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-card">

        <!-- Header -->
        <div class="email-header">
            <div class="email-brand">Fin<span>Track</span></div>
        </div>

        <!-- Body -->
        <div class="email-body">

            <p class="greeting">Hello, {{ $recipient->name }}!</p>

            <p class="help-text" style="margin-bottom:20px;">
                You have a new notification from FinTrack that requires your attention.
            </p>

            <!-- Notification Card -->
            <div class="notif-box type-{{ $notification->type }}">
                <div class="notif-icon-wrap icon-{{ $notification->type }}">
                    @switch($notification->type)
                        @case('invoice_overdue')   📄 @break
                        @case('quote_expiring')    📋 @break
                        @case('debt_due')          🤝 @break
                        @case('savings_goal')      🐷 @break
                        @case('project_deadline')  📊 @break
                        @case('budget_alert')      ⚠️ @break
                        @case('payment_received')  ✅ @break
                        @default                   🔔
                    @endswitch
                </div>
                <div class="notif-content">
                    <div class="notif-title">{{ $notification->title }}</div>
                    <div class="notif-message">{{ $notification->message }}</div>
                </div>
            </div>

            <!-- CTA -->
            <div class="cta-section">
                <a href="{{ url('/portal/dashboard') }}" class="cta-btn">
                    View in FinTrack Dashboard
                </a>
            </div>

            <hr class="divider">

            <p class="help-text">
                This notification was generated on {{ $notification->created_at->format('F j, Y \a\t g:i A') }}.
                Log in to your FinTrack account to take action.
            </p>

        </div>

        <!-- Footer -->
        <div class="email-footer">
            <p class="footer-text">&copy; {{ date('Y') }} FinTrack. All rights reserved.</p>
            <p class="footer-text">
                You're receiving this because you have email notifications enabled.
            </p>
            <p class="settings-link">
                <a href="{{ url('/settings') }}">Manage notification preferences</a>
                &nbsp;·&nbsp;
                <a href="{{ url('/portal/dashboard') }}">Go to Dashboard</a>
            </p>
        </div>

    </div>
</div>
</body>
</html>
