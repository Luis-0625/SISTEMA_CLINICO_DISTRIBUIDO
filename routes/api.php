<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Middleware\RolMiddleware;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\ConsultaController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/
// Rutas de Citas
Route::get('/citas', [CitaController::class, 'getCitas']);
Route::get('/citas/medicos', [CitaController::class, 'getMedicos']);
Route::get('/citas/proximas', [CitaController::class, 'getCitasProximas']);
Route::get('/citas/{citaId}', [CitaController::class, 'show']);
Route::post('/citas', [CitaController::class, 'store']);
Route::put('/citas/{citaId}/estado', [CitaController::class, 'updateEstado']);

// Rutas de Admisionista
Route::get('/admisionista/buscar/{documento}', [AdmisionistaController::class, 'buscarPaciente']);
Route::post('/admisionista/registrar-ingreso', [AdmisionistaController::class, 'registrarIngreso']);
Route::get('/admisionista/ingresos-recientes', [AdmisionistaController::class, 'obtenerIngresosRecientes']);
Route::get('/admisionista/pacientes', [AdmisionistaController::class, 'getPacientesAdmisionista']);

// Rutas de Médico
Route::get('/medico/estadisticas', [MedicoController::class, 'getEstadisticas']);
Route::get('/medico/consultas-recientes', [MedicoController::class, 'getConsultasRecientes']);

// Rutas de Paciente
Route::get('/paciente/consultas/{numeroDocumento}', [PacienteController::class, 'obtenerConsultas']);
Route::get('/paciente/consultas/{documento}/filtradas', [PacienteController::class, 'obtenerConsultasPaciente']);
Route::get('/paciente/consulta/{hcId}', [PacienteController::class, 'obtenerDetalleConsulta']);
Route::get('/paciente/resultados/{documento}', [PacienteController::class, 'obtenerResultadosPaciente']);

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});