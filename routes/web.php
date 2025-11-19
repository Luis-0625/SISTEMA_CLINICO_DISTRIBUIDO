<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\AdmisionistaController;
use App\Http\Controllers\PacienteController;
use App\Http\Controllers\MedicoController;
use App\Http\Controllers\CitaController;

// Rutas públicas
Route::get('/login', [LoginController::class, 'index'])->name('login.index');
Route::post('/login', [LoginController::class, 'store'])->name('login.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::prefix('medico')->group(function () {
    Route::get('/medico', [MedicoController::class, 'index'])->name('medico.index');
    Route::get('/consultas', [MedicoController::class, 'consultas'])->name('medico.consultas');
    Route::get('/pacientes', [MedicoController::class, 'pacientes'])->name('medico.pacientes');
    Route::get('/agenda', [MedicoController::class, 'agenda'])->name('medico.agenda');
    Route::get('/reportes', [MedicoController::class, 'reportes'])->name('medico.reportes');
});

// Rutas protegidas
Route::middleware(['auth'])->group(function () {
    // Admisionista
    Route::get('/admisionista', [AdmisionistaController::class, 'index'])->name('admisionista.index');
    Route::get('/admisionista/ingresos', [AdmisionistaController::class, 'ingresos'])->name('admisionista.ingresos');
    Route::get('/admisionista/pacientes', [AdmisionistaController::class, 'pacientes'])->name('admisionista.pacientes');
    Route::get('/admisionista/reportes', [AdmisionistaController::class, 'reportes'])->name('admisionista.reportes');


    // Paciente
     Route::get('/paciente', [PacienteController::class, 'index'])->name('paciente.home');
    Route::get('/paciente/consultas', [PacienteController::class, 'consultas'])->name('paciente.consultas');
    Route::get('/paciente/resultados', [PacienteController::class, 'resultados'])->name('paciente.resultados');
    Route::get('/paciente/citas', [PacienteController::class, 'citas'])->name('paciente.citas');
    Route::get('/paciente/configuracion', [PacienteController::class, 'configuracion'])->name('paciente.configuracion');
    
    // Citas (compartido)
    Route::get('/citas', [CitaController::class, 'index'])->name('citas.index');
});

// API Routes
Route::get('/api/medico/estadisticas', [MedicoController::class, 'getEstadisticas']);
Route::get('/api/medico/consultas-recientes', [MedicoController::class, 'getConsultasRecientes']);
Route::get('/api/admisionista/buscar/{documento}', [AdmisionistaController::class, 'buscarPaciente']);
Route::get('/api/paciente/consultas/{numeroDocumento}', [PacienteController::class, 'obtenerConsultas']);