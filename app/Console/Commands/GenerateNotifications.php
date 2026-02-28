<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Console\Command;

class GenerateNotifications extends Command
{
    protected $signature   = 'notifications:generate {--user= : Only generate for a specific user ID}';
    protected $description = 'Generate smart notifications for all active users';

    public function handle(NotificationService $service): int
    {
        $userId = $this->option('user');

        $query = User::query()->whereNull('deleted_at');

        if ($userId) {
            $query->where('id', $userId);
        }

        $users = $query->get();

        if ($users->isEmpty()) {
            $this->warn('No users found.');
            return self::SUCCESS;
        }

        $this->info("Generating notifications for {$users->count()} user(s)...");

        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        foreach ($users as $user) {
            try {
                $service->generateForUser($user);
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed for user #{$user->id}: " . $e->getMessage());
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Done!');

        return self::SUCCESS;
    }
}
