<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('historiales', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id');
            $table->unsignedBigInteger('especialista_id');
            $table->text('comentarios_paciente')->nullable();
            $table->text('observaciones_especialista')->nullable();
            $table->text('recomendaciones')->nullable();
            $table->text('dieta')->nullable();
            $table->text('lista_compra')->nullable();
            $table->timestamps();

            $table->foreign('paciente_id')->references('id')->on('pacientes')->onDelete('cascade');
            $table->foreign('especialista_id')->references('id')->on('especialistas')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historiales');
    }
};