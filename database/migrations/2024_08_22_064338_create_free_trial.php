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
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->smallInteger('free_trial')->default(0);
            $table->date('free_trial_start_date')->nullable();
           
            $table->date('free_trial_end_date')->nullable();
            
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->dropColumn('free_trial');
            $table->dropColumn('free_trial_end_date');
            $table->dropColumn('free_trial_start_date');
           
        });
    }
};
