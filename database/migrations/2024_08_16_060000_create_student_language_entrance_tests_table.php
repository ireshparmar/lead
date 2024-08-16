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
        Schema::create('student_language_entrance_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->nullable()->constrained("students")->cascadeOnDelete();
            $table->string('name')->nullable();
            $table->string('test_center')->nullable();
            $table->date('test_date')->nullable();
            $table->date('expire_date')->nullable();
            $table->decimal('read_score')->nullable();
            $table->decimal('write_score')->nullable();
            $table->decimal('speak_score')->nullable();
            $table->decimal('listen_score')->nullable();
            $table->decimal('overall_score')->nullable();
            $table->string('report_no', 50)->nullable();
            $table->string('username', 100)->nullable();
            $table->string('password', 255)->nullable();
            $table->string('isVerified', 50)->default('Unverified')->comment('Verified, Unverified');
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
        Schema::dropIfExists('student_language_entrance_tests');
    }
};
