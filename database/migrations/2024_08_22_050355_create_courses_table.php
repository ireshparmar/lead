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
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('country_id');
            $table->unsignedBigInteger('college_id');
            $table->unsignedBigInteger('campus_id');
            $table->unsignedBigInteger('stream_id');
            $table->unsignedBigInteger('eligibility_id');
            $table->unsignedBigInteger('degree_id');
            $table->text('course_description')->nullable();
            $table->string('duration');
            $table->string('fees')->nullable();
            $table->text('facility')->nullable();
            $table->text('document')->nullable();
            $table->text('remarks')->nullable();
            $table->text('other')->nullable();
            $table->string('eligibility')->nullable();
            $table->string('broucher')->nullable();
            $table->string('program_link')->nullable();
            $table->string('own_comission')->nullable();
            $table->string('agent_comission')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('country_id')->references('id')->on('countries')->onDelete('cascade');
            $table->foreign('college_id')->references('id')->on('colleges')->onDelete('cascade');
            $table->foreign('campus_id')->references('id')->on('campuses')->onDelete('cascade');
            $table->foreign('stream_id')->references('id')->on('streams')->onDelete('cascade');
            $table->foreign('eligibility_id')->references('id')->on('eligibilities')->onDelete('cascade');
            $table->foreign('degree_id')->references('id')->on('degrees')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('courses');
    }
};
