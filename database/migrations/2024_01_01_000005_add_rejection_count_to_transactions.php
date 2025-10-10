<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->integer('rejection_count')->default(0)->after('payment_status');
        });
        
        // Update existing records to have rejection_count = 0
        \DB::table('transactions')->whereNull('rejection_count')->update(['rejection_count' => 0]);
    }

    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn('rejection_count');
        });
    }
};
