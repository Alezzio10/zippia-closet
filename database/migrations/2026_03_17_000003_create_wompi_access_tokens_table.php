<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wompi_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->text('access_token');
            $table->unsignedInteger('expires_in')->nullable();
            $table->string('token_type', 50)->nullable();
            $table->string('scope', 191)->nullable();
            $table->timestamp('obtained_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wompi_access_tokens');
    }
};

