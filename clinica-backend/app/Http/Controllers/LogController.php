<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class LogController extends Controller
{
    
    /**
     * 
     * Constructor para aplicar middleware de autenticación y rol.
     * 
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'role:admin']);
    }

    /**
     * 
     * Listar todos los logs.
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function listarLogs()
    {
        $logs = Log::with('usuario:id,nombre,apellidos,email')
                    ->orderBy('created_at', 'desc')
                    ->get();

        return response()->json($logs);
    }

    /**
     * 
     * Obtener logs por ID de usuario.
     * 
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse
     */
    public function porUsuario($id)
    {
        $respuesta = [];

        if (!is_numeric($id)) {
            $respuesta = [
                'error' => 'ID inválido',
                'status' => 400,
            ];
        } else {
            $logs = Log::with('usuario:id,nombre,apellidos,email')
                        ->where('usuario_id', $id)
                        ->orderBy('created_at', 'desc')
                        ->get();

            \Log::info("Consulta de logs del usuario ID {$id}");

            $respuesta = [
                'data' => $logs,
                'status' => 200,
            ];
        }

        return response()->json($respuesta, $respuesta['status']);
    }

    /**
     * 
     * Obtener logs por acción específica.
     * 
     * @param string $accion Acción a filtrar
     * @return \Illuminate\Http\JsonResponse
     */
    public function porAccion($accion)
    {
        $respuesta = [];
        $accionesValidas = ['login', 'logout', 'crear_cita', 'actualizar_usuario'];

        if (!in_array($accion, $accionesValidas)) {
            $respuesta = [
                'error' => 'Acción no válida',
                'status' => 400,
            ];
        } else {
            $logs = Log::with('usuario:id,nombre,apellidos,email')
                        ->where('accion', $accion)
                        ->orderBy('created_at', 'desc')
                        ->get();

            \Log::info("Consulta de logs por acción: {$accion}");

            $respuesta = [
                'data' => $logs,
                'status' => 200,
            ];
        }

        return response()->json($respuesta, $respuesta['status']);
    }

}
