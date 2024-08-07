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
        Schema::create('intakemonths', function (Blueprint $table) {
            $table->id();
            $table->string('inmonth_name',255);
            $table->enum('status',['Active','Inactive'])->default('Active');
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
        Schema::dropIfExists('intakemonths');
    }
};
