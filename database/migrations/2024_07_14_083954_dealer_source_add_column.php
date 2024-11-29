<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->smallInteger('is_manage_by_admin')->default(0);
            $table->float('subscription_price', 8, 2)->nullable();
            $table->string('subscription_plan')->nullable();
            $table->smallInteger('is_subscribed')->default(0);
            $table->date('cancelled_at')->nullable();
            $table->string('subscription_id')->nullable();
            $table->string('email')->nullable();
            $table->string('phone')->nullable();
            $table->string('address')->nullable();
            $table->string('call_tracking_number')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('city')->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->dropColumn('is_manage_by_admin');
            $table->dropColumn('subscription_price');
            $table->dropColumn('subscription_plan');
            $table->dropColumn('subscription_id');
            $table->dropColumn('cancelled_at');
            $table->dropColumn('email');
            $table->dropColumn('phone');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('zip_code');
            $table->dropColumn('latitude');
            $table->dropColumn('longitude');
            $table->dropColumn('is_subscribed');
           
        });
    }
};
