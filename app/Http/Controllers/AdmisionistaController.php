<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class AdmisionistaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view("admisionista.index");
    }

    /**
     * Vista de gestión de ingresos
     */
    public function ingresos()
    {
        return view('admisionista.ingresos');
    }

    /**
     * Vista de gestión de pacientes
     */
    public function pacientes()
    {
        return view('admisionista.pacientes');
    }

    /**
     * Vista de reportes
     */
    public function reportes()
    {
        return view('admisionista.reportes');
    }

    /**
     * API: Buscar paciente por documento
     */
    public function buscarPaciente($documento)
    {
        $usuario = DB::table('usuario')
            ->where('documento_id', $documento)
            ->first();

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Paciente no encontrado'
            ]);
        }

        return response()->json([
            'success' => true,
            'data' => [$usuario]
        ]);
    }

    /**
     * API: Registrar nuevo ingreso
     */
    public function registrarIngreso(Request $request)
    {
        $request->validate([
            'documento_paciente' => 'required|exists:usuario,documento_id',
            'tipo_ingreso' => 'required|in:urgencias,consulta,hospitalizacion,procedimiento',
            'motivo_consulta' => 'required|string|max:500',
            'prioridad' => 'required|in:normal,urgente,emergencia'
        ]);

        try {
            DB::beginTransaction();

            // Crear la atención médica
            $atencionId = DB::table('atencion')->insertGetId([
                'documento_id' => $request->documento_paciente,
                'entidad_salud' => 'IPS Centro Salud',
                'fecha_ingreso' => now(),
                'modalidad_entrega' => 'Presencial',
                'entorno_atencion' => $this->mapearTipoIngreso($request->tipo_ingreso),
                'via_ingreso' => 'Admisionista',
                'causa_atencion' => $request->motivo_consulta,
                'clasificacion_triage' => $this->mapearPrioridad($request->prioridad),
                'estado' => 'activa'
            ]);

            // Crear registro en HC
            $hcId = DB::table('hc')->insertGetId([
                'hc_id' => \Illuminate\Support\Str::uuid(),
                'documento_id' => $request->documento_paciente,
                'atencion_id' => $atencionId,
                'motivo_consulta' => $request->motivo_consulta,
                'fecha_registro' => now(),
                'prioridad_atencion' => $request->prioridad,
                'estado_paciente' => 'ingresado'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Ingreso registrado exitosamente',
                'atencion_id' => $atencionId,
                'hc_id' => $hcId
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error al registrar ingreso: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Obtener ingresos recientes
     */
    public function obtenerIngresosRecientes()
    {
        $ingresos = DB::table('atencion as at')
            ->join('usuario as u', 'at.documento_id', '=', 'u.documento_id')
            ->select(
                'at.atencion_id',
                'at.fecha_ingreso',
                'at.entorno_atencion',
                'at.causa_atencion as motivo_consulta',
                'u.nombre_completo',
                'u.documento_id'
            )
            ->whereDate('at.fecha_ingreso', '>=', now()->subDays(7))
            ->orderBy('at.fecha_ingreso', 'DESC')
            ->limit(10)
            ->get();

        return response()->json($ingresos);
    }

    /**
     * API: Lista de pacientes para admisionista
     */
    public function getPacientesAdmisionista(Request $request)
    {
        $page = $request->get('page', 1);
        $limit = $request->get('limit', 10);
        $search = $request->get('search');
        $estado = $request->get('estado');
        $edad = $request->get('edad');

        $query = DB::table('usuario')
            ->leftJoin('login_usuario', 'usuario.documento_id', '=', 'login_usuario.documento_id')
            ->select(
                'usuario.*',
                'login_usuario.activo',
                'login_usuario.correo'
            );

        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('usuario.nombre_completo', 'ILIKE', "%{$search}%")
                  ->orWhere('usuario.documento_id', 'ILIKE', "%{$search}%")
                  ->orWhere('login_usuario.correo', 'ILIKE', "%{$search}%");
            });
        }

        if ($estado) {
            $query->where('login_usuario.activo', $estado === 'activo');
        }

        // Paginación manual para Citus
        $total = $query->count();
        $pacientes = $query->skip(($page - 1) * $limit)
                          ->take($limit)
                          ->get();

        return response()->json([
            'pacientes' => $pacientes,
            'current_page' => (int)$page,
            'total_pages' => ceil($total / $limit),
            'total' => $total
        ]);
    }

    /**
     * Helper: Mapear tipo de ingreso a entorno de atención
     */
    private function mapearTipoIngreso($tipoIngreso)
    {
        $mapeo = [
            'urgencias' => 'Urgencias',
            'consulta' => 'Consulta',
            'hospitalizacion' => 'Hospitalización',
            'procedimiento' => 'Procedimiento'
        ];

        return $mapeo[$tipoIngreso] ?? 'Consulta';
    }

    /**
     * Helper: Mapear prioridad a triage
     */
    private function mapearPrioridad($prioridad)
    {
        $mapeo = [
            'normal' => '4',
            'urgente' => '2',
            'emergencia' => '1'
        ];

        return $mapeo[$prioridad] ?? '4';
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}