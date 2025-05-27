<?php

namespace App\Http\Controllers;

use App\Models\Paciente;
use Illuminate\Http\Request;
use App\Traits\RegistraLog;

class PacienteController extends Controller
{
    use RegistraLog;

    /**
     * Muestra una lista de pacientes.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * 
     */
    public function listarPacientes()
    {
        $respuesta = [];
        $codigo = 200;

        $pacientes = Paciente::all();

        if ($pacientes->isEmpty()) {
            $this->registrarLog('pacientes', 'index', null, 'No hay pacientes registrados');
            $respuesta = ['message' => 'No hay pacientes disponibles'];
            $codigo = 404;
        } else {
            $this->registrarLog('pacientes', 'index', null, 'Listado de pacientes consultado');
            $respuesta = $pacientes;
        }

        return response()->json($respuesta, $codigo);
    }

    /**
     * Crea un nuevo paciente.
     * @param \Illuminate\Http\Request $solicitud recibe los datos del paciente
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function nuevoPaciente(Request $solicitud)
    {
        $respuesta = [];
        $codigo = 201;

        $solicitud->validate([
            'usuario_id' => 'required|exists:users,id|unique:pacientes,usuario_id',
            'nss' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        try {
            $paciente = Paciente::create($solicitud->all());

            if (!$paciente) {
                $this->registrarLog('pacientes', 'store', null, 'Error al crear el paciente');
                $respuesta = ['message' => 'No se pudo crear el paciente'];
                $codigo = 500;
            } else {
                $this->registrarLog('pacientes', 'store', $paciente->id, 'Paciente creado');
                $respuesta = $paciente;
            }
        } catch (\Exception $e) {
            $this->registrarLog('pacientes', 'store', null, 'Excepción al crear el paciente: ' . $e->getMessage());
            $respuesta = ['message' => 'Error interno al crear el paciente'];
            $codigo = 500;
        }

        return response()->json($respuesta, $codigo);
    }

    /**
     * Muestra un paciente específico.
     * @param int $id ID del paciente a buscar
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * 
     */
    public function verPaciente($id)
    {
        $respuesta = [];
        $codigo = 200;

        $paciente = Paciente::find($id);

        if (!$paciente) {
            $this->registrarLog('pacientes', 'show', $id, 'Paciente no encontrado');
            $respuesta = ['message' => 'Paciente no encontrado'];
            $codigo = 404;
            return response()->json($respuesta, $codigo);
        }

        $this->registrarLog('pacientes', 'show', $id, 'Paciente consultado');

        $respuesta = $paciente;

        return response()->json($respuesta, $codigo);
    }

    /**
     * Actualiza los datos de un paciente.
     * @param \Illuminate\Http\Request $solicitud lleva los datos del paciente a actualizar
     * @param int $id ID del paciente a actualizar
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * 
     */
    public function actualizarPaciente(Request $solicitud, $id)
    {
        $respuesta = [];
        $codigo = 200;

        $paciente = Paciente::find($id);

        if (!$paciente) {
            $this->registrarLog('pacientes', 'update', $id, 'Paciente no encontrado');
            $respuesta = ['message' => 'Paciente no encontrado'];
            $codigo = 404;
            return response()->json($respuesta, $codigo);
        }

        $solicitud->validate([
            'nss' => 'nullable|string|max:20',
            'fecha_nacimiento' => 'nullable|date',
        ]);

        $paciente->update($solicitud->only(['nss', 'fecha_nacimiento']));

        $this->registrarLog('pacientes', 'update', $id, 'Paciente actualizado');

        $respuesta = [
            'message' => 'Paciente actualizado correctamente',
            'paciente' => $paciente,
        ];

        return response()->json($respuesta, $codigo);
    }

    /**
     * Borrar un paciente (softDelete).
     * @param int $id ID del paciente a eliminar
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function borrarPaciente($id)
    {
        $paciente = Paciente::find($id);

        $respuesta = [];
        $codigo = 200;

        if (!$paciente) {
            $this->registrarLog('pacientes', 'destroy', $id, 'Paciente no encontrado');

            $codigo = 404;
            $respuesta = ['message' => 'Paciente no encontrado'];
            return response()->json($respuesta, $codigo); // Cortamos aquí
        }

        if ($paciente->delete()) {
            $this->registrarLog('pacientes', 'destroy', $id, 'Paciente eliminado');
            $respuesta = ['message' => 'Paciente eliminado correctamente'];
        } else {
            $this->registrarLog('pacientes', 'destroy', $id, 'Fallo al eliminar paciente');
            $respuesta = ['message' => 'No se pudo eliminar el paciente'];
            $codigo = 500;
        }

        return response()->json($respuesta, $codigo);
    }
}
