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
        Schema::create('commissions', function (Blueprint $table) {
            $table->id();
            $table->string('commissionable_type'); // Polymorphic relation (e.g., 'lead', 'student')
            $table->unsignedBigInteger('commissionable_id'); // Polymorphic relation ID
            $table->string('commission_type')->comment('one-time', 'semester-wise'); // Commission type
            $table->decimal('own_commission', 10, 2)->nullable(); // Own commission amount
            $table->decimal('agent_commission', 10, 2)->nullable(); // Agent commission amount
            $table->decimal('base_currency_rate', 10, 2)->nullable();
            $table->unsignedBigInteger('agent_id')->nullable();
            $table->date('reminder_date')->nullable();
            $table->string('admission_by')->nullable();
            $table->text('remarks')->nullable(); // Additional remarks or description
            $table->unsignedBigInteger('created_by')->nullable(); // User who created the record
            $table->unsignedBigInteger('updated_by')->nullable(); // User who last updated the record
            $table->string('status', '255')->default('Pending Invoice')->comment('Pending Invoice', 'Invoice Generated');
            $table->softDeletes(); // Soft delete
            $table->timestamps();
            // Indexes
            $table->index(['commissionable_type', 'commissionable_id', 'agent_id']); // Index for polymorphic relationship
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('commissions');
    }
};
