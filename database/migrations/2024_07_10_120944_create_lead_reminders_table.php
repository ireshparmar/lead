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
        Schema::create('lead_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lead_id")->constrained("leads")->cascadeOnDelete()->nullable();
            $table->dateTime('reminder_date_time')->nullable();
            $table->longText('note')->nullable();
            $table->string('status')->default('Pending')->comment('Done,Pending');
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
        Schema::dropIfExists('lead_reminders');
    }
};
