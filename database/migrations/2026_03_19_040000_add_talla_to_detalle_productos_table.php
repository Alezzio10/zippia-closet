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
        Schema::table('detalle_productos', function (Blueprint $table) {
            if (!Schema::hasColumn('detalle_productos', 'talla')) {
                $table->string('talla', 10)->nullable()->after('cantidad');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalle_productos', function (Blueprint $table) {
            if (Schema::hasColumn('detalle_productos', 'talla')) {
                $table->dropColumn('talla');
            }
        });
    }
};

