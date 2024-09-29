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

        Schema::create('student_payment_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->on('students')->onDelete('cascade');
            $table->foreignId('student_fee_id')->constrained()->on('student_fees')->onDelete('cascade');
            $table->decimal('payment_amount', 10, 2);
            $table->date('payment_date');
            $table->string('payment_mode');
            $table->text('remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_payment_details');
    }
};
