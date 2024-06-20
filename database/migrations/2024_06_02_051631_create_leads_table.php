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
        Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->string("full_name")->nullable();
            $table->string("email")->nullable();
            $table->string("phone")->nullable();
            $table->string("passport_no")->nullable();
            $table->string("address")->nullable();
            $table->string("status")->default("New")->nullable();
            $table->string("job_offer")->nullable();
            $table->string("pcc")->nullable();
            $table->decimal("amount")->nullable();
            $table->foreignId("visa_type_id")->constrained("visa_types");
            $table->foreignId("agent_id")->nullable()->constrained("users");
            $table->decimal("agent_commission")->nullable();
            $table->foreignId("assigned_to")->nullable()->constrained("users");
            $table->foreignId("created_by")->constrained("users");
            $table->foreignId("updated_by")->nullable()->constrained("users");
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
