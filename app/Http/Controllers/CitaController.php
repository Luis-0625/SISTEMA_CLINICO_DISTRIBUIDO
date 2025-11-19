<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class CitaController extends Controller
{
    /**
     * Vista principal de gestión de citas
     */
    public function index()
    {
        return view('citas.index');
    }

    /**
     * API: Obtener todas las citas (para el calendario)
     */
    public function getCitas(Request $request)
    {
        $user = Auth::user();
        $citas = [];

        if ($user->rol_id === 'admisionista') {
            // Admisionista ve todas las citas
            $citas = DB::table('citas_medicas as cm')
                ->join('usuario as up', 'cm.documento_paciente', '=', 'up.documento_id')
                ->join('profesional_salud as ps', 'cm.documento_medico', '=', 'ps.documento_id')
                ->select(
                    'cm.cita_id as id',
                    'cm.fecha_cita as start',
                    DB::raw("CONCAT(up.nombre_completo, ' - ', ps.nombre, ' - ', cm.motivo_consulta) as title"),
                    'cm.estado',
                    'cm.especialidad'
                )
                ->where('cm.fecha_cita', '>=', now()->subDays(30))
                ->get();
        } elseif ($user->rol_id === 'medico') {
            // Médico ve solo sus citas
            $citas = DB::table('citas_medicas as cm')
                ->join('usuario as up', 'cm.documento_paciente', '=', 'up.documento_id')
                ->join('profesional_salud as ps', 'cm.documento_medico', '=', 'ps.documento_id')
                ->select(
                    'cm.cita_id as id',
                    'cm.fecha_cita as start',
                    DB::raw("CONCAT(up.nombre_completo, ' - ', cm.motivo_consulta) as title"),
                    'cm.estado',
                    'cm.especialidad'
                )
                ->where('cm.documento_medico', $user->documento_id)
                ->where('cm.fecha_cita', '>=', now()->subDays(30))
                ->get();
        } elseif ($user->rol_id === 'paciente') {
            // Paciente ve solo sus citas
            $citas = DB::table('citas_medicas as cm')
                ->join('usuario as up', 'cm.documento_paciente', '=', 'up.documento_id')
                ->join('profesional_salud as ps', 'cm.documento_medico', '=', 'ps.documento_id')
                ->select(
                    'cm.cita_id as id',
                    'cm.fecha_cita as start',
                    DB::raw("CONCAT(ps.nombre, ' - ', cm.motivo_consulta) as title"),
                    'cm.estado',
                    'cm.especialidad'
                )
                ->where('cm.documento_paciente', $user->documento_id)
                ->where('cm.fecha_cita', '>=', now()->subDays(30))
                ->get();
        }

        // Formatear para FullCalendar
        $formattedCitas = $citas->map(function ($cita) {
            $color = $this->getColorByEstado($cita->estado);
            return [
                'id' => $cita->id,
                'title' => $cita->title,
                'start' => $cita->start,
                'color' => $color,
                'extendedProps' => [
                    'estado' => $cita->estado,
                    'especialidad' => $cita->especialidad
                ]
            ];
        });

        return response()->json($formattedCitas);
    }

    /**
     * API: Obtener lista de médicos para select
     */
    public function getMedicos()
    {
        $medicos = DB::table('profesional_salud')
            ->where('activo', true)
            ->select('documento_id', 'nombre', 'especialidad')
            ->get();

        return response()->json($medicos);
    }

    /**
     * API: Crear nueva cita
     */
    public function store(Request $request)
    {
        $request->validate([
            'documento_paciente' => 'required|exists:usuario,documento_id',
            'documento_medico' => 'required|exists:profesional_salud,documento_id',
            'fecha_cita' => 'required|date|after:now',
            'motivo_consulta' => 'required|string|max:500',
            'especialidad' => 'required|string|max:100'
        ]);

        try {
            // Verificar disponibilidad del médico
            $citaExistente = DB::table('citas_medicas')
                ->where('documento_medico', $request->documento_medico)
                ->where('fecha_cita', '>=', $request->fecha_cita)
                ->where('fecha_cita', '<', date('Y-m-d H:i:s', strtotime($request->fecha_cita . ' + 30 minutes')))
                ->whereIn('estado', ['programada', 'confirmada'])
                ->first();

            if ($citaExistente) {
                return response()->json([
                    'success' => false,
                    'message' => 'El médico ya tiene una cita programada en ese horario'
                ], 409);
            }

            // Crear la cita
            $citaId = DB::table('citas_medicas')->insertGetId([
                'cita_id' => \Illuminate\Support\Str::uuid(),
                'documento_paciente' => $request->documento_paciente,
                'documento_medico' => $request->documento_medico,
                'fecha_cita' => $request->fecha_cita,
                'motivo_consulta' => $request->motivo_consulta,
                'especialidad' => $request->especialidad,
                'creado_por' => Auth::user()->documento_id,
                'creado_en' => now(),
                'actualizado_en' => now()
            ]);

            // Crear notificación para el paciente
            $paciente = DB::table('usuario')
                ->where('documento_id', $request->documento_paciente)
                ->first();

            $medico = DB::table('profesional_salud')
                ->where('documento_id', $request->documento_medico)
                ->first();

            DB::table('notificaciones')->insert([
                'notificacion_id' => \Illuminate\Support\Str::uuid(),
                'documento_destinatario' => $request->documento_paciente,
                'tipo_notificacion' => 'cita_programada',
                'titulo' => 'Nueva Cita Programada',
                'mensaje' => "Tiene una cita programada con {$medico->nombre} para " . 
                            date('d/m/Y H:i', strtotime($request->fecha_cita)),
                'prioridad' => 'normal',
                'fecha_envio' => now()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Cita agendada exitosamente',
                'cita_id' => $citaId
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error al agendar la cita: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * API: Actualizar estado de cita
     */
    public function updateEstado(Request $request, $citaId)
    {
        $request->validate([
            'estado' => 'required|in:confirmada,cancelada,completada,no_asistio'
        ]);

        $user = Auth::user();

        // Verificar permisos
        $cita = DB::table('citas_medicas')
            ->where('cita_id', $citaId)
            ->first();

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        // Verificar que el usuario tenga permisos para modificar esta cita
        if ($user->rol_id === 'paciente' && $cita->documento_paciente !== $user->documento_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para modificar esta cita'
            ], 403);
        }

        if ($user->rol_id === 'medico' && $cita->documento_medico !== $user->documento_id) {
            return response()->json([
                'success' => false,
                'message' => 'No tiene permisos para modificar esta cita'
            ], 403);
        }

        DB::table('citas_medicas')
            ->where('cita_id', $citaId)
            ->update([
                'estado' => $request->estado,
                'actualizado_en' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Estado de cita actualizado correctamente'
        ]);
    }

    /**
     * API: Obtener detalles de una cita
     */
    public function show($citaId)
    {
        $cita = DB::table('citas_medicas as cm')
            ->join('usuario as up', 'cm.documento_paciente', '=', 'up.documento_id')
            ->join('profesional_salud as ps', 'cm.documento_medico', '=', 'ps.documento_id')
            ->leftJoin('usuario as uc', 'cm.creado_por', '=', 'uc.documento_id')
            ->select(
                'cm.*',
                'up.nombre_completo as paciente_nombre',
                'up.telefono as paciente_telefono',
                'up.correo as paciente_correo',
                'ps.nombre as medico_nombre',
                'ps.especialidad as medico_especialidad',
                'uc.nombre_completo as creador_nombre'
            )
            ->where('cm.cita_id', $citaId)
            ->first();

        if (!$cita) {
            return response()->json([
                'success' => false,
                'message' => 'Cita no encontrada'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'cita' => $cita
        ]);
    }

    /**
     * Helper: Obtener color según estado de cita
     */
    private function getColorByEstado($estado)
    {
        switch ($estado) {
            case 'programada':
                return '#007bff'; // Azul
            case 'confirmada':
                return '#28a745'; // Verde
            case 'en_proceso':
                return '#ffc107'; // Amarillo
            case 'completada':
                return '#6c757d'; // Gris
            case 'cancelada':
                return '#dc3545'; // Rojo
            case 'no_asistio':
                return '#fd7e14'; // Naranja
            default:
                return '#6c757d'; // Gris por defecto
        }
    }
}