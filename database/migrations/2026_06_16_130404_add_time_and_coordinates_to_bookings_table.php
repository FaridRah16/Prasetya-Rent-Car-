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
        Schema::table('bookings', function (Blueprint $table) {
            $table->time('pickup_time')->nullable()->after('start_date');
            $table->time('return_time')->nullable()->after('end_date');
            $table->decimal('pickup_lat', 10, 7)->nullable()->after('pickup_location');
            $table->decimal('pickup_lng', 10, 7)->nullable()->after('pickup_lat');
            $table->decimal('dropoff_lat', 10, 7)->nullable()->after('dropoff_location');
            $table->decimal('dropoff_lng', 10, 7)->nullable()->after('dropoff_lat');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bookings', function (Blueprint $table) {
            $table->dropColumn(['pickup_time', 'return_time', 'pickup_lat', 'pickup_lng', 'dropoff_lat', 'dropoff_lng']);
        });
    }
};
