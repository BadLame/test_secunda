<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void
    {
        Schema::create('business_directions', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('title');
            $table->uuid('parent_id')->nullable()->index();

            $table->timestamps();
        });

        Schema::table('business_directions', function (Blueprint $table) {
            $table->foreign('parent_id')
                ->references('id')->on('business_directions')
                ->nullOnDelete()
                ->cascadeOnUpdate();
        });
    }

    function down(): void
    {
        Schema::dropIfExists('business_directions');
    }
};
