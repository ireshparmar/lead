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
        Schema::create('student_education_levels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained("students")->cascadeOnDelete();
            $table->foreignId('education_level_id')->nullable()->constrained("education_levels")->cascadeOnDelete();
            $table->foreignId('duration_id')->nullable()->constrained("durations")->cascadeOnDelete();
            $table->string('status', 50)->nullable()->comment('Completed, In Process');
            $table->string('isVerified', 50)->default('Unverified')->comment('Verified, Unverified');
            $table->string('school_or_uni')->nullable()->comment('School Or University Name');
            $table->string('degree_or_dept')->nullable()->comment('Degree Or Department Name');
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->string('gpa_or_percentage', 50)->nullable()->comment('Gpa, Grade, Percentage');
            $table->mediumText('note')->nullable();
            $table->mediumText('remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->timestamp('verified_date')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_education_levels');
    }
};
