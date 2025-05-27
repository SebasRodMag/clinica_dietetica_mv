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
     * @return \Illuminate\Http\JsonResponse datos del usuario autenticado, token de acceso y código de respuesta HTTP
     * 
     * La función registrarLog está llamada solo si el login es exitoso, pues si falla no hay usuario para identificar.
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
     * 
     * @return \Illuminate\Http\JsonResponse devuelve un mensaje de éxito o error y el código de respuesta HTTP
     */

    public function logout(): JsonResponse
    {
        $codigoRespuesta = 200;
        $respuesta = ['message' => 'Sesión cerrada correctamente'];

        $user = auth()->user();

        if ($user) {
            $user->tokens()->delete();

            $this->registrarLog('users', 'logout', $user->id, 'Cierre de sesión exitoso');
        } else {
            $codigoRespuesta = 401;
            $respuesta = ['message' => 'No autenticado'];
        }

        return response()->json($respuesta, $codigoRespuesta);
    }

    /**
     * Devuelve los datos del usuario autenticado.
     *
     * @param Request $solicitud datos de la solicitud HTTP
     * @return \Illuminate\Http\JsonResponse devuelve los datos del usuario autenticado o un mensaje de error si no hay usuario autenticado
     */
    public function me(Request $solicitud): JsonResponse
    {
        $codigoRespuesta = 200;
        $respuesta = [];

        $user = $solicitud->user();

        if (!$user) {
            //Teme,os una función para registrar logs, pero no se puede usar aquí porque no hay usuario autenticado.
            // Por lo tanto, se registra manualmente.
            Log::create([
                'usuario_id'      => null,
                'accion'          => 'acceso_me_fallido',
                'descripcion'     => 'Token no válido o usuario no autenticado.',
                'tabla_afectada'  => 'users',
                'columna_afectada'=> null,
            ]);

            $codigoRespuesta = 401;
            $respuesta = ['message' => 'No autenticado'];
        } else {
            $this->registrarLog('users', 'acceso_me', $user->id, 'Acceso al endpoint /me');

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
