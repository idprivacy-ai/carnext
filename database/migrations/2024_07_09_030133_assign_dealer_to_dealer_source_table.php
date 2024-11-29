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
        Schema::create('assigned_dealer_source', function (Blueprint $table) {
            $table->id();
            $table->integer('dealer_id')->nullable();
            $table->integer('dealer_source_id')->nullable();
            $table->integer('role_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assigned_dealer_source');
    }
};
