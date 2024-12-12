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
        Schema::create('commission_semesters', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_id'); // Reference to the commissions table
            $table->string('term_start_month')->nullable();
            $table->integer('term_start_year')->nullable();
            $table->decimal('term_fees', 10, 2)->nullable();
            $table->decimal('own_commission', 10, 2)->nullable(); // Own commission for this semester
            $table->decimal('agent_commission', 10, 2)->nullable(); // Agent commission for this semester
            $table->decimal('base_currency_rate', 10, 2)->nullable();
            $table->date('reminder_date')->nullable();
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('commission_id')->references('id')->on('commissions')->onDelete('cascade');
            // Indexes
            $table->index(['commission_id']); // Index for performance
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commission_semesters');
    }
};
