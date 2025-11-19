<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultaController extends Controller
{
    /**
     * Vista que carga la página de consultas del paciente
     */
    public function index(Request $request)
    {
        $documento_id = $request->query('documento_id');

        if (!$documento_id) {
            return back()->with('error', 'No se recibió el documento del paciente.');
        }

        // Consultas asociadas al paciente
        $consultas = DB::table('hcd.historia_clinica')
            ->where('documento_id', $documento_id)
            ->orderBy('fecha_atencion', 'DESC')
            ->get();

        return view('paciente.index', [
            'consultas' => $consultas,
            'documento_id' => $documento_id
        ]);
    }

    /**
     * Vista del detalle de una consulta
     */
    public function show($id)
    {
        $consulta = DB::table('hcd.historia_clinica')
            ->where('historia_id', $id)
            ->first();

        if (!$consulta) {
            return back()->with('error', 'Consulta no encontrada.');
        }

        return view('paciente.index', [
            'consulta' => $consulta
        ]);
    }

    /**
     * API: Lista de consultas del paciente (para app / AJAX)
     */
    public function apiConsultas($documento_id)
    {
        $consultas = DB::table('hcd.historia_clinica')
            ->where('documento_id', $documento_id)
            ->orderBy('fecha_atencion', 'DESC')
            ->get();

        return response()->json($consultas);
    }

    /**
     * API: detalle individual de una consulta
     */
    public function apiConsultaDetalle($id)
    {
        $consulta = DB::table('hcd.historia_clinica')
            ->where('historia_id', $id)
            ->first();

        if (!$consulta) {
            return response()->json(['error' => 'Consulta no encontrada'], 404);
        }

        return response()->json($consulta);
    }
}