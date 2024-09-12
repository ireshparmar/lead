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
        Schema::create('student_college_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('interested_course_id')->constrained()->on('student_interested_courses')->onDelete('cascade');
            $table->foreignId('student_id')->constrained()->on('students')->onDelete('cascade');
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('degree_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('min_eligibility')->nullable(); //min eligibility
            $table->string('duration')->nullable();
            $table->longText('facility')->nullable();
            $table->longText('document')->nullable();
            $table->string('fees')->nullable();
            $table->string('fees_currency')->nullable();
            $table->string('status');
            $table->longText('remark')->nullable();
            $table->string('is_move_to_admission', 20)->nullable()->comment('Yes,No');
            $table->string('allocate_to')->nullable();
            $table->unsignedBigInteger('allocated_user')->nullable();
            $table->longText('note')->nullable();
            $table->unsignedInteger('reference_portal_id')->nullable();
            $table->string('ref_link')->nullable();
            $table->string('eligibility')->nullable();
            $table->unsignedBigInteger('intakemonth_id')->nullable();
            $table->unsignedBigInteger('intakeyear_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->date('app_date')->nullable();
            $table->string('app_number')->nullable();
            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('degree_id')->references('id')->on('degrees')->onDelete('cascade');
            $table->foreign('min_eligibility')->references('id')->on('eligibilities')->onDelete('cascade');
            $table->foreign('reference_portal_id')->references('id')->on('reference_portals')->onDelete('cascade');
            $table->foreign('intakemonth_id')->references('id')->on('intakemonths')->onDelete('cascade');
            $table->foreign('intakeyear_id')->references('id')->on('intakeyears')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('allocated_user')->references('id')->on('users')->onDelete('cascade');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_college_applications');
    }
};
