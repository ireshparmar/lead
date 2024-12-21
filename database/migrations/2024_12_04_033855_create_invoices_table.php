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
        Schema::create('invoices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('commission_id'); // Reference to the commissions table
            $table->string('invoice_number')->unique(); // Unique invoice identifier
            $table->string('invoice_type'); // Type of invoice (e.g., 'student', 'lead', etc.
            $table->decimal('own_commission', 10, 2)->nullable(); // Own commission amount
            $table->decimal('agent_commission', 10, 2)->nullable(); // Agent commission amount
            $table->decimal('base_currency_rate', 10, 2)->nullable();
            $table->string('base_currency')->nullable(); // Base currency
            $table->string('payment_currency')->nullable(); // Payment currency
            $table->dateTime('payment_date')->nullable(); // Payment date
            $table->dateTime('agent_payment_date')->nullable(); // Payment date
            $table->string('status')->default('Pending Payment')->comment('Pending Payment', 'Commission Received'); // Invoice status
            $table->string('agent_payment_status')->default('Pending Payment')->default('Pending Payment', 'Commission Paid');
            $table->unsignedBigInteger('agent_payment_status_updated_by')->nullable(); // User who update agent payment status
            $table->text('remarks')->nullable(); // Additional remarks or notes
            $table->text('agent_payment_remarks')->nullable(); // Additional remarks or notes
            $table->unsignedBigInteger('created_by')->nullable(); // User who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User who last updated the record
            $table->timestamps();
            // Foreign key constraints
            $table->foreign('commission_id')->references('id')->on('commissions')->onDelete('cascade');
            // Indexes
            $table->index('commission_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
