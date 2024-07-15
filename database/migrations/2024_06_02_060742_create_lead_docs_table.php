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
        Schema::create('lead_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("lead_id")->constrained("leads");
            $table->foreignId("user_id")->constrained("users")->comment("id of user who uploading document");
            $table->string("doc_name");
            $table->string("doc_org_name",255)->nullable();
            $table->string("doc_type",255);
            $table->string("other_type",255)->nullable();
            $table->string("mime_type")->comment("image,pdf,doc")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lead_docs');
    }
};
