<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('system_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            // Cast type for reading the value: string, boolean, integer
            $table->string('type', 20)->default('string');
            // Logical group for display purposes
            $table->string('group', 50)->default('general');
            $table->string('label', 150);
            $table->text('description')->nullable();
            $table->timestamps();

            $table->index('group');
        });

        // Seed default settings
        $now = now();
        DB::table('system_settings')->insert([
            // ── General ──────────────────────────────────────────────
            [
                'key' => 'app_name', 'value' => 'FinTrack', 'type' => 'string',
                'group' => 'general', 'label' => 'Application Name',
                'description' => 'The name displayed in the browser title and UI.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean',
                'group' => 'general', 'label' => 'User Registration',
                'description' => 'Allow new users to register an account. Disable to close registrations.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'maintenance_mode', 'value' => '0', 'type' => 'boolean',
                'group' => 'general', 'label' => 'Maintenance Mode',
                'description' => 'Display a maintenance notice. Use `php artisan down` to fully block access.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'maintenance_message', 'value' => 'We are currently performing scheduled maintenance. Please check back soon.', 'type' => 'string',
                'group' => 'general', 'label' => 'Maintenance Message',
                'description' => 'Message shown to users when maintenance mode is active.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'max_users', 'value' => '0', 'type' => 'integer',
                'group' => 'general', 'label' => 'Maximum Users',
                'description' => 'Maximum number of registered users allowed. Set to 0 for unlimited.',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Security ─────────────────────────────────────────────
            [
                'key' => 'require_email_verification', 'value' => '1', 'type' => 'boolean',
                'group' => 'security', 'label' => 'Require Email Verification',
                'description' => 'Users must verify their email address before accessing the portal.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'two_factor_required', 'value' => '0', 'type' => 'boolean',
                'group' => 'security', 'label' => 'Enforce Two-Factor Authentication',
                'description' => 'Force all users to complete email OTP verification at every login.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'session_lifetime', 'value' => '120', 'type' => 'integer',
                'group' => 'security', 'label' => 'Session Lifetime (minutes)',
                'description' => 'How long an idle session remains valid before the user is logged out.',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'password_min_length', 'value' => '8', 'type' => 'integer',
                'group' => 'security', 'label' => 'Minimum Password Length',
                'description' => 'Minimum number of characters required for user passwords.',
                'created_at' => $now, 'updated_at' => $now,
            ],

            // ── Localization ──────────────────────────────────────────
            [
                'key' => 'default_currency', 'value' => 'USD', 'type' => 'string',
                'group' => 'localization', 'label' => 'Default Currency',
                'description' => 'Default currency code for new user accounts (ISO 4217).',
                'created_at' => $now, 'updated_at' => $now,
            ],
            [
                'key' => 'default_timezone', 'value' => 'UTC', 'type' => 'string',
                'group' => 'localization', 'label' => 'Default Timezone',
                'description' => 'Default timezone for new user accounts.',
                'created_at' => $now, 'updated_at' => $now,
            ],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('system_settings');
    }
};
