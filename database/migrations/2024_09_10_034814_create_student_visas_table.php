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
        Schema::create('student_visas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->on('students')->onDelete('cascade');
            $table->foreignId('student_admission_id')->constrained()->on('student_admissions')->onDelete('cascade');
            $table->string('visa_type');
            $table->unsignedBigInteger('intakemonth_id')->nullable();
            $table->unsignedBigInteger('intakeyear_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->foreign('intakemonth_id')->references('id')->on('intakemonths')->onDelete('cascade');
            $table->foreign('intakeyear_id')->references('id')->on('intakeyears')->onDelete('cascade');
            $table->string('status')->nullable();
            $table->string('visa_no')->nullable();
            $table->date('visa_date')->nullable();
            $table->date('app_submission_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->string('visa_done')->nullable()->comment('Yes,No');
            $table->date('travel_date')->nullable();
            $table->string('ticket')->nullable();
            $table->string('contact_detail')->nullable();
            $table->string('address')->nullable();
            $table->tinyText('more_detail')->nullable();
            $table->tinyText('remark')->nullable();
            $table->tinyText('note')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_visas');
    }
};
