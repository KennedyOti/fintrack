<?php

namespace App\Console\Commands;

use App\Models\Expense;
use App\Models\Income;
use App\Models\RecurringTransaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateRecurringTransactions extends Command
{
    protected $signature   = 'recurring:generate {--id= : Only process a specific recurring transaction ID}';
    protected $description = 'Auto-generate expense and income records for due recurring transactions';

    public function handle(): int
    {
        $today = Carbon::today();
        $specificId = $this->option('id');

        $query = RecurringTransaction::where('is_active', true)
            ->where('next_due_date', '<=', $today);

        if ($specificId) {
            $query->where('id', $specificId);
        }

        $due = $query->get();

        if ($due->isEmpty()) {
            $this->info('No recurring transactions are due.');
            return self::SUCCESS;
        }

        $generated = 0;
        $skipped   = 0;

        foreach ($due as $recurring) {
            // Skip if past end_date
            if ($recurring->end_date && $recurring->next_due_date->gt($recurring->end_date)) {
                $recurring->update(['is_active' => false]);
                $skipped++;
                continue;
            }

            try {
                DB::transaction(function () use ($recurring, $today) {
                    $note = '[Auto] ' . $recurring->name;

                    if ($recurring->type === 'expense') {
                        Expense::create([
                            'user_id'          => $recurring->user_id,
                            'category_id'      => $recurring->category_id,
                            'project_id'       => $recurring->project_id,
                            'vendor_name'      => $recurring->vendor_name,
                            'amount'           => $recurring->amount,
                            'expense_date'     => $recurring->next_due_date,
                            'payment_method'   => $recurring->payment_method,
                            'reference_number' => $recurring->reference_number,
                            'notes'            => $note . ($recurring->notes ? ' — ' . $recurring->notes : ''),
                        ]);
                    } else {
                        Income::create([
                            'user_id'          => $recurring->user_id,
                            'client_id'        => $recurring->client_id,
                            'project_id'       => $recurring->project_id,
                            'category_id'      => $recurring->category_id,
                            'amount'           => $recurring->amount,
                            'income_date'      => $recurring->next_due_date,
                            'payment_method'   => $recurring->payment_method,
                            'reference_number' => $recurring->reference_number,
                            'notes'            => $note . ($recurring->notes ? ' — ' . $recurring->notes : ''),
                        ]);
                    }

                    $newNextDue = $recurring->calculateNextDueDate($recurring->next_due_date);

                    $updates = [
                        'last_generated_at' => now(),
                        'next_due_date'     => $newNextDue,
                    ];

                    // Deactivate if the new next_due_date is past the end_date
                    if ($recurring->end_date && $newNextDue->gt($recurring->end_date)) {
                        $updates['is_active'] = false;
                    }

                    $recurring->update($updates);
                });

                $generated++;
                $this->line("  Generated: [{$recurring->type}] {$recurring->name}");
            } catch (\Throwable $e) {
                Log::error('Failed to generate recurring transaction', [
                    'id'    => $recurring->id,
                    'name'  => $recurring->name,
                    'error' => $e->getMessage(),
                ]);
                $this->error("  Failed:    [{$recurring->type}] {$recurring->name} — {$e->getMessage()}");
            }
        }

        $this->info("Done. Generated: {$generated}, Skipped/deactivated: {$skipped}.");
        return self::SUCCESS;
    }
}
