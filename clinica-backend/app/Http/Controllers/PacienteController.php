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
     * Muestra todos los pacientes registrados en la base de datos.
     * @return \Illuminate\Http\JsonResponse esta función devuelve una respuesta JSON con el listado de pacientes.
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
     * Registra un nuevo paciente en la base de datos.
     * Se valida que el usuario asociado exista y no esté ya registrado como paciente.
     * 
     * @param \Illuminate\Http\Request $solicitud recibe los datos del paciente
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con el paciente creado o un mensaje de error.
     * @throws \Illuminate\Validation\ValidationException si los datos no cumplen con las reglas de validación.
     * @throws \Exception lanza excepción si ocurre un error al crear el paciente.
     * 
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
            $datos = $solicitud->only(['usuario_id', 'nss', 'fecha_nacimiento']);
            $paciente = Paciente::create($datos);

            $this->registrarLog('pacientes', 'store', $paciente->id, 'Paciente creado correctamente');
            $respuesta = $paciente;
        } catch (\Exception $e) {
            $this->registrarLog('pacientes', 'store', null, 'Excepción al crear paciente: ' . $e->getMessage());
            $respuesta = ['message' => 'Error interno al crear el paciente'];
            $codigo = 500;
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * Muestra un paciente específico.
     * Busca un paciente por su ID y devuelve sus datos.
     * Se valida que el ID sea numérico y que el paciente exista.
     * @param int $id ID del paciente a buscar
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con los datos del paciente o un mensaje de error.
     * 
     */
    public function verPaciente($id)
    {
        $respuesta = [];
        $codigo = 200;

        if (!is_numeric($id)) {
            $this->registrarLog('pacientes', 'show', $id, 'ID inválido (no numérico)');
            $respuesta = ['message' => 'ID inválido'];
            $codigo = 400;
        } else {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                $this->registrarLog('pacientes', 'show', $id, 'Paciente no encontrado');
                $respuesta = ['message' => 'Paciente no encontrado'];
                $codigo = 404;
            } else {
                $this->registrarLog('pacientes', 'show', $id, 'Paciente consultado');
                $respuesta = $paciente;
            }
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * Actualiza los datos de un paciente.
     * Actualiza la información de un paciente existente en la base de datos.
     * Se valida que el ID sea numérico y que el paciente exista.
     * @param \Illuminate\Http\Request $solicitud lleva los datos del paciente a actualizar
     * @param int $id ID del paciente a actualizar
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con los datos del paciente actualizado o un mensaje de error.
     * @throws \Illuminate\Validation\ValidationException lanza excepción si los datos no cumplen con las reglas de validación.
     * 
     */
    public function actualizarPaciente(Request $solicitud, $id)
    {
        $respuesta = [];
        $codigo = 200;

        if (!is_numeric($id)) {
            $this->registrarLog('pacientes', 'update', $id, 'ID inválido (no numérico)');
            $respuesta = ['message' => 'ID inválido'];
            $codigo = 400;
        } else {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                $this->registrarLog('pacientes', 'update', $id, 'Paciente no encontrado');
                $respuesta = ['message' => 'Paciente no encontrado'];
                $codigo = 404;
            } else {
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
            }
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * Borrar un paciente (softDelete).
     * Este método elimina un paciente por su ID.
     * Se valida que el ID sea numérico y que el paciente exista.
     * @param int $id ID del paciente a eliminar
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con un mensaje de éxito o un mensaje de error.
     * 
     */
    public function borrarPaciente($id)
    {
        $respuesta = [];
        $codigo = 200;

        if (!is_numeric($id)) {
            $this->registrarLog('pacientes', 'destroy', $id, 'ID inválido (no numérico)');
            $respuesta = ['message' => 'ID inválido'];
            $codigo = 400;
        } else {
            $paciente = Paciente::find($id);

            if (!$paciente) {
                $this->registrarLog('pacientes', 'destroy', $id, 'Paciente no encontrado');
                $respuesta = ['message' => 'Paciente no encontrado'];
                $codigo = 404;
            } else {
                if ($paciente->delete()) {
                    $this->registrarLog('pacientes', 'destroy', $id, 'Paciente eliminado');
                    $respuesta = ['message' => 'Paciente eliminado correctamente'];
                } else {
                    $this->registrarLog('pacientes', 'destroy', $id, 'Fallo al eliminar paciente');
                    $respuesta = ['message' => 'No se pudo eliminar el paciente'];
                    $codigo = 500;
                }
            }
        }

        return response()->json($respuesta, $codigo);
    }

}
