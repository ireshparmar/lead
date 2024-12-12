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
            $table->decimal('total_amount', 10, 2); // Total amount for the invoice
            $table->string('status')->default('Pending Payment')->default('Pending Payment', 'Commission Received'); // Invoice status
            $table->text('remarks')->nullable(); // Additional remarks or notes
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
