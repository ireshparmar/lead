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
        Schema::create('agent_docs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("agent_id")->constrained("users")->comment("id of agent");
            $table->string("doc_name");
            $table->string("doc_org_name", 255)->nullable();
            $table->string("mime_type")->comment("image,pdf,doc")->nullable();
            $table->foreignId("created_by")->constrained("users")->comment("id of user who is uploading document");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_docs');
    }
};
