<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Http\Traits\Loggable;

class UserController extends Controller
{
    use Loggable;

    /**
     * 
     * Función para llistar todos los usuarios.
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con el listado de usuarios.
     * 
     * 
     *  */    
    public function listarTodos(): JsonResponse
    {
        $usuarios = User::all();
        $codigo = 200;
        $respuesta = $usuarios;

        if ($usuarios->isEmpty()) {
            $respuesta = ['message' => 'No hay usuarios registrados'];
            $codigo = 404;
            $this->registrarLog(auth()->id(), 'listar', 'No hay usuarios registrados', 'users');
        } else {
            $this->registrarLog(auth()->id(), 'listar', 'Listar todos los usuarios', 'users');
        }

        return response()->json($respuesta, $codigo);
    }

    /**
     * 
     * Función para mostrar un usuario por ID.
     * 
     * @param int $id id del usuario a buscar
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con los datos del usuario o un mensaje de error.
     * 
     */
    public function verUsuario($id): JsonResponse
    {
        $codigo = 200;

        if (!is_numeric($id)) {
            $respuesta = ['mensaje' => 'ID inválido. Debe ser numérico'];
            $codigo = 400;
        } else {
            $usuario = User::find($id);

            if (!$usuario) {
                $respuesta = ['mensaje' => 'Usuario no encontrado'];
                $codigo = 404;
            } else {
                $respuesta = $usuario;
            }

            $this->registrarLog(auth()->id(), 'ver', "Ver usuario ID: $id", 'users');
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * 
     * Función para crear un nuevo usuario.
     * * Valida los datos de entrada y crea un nuevo usuario en la base de datos.
     * 
     * @param Request $solicitud datos del usuario nuevo
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con los datos del usuario creado o un mensaje de error.
     */
    public function insertarUsuario(Request $solicitud): JsonResponse
    {
        $codigo = 201;

        $validador = Validator::make($solicitud->all(), [
            'nombre'     => 'required|string|max:255',
            'apellidos'  => 'required|string|max:255',
            'email'      => 'required|email|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        if ($validador->fails()) {
            $respuesta = ['errores' => $validador->errors()];
            $codigo = 422;
        } else {
            $usuario = User::create([
                'nombre'     => $solicitud->input('nombre'),
                'apellidos'  => $solicitud->input('apellidos'),
                'email'      => $solicitud->input('email'),
                'password'   => Hash::make($solicitud->input('password')),
            ]);

            $respuesta = $usuario;

            $this->registrarLog(auth()->id(), 'crear', "Nuevo usuario ID: {$usuario->id}", 'users');
        }

        return response()->json($respuesta, $codigo);
    }



    /**
     * 
     * Función para actualizar un usuario por ID.
     * * Valida los datos de entrada y actualiza el usuario en la base de datos.
     * 
     * @param Request $solicitud datos del usuario a actualizar
     * @param int $id ID del usuario que se va a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $solicitud, $id)
    {
        $respuesta = [];
        $codigo = 200;

        if (!is_numeric($id)) {
            $respuesta = ['mensaje' => 'ID inválido'];
            $codigo = 400;
        } else {
            $usuario = User::find($id);

            if (!$usuario) {
                $respuesta = ['mensaje' => 'Usuario no encontrado'];
                $codigo = 404;
            } else {
                $usuario->update($solicitud->only(['nombre', 'apellidos', 'email']));
                $respuesta = $usuario;

                $this->registrarLog(auth()->id(), 'actualizar', "Usuario ID: $id", 'users');
            }
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * 
     * Función para eliminar un usuario por ID (SoftDelete).
     * Valida el ID y elimina el usuario de forma segura.
     * 
     * @param int $id ID del usuario que se va a eliminar
     * @return \Illuminate\Http\JsonResponse devuelve una respuesta JSON con un mensaje de éxito o error.
     */
    public function borrarUsuario($id)
    {
        $respuesta = [];
        $codigo = 200;

        if (!is_numeric($id)) {
            $respuesta = ['mensaje' => 'ID inválido'];
            $codigo = 400;
        } else {
            $usuario = User::find($id);

            if (!$usuario) {
                $respuesta = ['mensaje' => 'Usuario no encontrado'];
                $codigo = 404;
            } else {
                $usuario->delete();
                $respuesta = ['mensaje' => 'Usuario eliminado correctamente'];

                $this->registrarLog(auth()->id(), 'eliminar', "Usuario ID: $id", 'users');
            }
        }

        return response()->json($respuesta, $codigo);
    }


}
