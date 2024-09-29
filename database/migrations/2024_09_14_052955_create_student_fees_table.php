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

        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->on('students')->onDelete('cascade');
            $table->foreignId('package_id')->constrained()->on('packages')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
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
        Schema::dropIfExists('student_fees');
    }
};
