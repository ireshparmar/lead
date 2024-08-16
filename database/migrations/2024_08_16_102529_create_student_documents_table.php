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
        // Schema::create('student_documents', function (Blueprint $table) {
        //     $table->id();
        //     $table->foreignId('student_id')->nullable()->constrained("students")->cascadeOnDelete();
        //     $table->string("doc_name");
        //     $table->string("doc_org_name", 255)->nullable();
        //     $table->string("doc_type", 255);
        //     $table->string("other_type", 255)->nullable();
        //     $table->string('isVerified', 50)->default('Unverified')->comment('Verified, Unverified, Reupload');
        //     $table->mediumText('note')->nullable();
        //     $table->mediumText('remark')->nullable();
        //     $table->foreignId('created_by')->nullable()->constrained("users")->cascadeOnDelete();
        //     $table->foreignId('updated_by')->nullable()->constrained("users")->cascadeOnDelete();
        //     $table->foreignId('verified_by')->nullable()->constrained("users")->cascadeOnDelete();
        //     $table->timestamp('verified_date')->nullable();
        //     $table->timestamps();
        // });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_documents');
    }
};
