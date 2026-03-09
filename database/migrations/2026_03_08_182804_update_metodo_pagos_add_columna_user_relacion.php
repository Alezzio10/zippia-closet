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
        Schema::table('metodo_pagos', function (Blueprint $table) {

        // elimina la columna de ccv
        $table->dropColumn('ccv');

       //esto crea la columna de la llave foranea de usuario. lo de after hace que se cree despues de la columna id 
       //y no al final, onDelete cascade hace que si se borra un registro de usuario tambien se borre el registro de metodo de pago de
       //ese usuario
        $table->unsignedBigInteger('user_id')->after('id');
        $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('metodo_pagos', function (Blueprint $table) {

        // esto elimina la llave foranea de user id y transforma las id en solo numeros
        $table->dropForeign(['user_id']);

        // elimina la columna user_id
        $table->dropColumn('user_id');

        // vuelve a crear la de ccv
        $table->string('ccv');

        });
    }
};
