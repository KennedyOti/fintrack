<?php

namespace App\Services;

use App\Mail\NotificationMail;
use App\Models\DebtsPayable;
use App\Models\Invoice;
use App\Models\Notification;
use App\Models\NotificationSetting;
use App\Models\Project;
use App\Models\Quote;
use App\Models\SavingsAccount;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Generate all notifications for a single user.
     */
    public function generateForUser(User $user): void
    {
        $this->checkInvoiceOverdue($user);
        $this->checkQuotesExpiring($user);
        $this->checkDebtsDue($user);
        $this->checkSavingsGoals($user);
        $this->checkProjectDeadlines($user);
    }

    /**
     * Get or create the notification settings for a user with sensible defaults.
     */
    public function getSettings(User $user): NotificationSetting
    {
        return NotificationSetting::firstOrCreate(
            ['user_id' => $user->id],
            NotificationSetting::defaults()
        );
    }

    /**
     * Check whether a notification for this entity was already sent TODAY
     * (prevents duplicate spam even if the previous one was marked as read).
     */
    private function notificationExists(User $user, string $type, int $relatedId, string $relatedType): bool
    {
        return Notification::where('user_id', $user->id)
            ->where('type', $type)
            ->where('related_id', $relatedId)
            ->where('related_type', $relatedType)
            ->whereDate('created_at', today())
            ->exists();
    }

    /**
     * Persist a notification and optionally fire an email.
     */
    private function createNotification(User $user, array $data, bool $sendEmail): Notification
    {
        $notification = Notification::create([
            'user_id'      => $user->id,
            'title'        => $data['title'],
            'message'      => $data['message'],
            'type'         => $data['type'],
            'related_id'   => $data['related_id'] ?? null,
            'related_type' => $data['related_type'] ?? null,
        ]);

        if ($sendEmail) {
            try {
                Mail::to($user->email)->send(new NotificationMail($notification, $user));
            } catch (\Exception $e) {
                Log::error('Notification email failed: ' . $e->getMessage(), [
                    'user_id'         => $user->id,
                    'notification_id' => $notification->id,
                ]);
            }
        }

        return $notification;
    }

    // ─────────────────────────────────────────────────────────────────────────
    //  Business rule checks
    // ─────────────────────────────────────────────────────────────────────────

    private function checkInvoiceOverdue(User $user): void
    {
        $settings = $this->getSettings($user);

        if (!$settings->invoice_overdue_app && !$settings->invoice_overdue_email) {
            return;
        }

        $overdueInvoices = Invoice::where('user_id', $user->id)
            ->where('due_date', '<', now()->startOfDay())
            ->whereNotIn('status', ['paid', 'cancelled'])
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($this->notificationExists($user, 'invoice_overdue', $invoice->id, Invoice::class)) {
                continue;
            }

            $daysOverdue = (int) now()->diffInDays($invoice->due_date);
            $outstanding = number_format($invoice->total_amount - $invoice->paid_amount, 2);

            $data = [
                'title'        => 'Invoice Overdue',
                'message'      => "Invoice #{$invoice->invoice_number} is {$daysOverdue} day(s) overdue. Outstanding balance: {$outstanding}.",
                'type'         => 'invoice_overdue',
                'related_id'   => $invoice->id,
                'related_type' => Invoice::class,
            ];

            if ($settings->invoice_overdue_app) {
                $this->createNotification($user, $data, $settings->invoice_overdue_email);
            } elseif ($settings->invoice_overdue_email) {
                // Email-only (no in-app record needed, but we still store it read)
                $n = $this->createNotification($user, $data, true);
                $n->update(['is_read' => true, 'read_at' => now()]);
            }
        }
    }

    private function checkQuotesExpiring(User $user): void
    {
        $settings = $this->getSettings($user);

        if (!$settings->quote_expiring_app && !$settings->quote_expiring_email) {
            return;
        }

        $days = max(1, (int) ($settings->quote_expiring_days ?? 3));

        $expiringQuotes = Quote::where('user_id', $user->id)
            ->where('valid_until', '>=', now()->startOfDay())
            ->where('valid_until', '<=', now()->addDays($days)->endOfDay())
            ->whereIn('status', ['draft', 'sent'])
            ->get();

        foreach ($expiringQuotes as $quote) {
            if ($this->notificationExists($user, 'quote_expiring', $quote->id, Quote::class)) {
                continue;
            }

            $daysLeft = (int) now()->diffInDays($quote->valid_until);
            $dayWord  = $daysLeft === 1 ? 'day' : 'days';

            $data = [
                'title'        => 'Quote Expiring Soon',
                'message'      => "Quote #{$quote->quote_number} expires in {$daysLeft} {$dayWord}. Follow up with your client to close the deal.",
                'type'         => 'quote_expiring',
                'related_id'   => $quote->id,
                'related_type' => Quote::class,
            ];

            if ($settings->quote_expiring_app) {
                $this->createNotification($user, $data, $settings->quote_expiring_email);
            } elseif ($settings->quote_expiring_email) {
                $n = $this->createNotification($user, $data, true);
                $n->update(['is_read' => true, 'read_at' => now()]);
            }
        }
    }

    private function checkDebtsDue(User $user): void
    {
        $settings = $this->getSettings($user);

        if (!$settings->debt_due_app && !$settings->debt_due_email) {
            return;
        }

        $days = max(1, (int) ($settings->debt_due_days ?? 3));

        $dueSoonDebts = DebtsPayable::where('user_id', $user->id)
            ->where('due_date', '>=', now()->startOfDay())
            ->where('due_date', '<=', now()->addDays($days)->endOfDay())
            ->whereIn('status', ['pending', 'partial'])
            ->get();

        foreach ($dueSoonDebts as $debt) {
            if ($this->notificationExists($user, 'debt_due', $debt->id, DebtsPayable::class)) {
                continue;
            }

            $daysLeft  = (int) now()->diffInDays($debt->due_date);
            $dayWord   = $daysLeft === 1 ? 'day' : 'days';
            $remaining = number_format($debt->original_amount - $debt->paid_amount, 2);

            $data = [
                'title'        => 'Debt Payment Due Soon',
                'message'      => "Payment of {$remaining} to {$debt->vendor_name} is due in {$daysLeft} {$dayWord}.",
                'type'         => 'debt_due',
                'related_id'   => $debt->id,
                'related_type' => DebtsPayable::class,
            ];

            if ($settings->debt_due_app) {
                $this->createNotification($user, $data, $settings->debt_due_email);
            } elseif ($settings->debt_due_email) {
                $n = $this->createNotification($user, $data, true);
                $n->update(['is_read' => true, 'read_at' => now()]);
            }
        }
    }

    private function checkSavingsGoals(User $user): void
    {
        $settings = $this->getSettings($user);

        if (!$settings->savings_goal_app && !$settings->savings_goal_email) {
            return;
        }

        $reachedAccounts = SavingsAccount::where('user_id', $user->id)
            ->where('status', 'active')
            ->where('target_amount', '>', 0)
            ->whereColumn('current_balance', '>=', 'target_amount')
            ->get();

        foreach ($reachedAccounts as $account) {
            if ($this->notificationExists($user, 'savings_goal', $account->id, SavingsAccount::class)) {
                continue;
            }

            $target = number_format($account->target_amount, 2);

            $data = [
                'title'        => 'Savings Goal Reached!',
                'message'      => "Congratulations! Your savings account \"{$account->name}\" has reached its target of {$target}.",
                'type'         => 'savings_goal',
                'related_id'   => $account->id,
                'related_type' => SavingsAccount::class,
            ];

            if ($settings->savings_goal_app) {
                $this->createNotification($user, $data, $settings->savings_goal_email);
            } elseif ($settings->savings_goal_email) {
                $n = $this->createNotification($user, $data, true);
                $n->update(['is_read' => true, 'read_at' => now()]);
            }
        }
    }

    private function checkProjectDeadlines(User $user): void
    {
        $settings = $this->getSettings($user);

        if (!$settings->project_deadline_app && !$settings->project_deadline_email) {
            return;
        }

        $days = max(1, (int) ($settings->project_deadline_days ?? 7));

        $approachingProjects = Project::where('user_id', $user->id)
            ->where('deadline', '>=', now()->startOfDay())
            ->where('deadline', '<=', now()->addDays($days)->endOfDay())
            ->whereIn('status', ['planned', 'in_progress'])
            ->get();

        foreach ($approachingProjects as $project) {
            if ($this->notificationExists($user, 'project_deadline', $project->id, Project::class)) {
                continue;
            }

            $daysLeft = (int) now()->diffInDays($project->deadline);
            $dayWord  = $daysLeft === 1 ? 'day' : 'days';
            $progress = $project->progress_percent ?? 0;

            $data = [
                'title'        => 'Project Deadline Approaching',
                'message'      => "Project \"{$project->title}\" deadline is in {$daysLeft} {$dayWord}. Progress: {$progress}%.",
                'type'         => 'project_deadline',
                'related_id'   => $project->id,
                'related_type' => Project::class,
            ];

            if ($settings->project_deadline_app) {
                $this->createNotification($user, $data, $settings->project_deadline_email);
            } elseif ($settings->project_deadline_email) {
                $n = $this->createNotification($user, $data, true);
                $n->update(['is_read' => true, 'read_at' => now()]);
            }
        }
    }
}
