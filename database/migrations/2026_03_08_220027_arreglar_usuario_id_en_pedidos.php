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
        Schema::table('pedidos', function (Blueprint $table) {
             // esto es para borrar la relacion incorrecta de la migraacion original, deberia ser users
             //porq la tabla usuarios no existe en la bd
            $table->dropForeign(['usuario_id']); 
            // apuntar a la tabla correcta
            $table->foreign('usuario_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
             $table->dropForeign(['usuario_id']);
             $table->foreign('usuario_id')->references('id')->on('usuarios')->cascadeOnDelete();
        });
    }
};
