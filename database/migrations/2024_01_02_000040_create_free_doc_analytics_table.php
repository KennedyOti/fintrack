<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('free_doc_analytics', function (Blueprint $table) {
            $table->id();

            // Visitor identity (hashed for privacy)
            $table->string('session_hash', 64)->nullable()->index();
            $table->string('ip_hash', 64)->nullable();

            // Geo
            $table->string('country_code', 5)->nullable()->index();
            $table->string('country_name', 100)->nullable();

            // Event
            $table->string('event_type', 50)->index();
            // page_view | builder_open | pdf_generated | doc_saved | doc_viewed | pdf_from_share

            // Document details (nullable — page_view has none)
            $table->string('doc_type', 50)->nullable()->index();
            $table->string('template', 50)->nullable();
            $table->string('font', 100)->nullable();
            $table->string('currency', 10)->nullable();

            // Feature usage snapshot (stored when PDF generated / saved)
            $table->json('features_used')->nullable();
            // {tax:bool, discount:bool, shipping:bool, logo:bool,
            //  notes:bool, terms:bool, payment_info:bool, item_count:int}

            // Traffic source
            $table->string('referrer_domain', 255)->nullable();

            $table->timestamps();

            // Composite indexes for analytics queries
            $table->index(['event_type', 'created_at']);
            $table->index(['doc_type', 'event_type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('free_doc_analytics');
    }
};
