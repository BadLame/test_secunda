<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    function up(): void
    {
        Schema::create('company_business_directions_pivot', function (Blueprint $table) {
            $table->foreignUuid('company_id')
                ->references('id')->on('companies')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignUuid('business_direction_id')
                ->references('id')->on('business_directions')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();

            $table->timestamps();

            $table->primary(['company_id', 'business_direction_id']);
        });
    }

    function down(): void
    {
        Schema::dropIfExists('company_business_directions_pivot');
    }
};
