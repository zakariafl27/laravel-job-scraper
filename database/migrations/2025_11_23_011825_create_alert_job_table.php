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
        Schema::create('alert_job', function (Blueprint $table) {
            $table->id();
            $table->foreignId('job_alert_id')->constrained()->onDelete('cascade');
            $table->foreignId('job_id')->constrained('job_listings')->onDelete('cascade');
            $table->boolean('is_notified')->default(false);
            $table->timestamp('notified_at')->nullable();
            $table->timestamps();
            
            $table->unique(['job_alert_id', 'job_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alert_job');
    }
};
