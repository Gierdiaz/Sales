<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class () extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('contacts', function (Blueprint $table) {
            $table->uuid('id')->primary()->unique();
            $table->string('name')->comment('Nome do contato');
            $table->string('phone')->comment('Telefone de contato');
            $table->string('email')->unique()->comment('Email de contato, deve ser único');
            $table->string('number')->nullable()->comment('Número da residência ou apartamento');
            $table->string('cep')->comment('Código postal');
            $table->string('address')->nullable()->comment('Endereço completo do contato');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
