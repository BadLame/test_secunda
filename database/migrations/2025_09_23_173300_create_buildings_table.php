<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    function up(): void
    {
        Schema::create('buildings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('address');
            $table->float('lat');
            $table->float('lng');
            $table->timestamps();
        });
    }

    function down(): void
    {
        Schema::dropIfExists('buildings');
    }
};
