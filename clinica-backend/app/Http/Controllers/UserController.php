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
     * Funcion para llistar todos los usuarios.
     * * @return \Illuminate\Http\JsonResponse
     * 
     * 
     *  */    
    public function listarTodos()
    {
        $usuarios = User::all();

        $this->registrarLog(auth()->id(), 'listar', 'Listar todos los usuarios', 'users');

        return response()->json($usuarios);
    }

    /**
     * 
     * Funcion para mostrar un usuario por ID.
     * 
     * @param int $id id del usuario a buscar
     * @return \Illuminate\Http\JsonResponse
     */
    public function verUsuario($id)
    {
        $usuario = User::find($id);

        $respuesta = [];
        $codigo = 200;

        if (!$usuario) {
            $respuesta = ['mensaje' => 'Usuario no encontrado'];
            $codigo = 404;
        } else {
            $respuesta = $usuario;
        }

        $this->registrarLog(auth()->id(), 'ver', "Ver usuario ID: $id", 'users');

        return response()->json($respuesta, $codigo);
    }


    /**
     * 
     * Funcion para crear un nuevo usuario.
     * 
     * @param Request $solicitud datos del usuario nuevo
     * @return \Illuminate\Http\JsonResponse
     */
    public function insertarUsuario(Request $solicitud)
    {
        $validacion = Validator::make($solicitud->all(), [
            'nombre' => 'required|string|max:255',
            'apellidos' => 'required|string|max:255',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $respuesta = [];
        $codigo = 201;

        if ($validacion->fails()) {
            $respuesta = ['errores' => $validacion->errors()];
            $codigo = 422;
        } else {
            $usuario = User::create([
                'nombre'    => $solicitud->nombre,
                'apellidos' => $solicitud->apellidos,
                'email'     => $solicitud->email,
                'password'  => Hash::make($solicitud->password),
            ]);

            $respuesta = $usuario;

            $this->registrarLog(auth()->id(), 'crear', "Nuevo usuario ID: {$usuario->id}", 'users');
        }

        return response()->json($respuesta, $codigo);
    }


    /**
     * 
     * Función para actualizar un usuario por ID.
     * 
     * @param Request $solicitud
     * @param int $id ID del usuario que se va a actualizar
     * @return \Illuminate\Http\JsonResponse
     */
    public function actualizar(Request $solicitud, $id)
    {
        $usuario = User::find($id);

        $respuesta = [];
        $codigo = 200;

        if (!$usuario) {
            $respuesta = ['mensaje' => 'Usuario no encontrado'];
            $codigo = 404;
        } else {
            $usuario->update($solicitud->only(['nombre', 'apellidos', 'email']));
            $respuesta = $usuario;

            $this->registrarLog(auth()->id(), 'actualizar', "Usuario ID: $id", 'users');
        }

        return response()->json($respuesta, $codigo);
    }

    /**
     * 
     * Función para eliminar un usuario por ID (SoftDelete).
     * 
     * @param int $id ID del usuario que se va a eliminar
     * @return \Illuminate\Http\JsonResponse
     */
    public function borrarUsuario($id)
    {
        $usuario = User::find($id);

        $respuesta = [];
        $codigo = 200;

        if (!$usuario) {
            $respuesta = ['mensaje' => 'Usuario no encontrado'];
            $codigo = 404;
        } else {
            $usuario->delete();
            $respuesta = ['mensaje' => 'Usuario eliminado correctamente'];

            $this->registrarLog(auth()->id(), 'eliminar', "Usuario ID: $id", 'users');
        }

        return response()->json($respuesta, $codigo);
    }

}
