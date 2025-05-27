<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\LogController;
use App\Http\Controllers\EspecialistaController;
use App\Http\Controllers\PacienteController;

Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('logs')->group(function () {
    Route::get('/', [LogController::class, 'listarLogs']);
    Route::get('/usuario/{id}', [LogController::class, 'porUsuario']);
    Route::get('/accion/{accion}', [LogController::class, 'porAccion']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/users', [UserController::class, 'listarTodos']);
    Route::get('/users/{id}', [UserController::class, 'verUsuario']);
    Route::post('/users', [UserController::class, 'insertarUsuario']);
    Route::put('/users/{id}', [UserController::class, 'actualizar']);
    Route::delete('/users/{id}', [UserController::class, 'borrarUsuario']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->prefix('pacientes')->group(function () {
    
    Route::get('/', [PacienteController::class, 'listarPacientes']);
    Route::post('/', [PacienteController::class, 'nuevoPaciente']);
    Route::get('/{id}', [PacienteController::class, 'verPaciente']);
    Route::put('/{id}', [PacienteController::class, 'actualizarPaciente']);
    Route::delete('/{id}', [PacienteController::class, 'borrarPaciente']);
    
});

Route::middleware(['auth:sanctum', 'role:admin|especialista|paciente'])->prefix('historiales')->group(function () {
    Route::get('/', [HistorialController::class, 'listarHistoriales'])->middleware('role:admin');
    Route::get('/{id}', [HistorialController::class, 'verHistorial']);
    Route::post('/', [HistorialController::class, 'nuevoHistorial'])->middleware('role:especialista');
    Route::put('/{id}', [HistorialController::class, 'actualizarHistorial'])->middleware('role:especialista');
    Route::delete('/{id}', [HistorialController::class, 'borrarHistorial'])->middleware('role:admin');
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::post('especialistas', [EspecialistaController::class, 'nuevoEspecialista']);
    Route::get('especialistas', [EspecialistaController::class, 'listarEspecialistas']);
    Route::get('especialistas/{id}', [EspecialistaController::class, 'verEspecialista']);
    Route::put('especialistas/{id}', [EspecialistaController::class, 'actualizarEspecialista']);
    Route::delete('especialistas/{id}', [EspecialistaController::class, 'borrarEspecialista']);
});

Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
    Route::apiResource('usuarios', UserController::class);
});