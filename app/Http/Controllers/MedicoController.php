<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class MedicoController extends Controller
{
    /**
     * Panel principal del médico
     */
    public function index()
    {
        return view('medico.index');
    }

    /**
     * API: Estadísticas del médico
     */
    public function getEstadisticas()
    {
        $medicoDocumento = Auth::user()->documento_id;

        // Consultas optimizadas para Citus (co-localizadas por documento_id)
        $estadisticas = [
            'pacientes_hoy' => DB::table('hc')
                ->where('profesional_responsable', $medicoDocumento)
                ->whereDate('fecha_registro', today())
                ->distinct('documento_id')
                ->count('documento_id'),

            'consultas_mes' => DB::table('hc')
                ->where('profesional_responsable', $medicoDocumento)
                ->whereMonth('fecha_registro', now()->month)
                ->count(),

            'pendientes' => DB::table('hc')
                ->where('profesional_responsable', $medicoDocumento)
                ->whereNull('diagnostico_principal')
                ->count(),

            'urgencias' => DB::table('hc')
                ->join('atencion', 'hc.atencion_id', '=', 'atencion.atencion_id')
                ->where('hc.profesional_responsable', $medicoDocumento)
                ->where('atencion.entorno_atencion', 'Urgencias')
                ->whereDate('hc.fecha_registro', today())
                ->count()
        ];

        return response()->json($estadisticas);
    }

    /**
     * API: Consultas recientes del médico
     */
    public function getConsultasRecientes()
    {
        $medicoDocumento = Auth::user()->documento_id;

        $consultas = DB::table('hc')
            ->join('usuario', 'hc.documento_id', '=', 'usuario.documento_id') // JOIN co-localizado
            ->where('hc.profesional_responsable', $medicoDocumento)
            ->orderBy('hc.fecha_registro', 'DESC')
            ->limit(5)
            ->select(
                'hc.hc_id',
                'usuario.nombre_completo',
                'hc.motivo_consulta',
                'hc.fecha_registro',
                'hc.estado_paciente'
            )
            ->get();

        return response()->json($consultas);
    }

    /**
     * Vista de consultas del médico
     */
    public function consultas()
    {
        return view('medico.consultas');
    }

    /**
     * Vista de pacientes del médico
     */
    public function pacientes()
    {
        return view('medico.pacientes');
    }

    /**
     * Vista de agenda del médico
     */
    public function agenda()
    {
        return view('medico.agenda');
    }

    /**
     * Vista de reportes del médico
     */
    public function reportes()
    {
        return view('medico.reportes');
    }
}