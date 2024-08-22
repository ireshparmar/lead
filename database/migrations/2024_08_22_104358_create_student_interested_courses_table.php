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
        Schema::create('student_interested_courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('course_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('campus_id');
            $table->string('eligibility')->nullable(); //min eligibility
            $table->string('duration')->nullable();

            $table->string('facility')->nullable();
            $table->string('document')->nullable();
            $table->string('fees')->nullable();
            $table->string('status');
            $table->string('remark')->nullable();
            $table->unsignedInteger('reference_portal_id');
            $table->string('ref_link')->nullable();
            $table->unsignedBigInteger('eligibility_id')->nullable();
            $table->unsignedBigInteger('intakemonth_id')->nullable();
            $table->unsignedBigInteger('intakeyear_id')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();


            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('course_id')->references('id')->on('courses')->onDelete('cascade');
            $table->foreign('eligibility_id')->references('id')->on('eligibilities')->onDelete('cascade');
            $table->foreign('reference_portal_id')->references('id')->on('reference_portals')->onDelete('cascade');
            $table->foreign('intakemonth_id')->references('id')->on('intakemonths')->onDelete('cascade');
            $table->foreign('intakeyear_id')->references('id')->on('intakeyears')->onDelete('cascade');
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
        Schema::dropIfExists('student_interested_courses');
    }
};
