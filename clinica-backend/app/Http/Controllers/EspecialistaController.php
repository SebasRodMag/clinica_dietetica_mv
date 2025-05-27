<?php

namespace App\Http\Controllers;

use App\Models\Especialista;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Http\Traits\Loggable;

class EspecialistaController extends Controller
{
    use Loggable;

    /**
     * Mostrar todos los especialistas.
     * @return \Illuminate\Http\JsonResponse
     * @throws \Illuminate\Validation\ValidationException
     * 
     * 
     */
    public function listarEspecialistas(): JsonResponse
    {
        $especialistas = Especialista::all();

        $this->registrarLog(auth()->id(), 'listar_especialistas', 'Acceso a listado completo de especialistas');

        return response()->json(['especialistas' => $especialistas], 200);
    }

    /**
     * Mostrar un especialista específico.
     *
     * @param int $id ID del especialista que deseamos ver
     * @throws \Illuminate\Validation\ValidationException
     * @return JsonResponse
     */
    public function verEspecialista(int $id): JsonResponse
    {
        $especialista = Especialista::find($id);

        $respuesta = [];
        $codigo = 200;

        if (!$especialista) {
            $this->registrarLog(auth()->id(), 'mostrar_especialista_fallido', 'Especialista no encontrado', 'id');
            $codigo = 404;
            $respuesta = ['message' => 'Especialista no encontrado'];
        }

        $this->registrarLog(auth()->id(), 'mostrar_especialista', "Visualización del especialista ID $id");

        $respuesta = [
            'especialista' => $especialista,
        ];
        return response()->json($respuesta, $codigo);
    }

    /**
     * Actualizar la información de un especialista.
     *
     * @param Request $solicitud parámetro de solicitud que contiene los datos a actualizar
     * @param int $id ID del especialista que se desea actualizar
     * @throws \Illuminate\Validation\ValidationException
     * @return JsonResponse
     */
    public function actualizarEspecialista(Request $solicitud, int $id): JsonResponse
    {
        $especialista = Especialista::find($id);
        $mensaje = '';
        $codigo = 200;

        if (!$especialista) {
            $this->registrarLog(auth()->id(), 'actualizar_especialista_fallido', 'Especialista no encontrado', 'id');
            $mensaje = ['message' => 'Especialista no encontrado'];
            $codigo = 404;
        } else {
            $solicitud->validate([
                'nombre'     => 'string|nullable',
                'apellidos'  => 'string|nullable',
                'telefono'   => 'string|nullable',
            ]);

            $especialista->actualizarEspecialista($solicitud->only(['nombre', 'apellidos', 'telefono']));

            $this->registrarLog(auth()->id(), 'actualizar_especialista', "Actualización del especialista ID $id");

            $mensaje = ['message' => 'Especialista actualizado correctamente', 'especialista' => $especialista];
        }

        return response()->json($mensaje, $codigo);
    }

    /**
     * Borrar un especialista (softDelete).
     *
     * @param int $id ID del especialista que se desea eliminar
     * @return JsonResponse
     */
    public function borrarEspecialista(int $id): JsonResponse
    {
        $especialista = Especialista::find($id);
        $mensaje = '';
        $codigo = 200;

        if (!$especialista) {
            $this->registrarLog(auth()->id(), 'eliminar_especialista_fallido', 'Especialista no encontrado', 'id');
            $mensaje = ['message' => 'Especialista no encontrado'];
            $codigo = 404;
        } else {
            $especialista->delete();

            $this->registrarLog(auth()->id(), 'eliminar_especialista', "Especialista ID $id eliminado");

            $mensaje = ['message' => 'Especialista eliminado correctamente'];
        }

        return response()->json($mensaje, $codigo);
    }


    /**
     * Almacena un nuevo especialista en la base de datos.
     *
     * @param  \Illuminate\Http\Request  $solicitud request que contiene los datos del especialista
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function nuevoEspecialista(Request $solicitud)
    {
        $respuesta = [];
        $codigo = 201;

        $validar = Validator::make($solicitud->all(), [
            'nombre'     => 'required|string|max:100',
            'apellidos'  => 'required|string|max:150',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
            'especialidad' => 'required|string|max:100',
        ]);

        if ($validar->fails()) {
            $respuesta = ['errors' => $validar->errors()];
            $codigo = 422;
        } else {
            $user = User::create([
                'nombre'     => $solicitud->nombre,
                'apellidos'  => $solicitud->apellidos,
                'email'      => $solicitud->email,
                'password'   => Hash::make($solicitud->password),
            ]);

            $user->assignRole('especialista');

            Especialista::create([
                'user_id'       => $user->id,
                'especialidad'  => $solicitud->especialidad,
            ]);

            // Registrar log
            $this->registrarLog('create', 'especialistas', 'user_id', $user->id);

            $respuesta = [
                'message' => 'Especialista creado correctamente',
                'user_id' => $user->id,
            ];
        }

        return response()->json($respuesta, $codigo);
    }
}
