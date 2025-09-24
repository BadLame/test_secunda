<?php

use App\Models\BusinessDirection;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration {
    function up(): void
    {
        Schema::table('business_directions', function (Blueprint $table) {
            $table->string('code')->nullable();
        });

        BusinessDirection::all()
            ->each(fn (BusinessDirection $bd) => $bd->update([
                'code' => Str::snake(Str::transliterate($bd->title)),
            ]));

        Schema::table('business_directions', function (Blueprint $table) {
            $table->string('code')
                ->nullable(false)
                ->unique()
                ->index()
                ->comment('Уникальный код каждого направления деятельности')
                ->change();
        });
    }

    function down(): void
    {
        Schema::table('business_directions', function (Blueprint $table) {
            $table->dropColumn('code');
        });
    }
};
