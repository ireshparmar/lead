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
        Schema::create('student_admission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained()->on('students')->onDelete('cascade');
            $table->foreignId('student_admission_id')->constrained()->on('student_admissions')->onDelete('cascade');
            $table->foreignId('doc_type_id')->nullable()->constrained("document_types")->cascadeOnDelete();
            $table->string("doc_name");
            $table->string("doc_org_name", 255)->nullable();
            $table->mediumText('remark')->nullable();
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
        Schema::dropIfExists('student_admission_documents');
    }
};
