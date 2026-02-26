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
        Schema::table('users', function (Blueprint $table) {
        $table->string('apellido')->nullable();
        $table->string('telefono')->nullable();
        $table->foreignId('direccion_id')->nullable()->constrained()->nullOnDelete();
        $table->foreignId('rol_id')->nullable()->constrained()->nullOnDelete();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
        $table->dropForeign(['direccion_id']);
        $table->dropForeign(['rol_id']);
        $table->dropColumn(['apellido', 'telefono', 'direccion_id', 'rol_id']);
    });
    }
};
