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
        Schema::table('leads', function (Blueprint $table) {
            $table->decimal('refund_amount')->nullable()->after('amount');
            $table->longText('refund_reson')->nullable()->after('refund_amount');
            $table->longText('refund_docs')->nullable()->after('refund_amount');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropColumn(['refund_amount','refund_reson','refund_docs']);
        });
    }
};
