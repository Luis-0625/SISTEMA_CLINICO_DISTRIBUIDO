@extends('layouts.app')

@section('title', 'Panel del Médico')

@section('styles')
<style>
    .dashboard-container { display: flex; min-height: 100vh; }
    .sidebar { width: 260px; background: linear-gradient(180deg, #0d6e6b, #065f5b); color: #fff; padding: 30px 0; position: fixed; top: 0; bottom: 0; left: 0; }
    .main-content { margin-left: 260px; padding: 40px; width: calc(100% - 260px); }
    .stats-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; margin-bottom: 30px; }
    .stat-card { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); text-align: center; }
    .stat-number { font-size: 2em; font-weight: bold; color: #0d9488; }
    .consultas-list { background: white; border-radius: 12px; padding: 25px; box-shadow: 0 3px 15px rgba(0,0,0,0.08); }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2 style="text-align:center; margin-bottom:30px;">Médico</h2>
        <ul class="nav-links" style="list-style:none; padding:0;">
            <li style="padding:14px 25px;"><a href="{{ route('medico.index') }}" style="color:#fff; text-decoration:none;">🏠 Inicio</a></li>
            <li style="padding:14px 25px;"><a href="{{ route('medico.consultas') }}" style="color:#fff; text-decoration:none;">📋 Mis Consultas</a></li>
            <li style="padding:14px 25px;"><a href="{{ route('medico.pacientes') }}" style="color:#fff; text-decoration:none;">👥 Mis Pacientes</a></li>
            <li style="padding:14px 25px;"><a href="{{ route('medico.agenda') }}" style="color:#fff; text-decoration:none;">📅 Agenda</a></li>
            <li style="padding:14px 25px;"><a href="{{ route('medico.reportes') }}" style="color:#fff; text-decoration:none;">📊 Reportes</a></li>
        </ul>
        <form method="POST" action="{{ route('logout') }}" style="padding: 0 25px;">
            @csrf
            <button type="button" onclick="logout()" style="background:#dc3545; color:white; border:none; padding:12px; width:100%; border-radius:8px; cursor:pointer; margin-top:20px;">
    Cerrar Sesión
</button>
        </form>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header" style="display:flex; justify-content:space-between; align-items:center; margin-bottom:30px;">
            <h1 style="color:#065f5b;">Panel del Médico</h1>
            <div class="user-info" style="display:flex; align-items:center; gap:12px;">
                <span>Bienvenido, Dr. {{ Auth::user()->nombre_usuario }}</span>
            </div>
        </div>

        <!-- Stats -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="pacientesHoy">0</div>
                <div>Pacientes Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="consultasMes">0</div>
                <div>Consultas Este Mes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pendientes">0</div>
                <div>Pendientes</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="urgencias">0</div>
                <div>Urgencias</div>
            </div>
        </div>

        <!-- Consultas Recientes -->
        <div class="consultas-list">
            <h3 style="color:#065f5b; margin-bottom:20px;">Consultas Recientes</h3>
            <div id="consultasRecientes">
                <p style="text-align:center; color:#777;">Cargando consultas...</p>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
// Cargar estadísticas del médico
function cargarEstadisticas() {
    fetch('/api/medico/estadisticas')
        .then(r => r.json())
        .then(data => {
            document.getElementById('pacientesHoy').textContent = data.pacientes_hoy || 0;
            document.getElementById('consultasMes').textContent = data.consultas_mes || 0;
            document.getElementById('pendientes').textContent = data.pendientes || 0;
            document.getElementById('urgencias').textContent = data.urgencias || 0;
        });
}

// Cargar consultas recientes
function cargarConsultasRecientes() {
    fetch('/api/medico/consultas-recientes')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('consultasRecientes');
            if (!data.length) {
                container.innerHTML = '<p style="text-align:center; color:#777;">No hay consultas recientes</p>';
                return;
            }

            let html = '<div style="display:grid; gap:15px;">';
            data.forEach(consulta => {
                html += `
                <div style="padding:15px; border:1px solid #eee; border-radius:8px;">
                    <div style="display:flex; justify-content:space-between;">
                        <strong>${consulta.nombre_completo}</strong>
                        <span style="color:#666;">${consulta.fecha_registro}</span>
                    </div>
                    <div style="margin-top:8px;">
                        <span style="background:#e3f2fd; padding:4px 8px; border-radius:4px; font-size:0.9em;">${consulta.motivo_consulta || 'Consulta médica'}</span>
                    </div>
                </div>`;
            });
            html += '</div>';
            container.innerHTML = html;
        });
}

// Inicializar
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    cargarConsultasRecientes();
});
</script>
@endsection