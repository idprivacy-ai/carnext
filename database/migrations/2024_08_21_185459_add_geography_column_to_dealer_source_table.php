<?php 
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddGeographyColumnToDealerSourceTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Ensure PostGIS extension is enabled
        DB::statement('CREATE EXTENSION IF NOT EXISTS postgis');

        // Add geography column for location
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->geography('location')->nullable();
        });

        // Populate the location column with existing latitude and longitude data
        DB::statement('
            UPDATE dealer_source 
            SET location = ST_SetSRID(ST_MakePoint(longitude, latitude), 4326)
            WHERE latitude IS NOT NULL AND longitude IS NOT NULL
        ');

        // Optionally drop latitude and longitude columns if no longer needed
        // Schema::table('dealer_source', function (Blueprint $table) {
        //     $table->dropColumn(['latitude', 'longitude']);
        // });

        // Create a spatial index on the location column
        DB::statement('CREATE INDEX location_idx ON dealer_source USING GIST(location)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Drop the spatial index
        DB::statement('DROP INDEX IF EXISTS location_idx');

        // Drop the geography column
        Schema::table('dealer_source', function (Blueprint $table) {
            $table->dropColumn('location');
        });

        // Optionally, you could restore the latitude and longitude columns if they were dropped
        // Schema::table('dealer_source', function (Blueprint $table) {
        //     $table->double('latitude')->nullable();
        //     $table->double('longitude')->nullable();
        // });
    }
}
