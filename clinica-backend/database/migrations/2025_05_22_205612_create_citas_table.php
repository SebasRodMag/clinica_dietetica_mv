<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id('id_cita');
            $table->foreignId('id_paciente')->constrained('pacientes')->onDelete('cascade');
            $table->foreignId('id_especialista')->constrained('especialistas');
            $table->dateTime('fecha_hora_cita');
            $table->enum('tipo_cita', ['presencial', 'telemática']);
            $table->enum('estado', ['pendiente', 'realizada', 'cancelada'])->default('pendiente');
            $table->boolean('es_primera')->default(false);
            $table->text('comentario')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
