<?php

// Controller: UsuariosController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Usuario;

class UsuariosController extends Controller
{
    /**
     * Handle user login.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $username = $request->input('username');
        $password = $request->input('password');

        $user = Usuario::where('usuario', $username)->first();

        if (Usuario::validateCredentials($username, $password)) {
            return response()->json([
                'status' => 'success',
                'message' => 'Inicio de sesión exitoso.',
                'user' => $user,
            ], 200);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'Credenciales invalidas.',
        ], 401);
    }
}
