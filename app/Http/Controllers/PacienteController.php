<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class PacienteController extends Controller
{
    /**
     * Vista principal del paciente (/paciente)
     */
    public function index()
    {
        $paciente = Auth::user();  
        return view('paciente.index', compact('paciente'));
    }

    /**
     * Vista de consultas del paciente
     */
    public function consultas()
    {
        return view('paciente.consultas');
    }

    /**
     * Vista de resultados del paciente
     */
    public function resultados()
    {
        return view('paciente.resultados');
    }

    /**
     * Vista de citas del paciente
     */
    public function citas()
    {
        return view('paciente.citas');
    }

    /**
     * Vista de configuración del paciente
     */
    public function configuracion()
    {
        return view('paciente.configuracion');
    }

    /**
     * API: Obtener datos del paciente autenticado
     */
    public function getPacienteData()
    {
        return response()->json([
            'status' => 'success',
            'paciente' => Auth::user()
        ]);
    }

    /**
     * API: Obtener consultas de un paciente por número real (CC)
     * Endpoint: /api/paciente/consultas/{numeroDocumento}
     * (MÉTODO EXISTENTE - MANTENER)
     */
    public function obtenerConsultas($numeroDocumento)
    {
        // 1. Buscar paciente por su número de documento real
        $paciente = DB::table('usuario')
            ->where('numero_documento', $numeroDocumento)
            ->first();

        if (!$paciente) {
            return response()->json([]);
        }

        $documentoUUID = $paciente->documento_id;

        // 2. Consultar historia clínica + atención médica + médico + CIE10
        $consultas = DB::table('historia_clinica as hc')
            ->join('atencion_medica as am', 'hc.atencion_id', '=', 'am.atencion_id')
            ->leftJoin('diagnostico as d', 'hc.diagnostico_id', '=', 'd.diagnostico_id')
            ->leftJoin('login_usuario as lu', 'am.medico_id', '=', 'lu.documento_id')
            ->where('hc.documento_id', $documentoUUID)
            ->select(
                'am.fecha_atencion as fecha',
                DB::raw("COALESCE(am.motivo_consulta, 'Consulta médica') AS especialidad"),
                DB::raw("COALESCE(lu.nombre_usuario, 'Médico') AS medico"),
                DB::raw("COALESCE(d.cie10_codigo, 'Sin código') AS diagnostico"),
                DB::raw("COALESCE(hc.plan_tratamiento, 'Sin tratamiento registrado') AS tratamiento")
            )
            ->orderBy('am.fecha_atencion', 'DESC')
            ->get();

        return response()->json($consultas);
    }

    /**
     * API: Obtener consultas del paciente con filtros (NUEVO MÉTODO)
     * Endpoint: /api/paciente/consultas/{documento}
     */
    public function obtenerConsultasPaciente($documento, Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 5);
        $fechaDesde = $request->get('fecha_desde');
        $fechaHasta = $request->get('fecha_hasta');
        $especialidad = $request->get('especialidad');
        $estado = $request->get('estado');

        // Verificar que el paciente solo vea sus propias consultas
        if ($documento != Auth::user()->documento_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para ver estas consultas'
            ], 403);
        }

        // Usar la estructura correcta de tu base de datos
        $query = DB::table('hc')
            ->leftJoin('atencion as at', 'hc.atencion_id', '=', 'at.atencion_id')
            ->leftJoin('profesional_salud as ps', 'at.documento_medico', '=', 'ps.documento_id')
            ->where('hc.documento_id', $documento)
            ->select(
                'hc.hc_id',
                'hc.fecha_registro',
                'hc.motivo_consulta',
                'hc.sintomas_principales',
                'hc.diagnostico_principal',
                'hc.plan_tratamiento',
                'hc.observaciones',
                'hc.estado_paciente',
                'hc.signos_vitales_peso',
                'hc.signos_vitales_talla',
                'hc.signos_vitales_imc',
                'hc.signos_vitales_fc',
                'hc.signos_vitales_ta',
                'at.entorno_atencion as especialidad',
                'at.entidad_salud',
                'ps.nombre as medico'
            );

        // Aplicar filtros
        if ($fechaDesde) {
            $query->whereDate('hc.fecha_registro', '>=', $fechaDesde);
        }

        if ($fechaHasta) {
            $query->whereDate('hc.fecha_registro', '<=', $fechaHasta);
        }

        if ($especialidad) {
            $query->where('at.entorno_atencion', 'ILIKE', "%{$especialidad}%");
        }

        if ($estado) {
            $query->where('hc.estado_paciente', $estado);
        }

        // Contar total para paginación
        $total = $query->count();

        // Obtener datos paginados
        $consultas = $query->orderBy('hc.fecha_registro', 'DESC')
            ->skip(($page - 1) * $limit)
            ->take($limit)
            ->get();

        return response()->json([
            'consultas' => $consultas,
            'current_page' => (int)$page,
            'total_pages' => ceil($total / $limit),
            'total' => $total
        ]);
    }

    /**
     * API: Obtener detalle completo de una consulta (NUEVO MÉTODO)
     * Endpoint: /api/paciente/consulta/{hcId}
     */
    public function obtenerDetalleConsulta($hcId)
    {
        $consulta = DB::table('hc')
            ->leftJoin('atencion as at', 'hc.atencion_id', '=', 'at.atencion_id')
            ->leftJoin('profesional_salud as ps', 'at.documento_medico', '=', 'ps.documento_id')
            ->where('hc.hc_id', $hcId)
            ->select('hc.*', 'at.entorno_atencion as especialidad', 'at.entidad_salud', 'ps.nombre as medico')
            ->first();

        if (!$consulta) {
            return response()->json([
                'success' => false,
                'message' => 'Consulta no encontrada'
            ], 404);
        }

        // Verificar que el paciente solo vea sus propias consultas
        if ($consulta->documento_id != Auth::user()->documento_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado para ver esta consulta'
            ], 403);
        }

        return response()->json([
            'success' => true,
            'consulta' => $consulta
        ]);
    }

    /**
     * API: Obtener resultados clínicos del paciente (NUEVO MÉTODO)
     */
    public function obtenerResultadosPaciente($documento, Request $request)
    {
        $tipo = $request->get('tipo');
        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        // Verificar autorización
        if ($documento != Auth::user()->documento_id) {
            return response()->json([
                'success' => false,
                'message' => 'No autorizado'
            ], 403);
        }

        $query = DB::table('resultados_clinicos')
            ->where('documento_id', $documento)
            ->orderBy('fecha_realizacion', 'DESC');

        if ($tipo) {
            $query->where('tipo_examen', $tipo);
        }

        if ($desde) {
            $query->whereDate('fecha_realizacion', '>=', $desde);
        }

        if ($hasta) {
            $query->whereDate('fecha_realizacion', '<=', $hasta);
        }

        $resultados = $query->get();

        return response()->json($resultados);
    }
}