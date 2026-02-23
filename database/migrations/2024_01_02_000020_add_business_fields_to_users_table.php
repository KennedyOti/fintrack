<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('business_name')->nullable()->after('phone');
            $table->string('tax_number')->nullable()->after('business_name');
            $table->text('business_address')->nullable()->after('tax_number');
            $table->string('logo_path')->nullable()->after('business_address');
            $table->string('currency_code', 3)->default('USD')->after('logo_path');
            $table->string('timezone')->default('UTC')->after('currency_code');
            $table->enum('role', ['user', 'admin'])->default('user')->after('timezone');
            $table->enum('status', ['active', 'inactive', 'suspended'])->default('active')->after('role');
            $table->timestamp('last_login_at')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone',
                'business_name',
                'tax_number',
                'business_address',
                'logo_path',
                'currency_code',
                'timezone',
                'role',
                'status',
                'last_login_at',
            ]);
        });
    }
};
