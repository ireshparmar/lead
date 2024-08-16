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
        Schema::create('student_work_experiences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained("students")->cascadeOnDelete();
            $table->date('from_date')->nullable();
            $table->date('to_date')->nullable();
            $table->string('company_name')->nullable();
            $table->string('company_address')->nullable();
            $table->string('designation')->nullable();
            $table->string('job_type')->nullable();
            $table->mediumText('job_description')->nullable();
            $table->boolean('is_working')->default(0)->comment('Shows that student is currently working here.If it is true then no need to enter to_date');
            $table->string('isVerified', 50)->default('Unverified')->comment('Verified, Unverified');
            $table->mediumText('remark')->nullable();
            $table->foreignId('created_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('verified_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->timestamp('verified_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_work_experiences');
    }
};
