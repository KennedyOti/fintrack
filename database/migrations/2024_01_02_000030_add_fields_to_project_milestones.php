<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('description');
            $table->enum('priority', ['low', 'medium', 'high', 'critical'])->default('medium')->after('status');
            $table->string('color', 7)->nullable()->after('priority');
            $table->unsignedInteger('order_position')->default(0)->after('color');
        });
    }

    public function down(): void
    {
        Schema::table('project_milestones', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'priority', 'color', 'order_position']);
        });
    }
};
