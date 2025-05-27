<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Log;
use App\Http\Traits\Loggable;

class AuthController extends Controller
{

    use Loggable;
    /**
     * Manejo de la solicitud de inicio de sesión.
     *
     * @param Request $solicitud datos de la solicitud HTTP
     * @return \Illuminate\Http\JsonResponse
     */

    public function login(Request $solicitud)
    {
        $solicitud->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $respuesta = [];
        $codigoRespuesta = 200;

        if (!Auth::attempt($solicitud->only('email', 'password'))) {
            $respuesta = [
                'message' => 'Credenciales inválidas',
            ];
            $codigoRespuesta = 401;
        }

        $user = Auth::user();

        $token = $user->createToken('auth_token')->plainTextToken;

        $this->registrarLog($user->id, 'login', 'Inicio de sesión exitoso', 'users');

        $respuesta = [
            'access_token' => $token,
            'user' => [
                'id' => $user->id,
                'nombre' => $user->nombre,
                'apellidos' => $user->apellidos,
                'email' => $user->email,
                'rol' => $user->getRoleNames()->first(),
            ],
        ];

        return response()->json($respuesta, $codigoRespuesta);
    }

    /**
     * Manejo de la solicitud de cierre de sesión.
     * @return \Illuminate\Http\JsonResponse
     */

    public function logout()
    {
        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();

            $this->registrarLog($user->id, 'logout', 'Cierre de sesión exitoso', 'users');
        }

        return response()->json(['message' => 'Sesión cerrada correctamente']);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     *
     * @param Request $solicitud datos de la solicitud HTTP
     * @return \Illuminate\Http\JsonResponse
     */
    public function me(Request $solicitud)
    {
        $respuesta = [];
        $codigoRespuesta = 200;

        $user = ($solicitud)->user();

        if (!$user) {
            Log::create([
                'usuario_id' => null,
                'accion' => 'acceso_me_fallido',
                'descripcion' => 'Token no válido o usuario no autenticado.',
                'tabla_afectada' => 'users',
            ]);

            $respuesta = ['message' => 'No autenticado'];
            $codigoRespuesta = 401;
        } else {
            $this->registrarLog($user->id, 'acceso_me', 'Acceso al endpoint /me', 'users');

            $respuesta = [
                'user' => [
                    'id'        => $user->id,
                    'nombre'    => $user->nombre,
                    'apellidos' => $user->apellidos,
                    'email'     => $user->email,
                    'rol'       => $user->getRoleNames()->first() ?? null,
                ],
            ];
        }

        return response()->json($respuesta, $codigoRespuesta);
    }
}
