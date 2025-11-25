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
        Schema::create('job_listings', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('company');
            $table->text('description')->nullable();
            $table->string('location');
            $table->string('job_type')->nullable();
            $table->string('source'); 
            $table->string('source_id')->unique(); 
            $table->string('url');
            $table->string('salary')->nullable();
            $table->date('posted_date')->nullable();
            $table->date('deadline')->nullable();
            $table->timestamps();
            
            $table->index(['source', 'source_id']);
            $table->index('posted_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_listings');
    }
};
