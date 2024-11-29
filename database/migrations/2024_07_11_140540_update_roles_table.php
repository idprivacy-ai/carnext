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
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name']); // Remove the existing unique constraint
            $table->unique(['name', 'guard_name', 'dealer_id']); // Add the composite unique constraint
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropUnique(['name', 'guard_name', 'dealer_id']); // Drop the composite unique constraint
            $table->unique(['name', 'guard_name']); // Restore the original unique constraint
        });
    }
};
