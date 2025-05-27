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
     * Se obtienen todos los logs de la base de datos, ordenados por fecha de creación.
     * Se incluye la relación con el usuario que generó el log para obtener su información.
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con todos los logs.
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
     * Se busca un usuario por su ID y se devuelven sus logs.
     * Se valida que el ID sea numérico y se maneja el caso en que no es válido.
     * @param int $id ID del usuario
     * @return \Illuminate\Http\JsonResponse devuelve los logs del usuario o un mensaje de error si el ID no es válido.
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
     * Se filtran los logs por una acción específica.
     * Se valida que la acción sea una de las acciones permitidas.
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
