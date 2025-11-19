<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
{
    /**
     * Mostrar formulario de login.
     */
    public function index()
    {
        return view('login.index');
    }

    /**
     * Procesar el inicio de sesión.
     */
    public function store(Request $request)
    {
        // Validar datos
        $request->validate([
            'documento_id' => 'required|string',
            'contrasena' => 'required|string',
        ]);

        // Buscar usuario por documento
        $usuario = User::where('documento_id', $request->documento_id)->first();

        if (! $usuario) {
            return back()->with('error', 'Usuario no encontrado.');
        }

        if (! $usuario->activo) {
            return back()->with('error', 'El usuario está inactivo.');
        }

        // Verificar contraseña
        if (! Hash::check($request->contrasena, $usuario->contrasena)) {
            return back()->with('error', 'Contraseña incorrecta.');
        }

        // Actualizar última fecha de login
        $usuario->ultimo_login = now();
        $usuario->save();

        // Iniciar sesión
        Auth::Login($usuario);

        // Redirigir por rol
        return $this->redireccionarPorRol($usuario->rol_id);
    }

    /**
     * Cerrar sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login.index');
    }

    /**
     * Redirección por roles reales.
     */
    private function redireccionarPorRol($rol)
    {
        switch ($rol) {
            case 'admisionista':
                return redirect()->route('admisionista.index');

            case 'medico':
                return redirect()->route('medico.index');

            case 'paciente':
                return redirect()->route('paciente.home');

            default:
                return redirect()->route('login.index');
        }
    }
}
