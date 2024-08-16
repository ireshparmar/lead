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
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('first_name', 255)->nullable();
            $table->string('middle_name', 255)->nullable();
            $table->string('last_name', 255)->nullable();
            $table->string('enrollment_number')->nullable();
            $table->date('enrollment_date')->nullable();
            $table->date('birth_date')->nullable();
            $table->string('mobile', 50)->nullable();
            $table->string('gender', 50)->nullable()->comment('Male', 'Female', 'TransGender');
            $table->string('email', 255)->nullable();
            $table->foreignId('inquiry_source_id')->nullable()->constrained("inquiry_sources")->cascadeOnDelete();
            $table->string('address', 255)->nullable();
            $table->string('postal_code', 50)->nullable();
            $table->foreignId('country_id')->nullable()->constrained("countries")->cascadeOnDelete();
            $table->foreignId('state_id')->nullable()->constrained("states")->cascadeOnDelete();
            $table->foreignId('city_id')->nullable()->constrained("cities")->cascadeOnDelete();
            $table->foreignId('reference_by')->nullable()->constrained("students")->cascadeOnDelete();
            $table->foreignId('purpose_id')->nullable()->constrained("purposes")->cascadeOnDelete();
            $table->foreignId('service_id')->nullable()->constrained("services")->cascadeOnDelete();
            $table->foreignId('pref_country_id')->nullable()->constrained("countries")->cascadeOnDelete();
            $table->string('remark', 255)->nullable();
            $table->foreignId('agent_id')->nullable()->constrained("users")->cascadeOnDelete();
            $table->string('emergency_name', 255)->nullable();
            $table->string('emergency_relation', 255)->nullable();
            $table->string('emergency_contact_no', 255)->nullable();
            $table->string('emergency_detail', 255)->nullable();
            $table->foreignId('created_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained("users")->cascadeOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
