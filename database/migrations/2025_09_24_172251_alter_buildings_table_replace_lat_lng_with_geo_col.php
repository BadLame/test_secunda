<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void
    {
        // Включить расширение postgis
        DB::statement('CREATE EXTENSION IF NOT EXISTS "postgis"');

        // Добавить колонку географии
        Schema::table('buildings', function (Blueprint $table) {
            $table->geography('geo', 'point')->nullable();
        });

        // Скопировать имеющиеся локации в новую колонку
        DB::statement('UPDATE buildings SET geo = ST_SetSRID(ST_MakePoint(lng, lat), 4326)::geography');

        // Удалить колонки и сделать невозможным проставить null в geo
        Schema::table('buildings', function (Blueprint $table) {
            $table->geography('geo', 'point')->nullable(false)->change();
            $table->dropColumn(['lat', 'lng']);
        });

        // Добавить индекс для более быстрого вычисления расстояния и тд
        DB::statement('CREATE INDEX idx_buildings_geo ON buildings USING GIST (geo)');
    }

    function down(): void
    {
        Schema::table('buildings', function (Blueprint $table) {
            $table->float('lat')->nullable();
            $table->float('lng')->nullable();
        });

        DB::statement('UPDATE buildings SET lat = ST_Y(geo::geometry), lng = ST_X(geo::geometry)');

        Schema::table('buildings', function (Blueprint $table) {
            $table->dropColumn('geo');
            $table->float('lat')->nullable(false)->change();
            $table->float('lng')->nullable(false)->change();
        });
    }
};
