@extends('layouts.app')

@section('title', 'Gestión de Ingresos - Admisionista')

@section('styles')
<style>
    .ingresos-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background: linear-gradient(180deg, #0d6e6b, #065f5b);
        color: #fff;
        padding: 30px 0;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
    }

    .sidebar h2 {
        text-align: center;
        font-size: 1.7em;
        margin-bottom: 25px;
        font-weight: 700;
    }

    .nav-links {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .nav-links li {
        padding: 14px 25px;
        transition: 0.25s ease;
    }

    .nav-links li:hover {
        background-color: rgba(255, 255, 255, 0.12);
        padding-left: 35px;
    }

    .nav-links a {
        color: #fff;
        text-decoration: none;
        font-size: 1em;
        font-weight: 500;
    }

    .logout-btn {
        background-color: #dc3545;
        border: none;
        padding: 12px;
        width: 85%;
        margin: 25px auto;
        display: block;
        color: #fff;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1em;
        font-weight: 600;
        transition: 0.3s;
    }

    .logout-btn:hover {
        background-color: #b02a37;
    }

    .main-content {
        margin-left: 260px;
        padding: 40px 50px;
        width: calc(100% - 260px);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
        margin-bottom: 30px;
    }

    .header h1 {
        font-size: 2em;
        color: #065f5b;
        font-weight: 700;
        margin: 0;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
        font-weight: 600;
    }

    .user-info img {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        border: 3px solid #0d9488;
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        text-align: center;
        border-left: 4px solid #0d9488;
    }

    .stat-number {
        font-size: 2em;
        font-weight: bold;
        color: #0d9488;
        margin: 10px 0;
    }

    .stat-label {
        color: #666;
        font-size: 0.9em;
    }

    /* Quick Actions */
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
        margin-bottom: 30px;
    }

    .action-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }

    .action-card:hover {
        transform: translateY(-5px);
        border-color: #0d9488;
        box-shadow: 0 5px 20px rgba(13, 148, 136, 0.2);
    }

    .action-icon {
        font-size: 2.5em;
        margin-bottom: 15px;
    }

    .action-title {
        font-weight: 600;
        color: #065f5b;
        margin-bottom: 8px;
    }

    .action-description {
        color: #666;
        font-size: 0.9em;
    }

    /* Forms Section */
    .forms-section {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 30px;
    }

    .form-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }

    .form-card h3 {
        color: #065f5b;
        margin-bottom: 20px;
        font-size: 1.3em;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
        color: #333;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 8px;
        font-size: 14px;
        transition: border-color 0.3s;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #0d9488;
        outline: none;
        box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
    }

    .btn {
        padding: 12px 24px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s;
    }

    .btn-primary {
        background: #0d9488;
        color: white;
        width: 100%;
    }

    .btn-primary:hover {
        background: #0b8078;
        transform: translateY(-2px);
    }

    .btn-success {
        background: #28a745;
        color: white;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
    }

    /* Recent Activity */
    .activity-card {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 3px 15px rgba(0,0,0,0.08);
    }

    .activity-list {
        max-height: 300px;
        overflow-y: auto;
    }

    .activity-item {
        display: flex;
        align-items: center;
        padding: 15px;
        border-bottom: 1px solid #eee;
        gap: 15px;
    }

    .activity-item:last-child {
        border-bottom: none;
    }

    .activity-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: #e3f2fd;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2em;
    }

    .activity-content {
        flex: 1;
    }

    .activity-title {
        font-weight: 600;
        margin-bottom: 4px;
    }

    .activity-time {
        color: #666;
        font-size: 0.8em;
    }

    /* Modal */
    .modal {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0,0,0,0.5);
        z-index: 1000;
    }

    .modal-content {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: white;
        padding: 30px;
        border-radius: 12px;
        width: 90%;
        max-width: 500px;
        max-height: 80vh;
        overflow-y: auto;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .stats-grid,
        .quick-actions,
        .forms-section {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 768px) {
        .stats-grid,
        .quick-actions,
        .forms-section {
            grid-template-columns: 1fr;
        }
        
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 20px;
        }
        
        .sidebar {
            display: none;
        }
    }
</style>
@endsection

@section('content')
<div class="ingresos-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admisionista</h2>

        <ul class="nav-links">
            <li><a href="{{ route('admisionista.index') }}">🏠 Inicio</a></li>
            <li><a href="{{ route('admisionista.ingresos') }}" style="background: rgba(255,255,255,0.12);">📥 Gestión de Ingresos</a></li>
            <li><a href="{{ route('citas.index') }}">📅 Citas</a></li>
            <li><a href="{{ route('admisionista.pacientes') }}">👥 Pacientes</a></li>
            <li><a href="{{ route('admisionista.reportes') }}">📊 Reportes</a></li>
        </ul>

        <button class="logout-btn" onclick="logout()">Cerrar sesión</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Header -->
        <div class="header">
            <h1>Gestión de Ingresos</h1>
            <div class="user-info">
                <span>Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
                <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" alt="usuario">
            </div>
        </div>

        <!-- Stats Grid -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-number" id="ingresosHoy">0</div>
                <div class="stat-label">Ingresos Hoy</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="ingresosSemana">0</div>
                <div class="stat-label">Ingresos Esta Semana</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="pacientesUrgencias">0</div>
                <div class="stat-label">Pacientes en Urgencias</div>
            </div>
            <div class="stat-card">
                <div class="stat-number" id="citasProgramadas">0</div>
                <div class="stat-label">Citas Programadas</div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="quick-actions">
            <div class="action-card" onclick="mostrarFormularioIngreso()">
                <div class="action-icon">👤</div>
                <div class="action-title">Nuevo Ingreso</div>
                <div class="action-description">Registrar nuevo paciente</div>
            </div>
            <div class="action-card" onclick="mostrarFormularioCita()">
                <div class="action-icon">📅</div>
                <div class="action-title">Agendar Cita</div>
                <div class="action-description">Programar nueva cita médica</div>
            </div>
            <div class="action-card" onclick="buscarPacienteRapido()">
                <div class="action-icon">🔍</div>
                <div class="action-title">Buscar Paciente</div>
                <div class="action-description">Búsqueda rápida de pacientes</div>
            </div>
        </div>

        <!-- Forms Section -->
        <div class="forms-section">
            <!-- Formulario de Ingreso -->
            <div class="form-card">
                <h3>📥 Registrar Nuevo Ingreso</h3>
                <form id="formIngreso">
                    @csrf
                    <div class="form-group">
                        <label for="documento_paciente">Documento del Paciente *</label>
                        <input type="text" id="documento_paciente" name="documento_paciente" required 
                               placeholder="Ej: 1001001001">
                    </div>
                    
                    <div class="form-group">
                        <label for="tipo_ingreso">Tipo de Ingreso *</label>
                        <select id="tipo_ingreso" name="tipo_ingreso" required>
                            <option value="">Seleccionar tipo</option>
                            <option value="urgencias">Urgencias</option>
                            <option value="consulta">Consulta Externa</option>
                            <option value="hospitalizacion">Hospitalización</option>
                            <option value="procedimiento">Procedimiento</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="motivo_consulta">Motivo de Consulta *</label>
                        <textarea id="motivo_consulta" name="motivo_consulta" rows="3" 
                                  placeholder="Describa el motivo de la consulta..." required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="prioridad">Prioridad</label>
                        <select id="prioridad" name="prioridad">
                            <option value="normal">Normal</option>
                            <option value="urgente">Urgente</option>
                            <option value="emergencia">Emergencia</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary">📋 Registrar Ingreso</button>
                </form>
            </div>

            <!-- Formulario de Búsqueda Rápida -->
            <div class="form-card">
                <h3>🔍 Búsqueda Rápida</h3>
                <div class="form-group">
                    <label for="busquedaDocumento">Documento o Nombre</label>
                    <input type="text" id="busquedaDocumento" 
                           placeholder="Documento o nombre del paciente">
                </div>
                <button class="btn btn-primary" onclick="buscarPaciente()">Buscar Paciente</button>
                
                <div id="resultadoBusqueda" style="margin-top: 20px; display: none;">
                    <h4>Resultado de Búsqueda</h4>
                    <div id="infoPaciente" style="background: #f8f9fa; padding: 15px; border-radius: 8px;"></div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="activity-card">
            <h3>📋 Ingresos Recientes</h3>
            <div class="activity-list" id="listaIngresosRecientes">
                <div class="activity-item">
                    <div class="activity-icon">⏳</div>
                    <div class="activity-content">
                        <div class="activity-title">Cargando ingresos...</div>
                    </div>
                </div>
            </div>
        </div>
    </main>
</div>

<!-- Modal para información del paciente -->
<div id="modalPaciente" class="modal">
    <div class="modal-content">
        <h3 id="modalTitulo">Información del Paciente</h3>
        <div id="modalContenido"></div>
        <div style="margin-top: 20px; text-align: right;">
            <button class="btn btn-warning" onclick="cerrarModal()">Cerrar</button>
            <button class="btn btn-success" onclick="registrarIngresoDesdeModal()">Registrar Ingreso</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Cargar estadísticas al iniciar
document.addEventListener('DOMContentLoaded', function() {
    cargarEstadisticas();
    cargarIngresosRecientes();
    
    // Configurar formulario de ingreso
    document.getElementById('formIngreso').addEventListener('submit', function(e) {
        e.preventDefault();
        registrarIngreso();
    });
});

function cargarEstadisticas() {
    // Simular carga de estadísticas (en producción sería una API real)
    setTimeout(() => {
        document.getElementById('ingresosHoy').textContent = '12';
        document.getElementById('ingresosSemana').textContent = '89';
        document.getElementById('pacientesUrgencias').textContent = '5';
        document.getElementById('citasProgramadas').textContent = '23';
    }, 1000);
}

function cargarIngresosRecientes() {
    fetch('/api/admisionista/ingresos-recientes')
        .then(r => r.json())
        .then(data => {
            const container = document.getElementById('listaIngresosRecientes');
            
            if (!data.length) {
                container.innerHTML = `
                    <div class="activity-item">
                        <div class="activity-icon">📭</div>
                        <div class="activity-content">
                            <div class="activity-title">No hay ingresos recientes</div>
                        </div>
                    </div>`;
                return;
            }

            let html = '';
            data.forEach(ingreso => {
                const fecha = new Date(ingreso.fecha_ingreso).toLocaleString('es-ES');
                const icon = ingreso.entorno_atencion === 'Urgencias' ? '🚑' : 
                            ingreso.entorno_atencion === 'Hospitalización' ? '🏥' : '👨‍⚕️';
                
                html += `
                <div class="activity-item">
                    <div class="activity-icon">${icon}</div>
                    <div class="activity-content">
                        <div class="activity-title">${ingreso.nombre_completo}</div>
                        <div class="activity-description">${ingreso.entorno_atencion} - ${ingreso.motivo_consulta}</div>
                        <div class="activity-time">${fecha}</div>
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function buscarPaciente() {
    const documento = document.getElementById('busquedaDocumento').value.trim();
    if (!documento) {
        alert('Por favor ingrese un documento o nombre para buscar');
        return;
    }

    fetch(`/api/admisionista/buscar-paciente/${documento}`)
        .then(r => r.json())
        .then(data => {
            const resultado = document.getElementById('resultadoBusqueda');
            const info = document.getElementById('infoPaciente');
            
            if (data.success && data.paciente) {
                const paciente = data.paciente;
                info.innerHTML = `
                    <p><strong>Nombre:</strong> ${paciente.nombre_completo}</p>
                    <p><strong>Documento:</strong> ${paciente.documento_id}</p>
                    <p><strong>Edad:</strong> ${paciente.edad} años</p>
                    <p><strong>Género:</strong> ${paciente.sexo === 'M' ? 'Masculino' : 'Femenino'}</p>
                    <p><strong>Teléfono:</strong> ${paciente.telefono || 'No registrado'}</p>
                    <div style="margin-top: 10px;">
                        <button class="btn btn-success" onclick="seleccionarPaciente('${paciente.documento_id}')">
                            Seleccionar para Ingreso
                        </button>
                    </div>
                `;
                resultado.style.display = 'block';
            } else {
                info.innerHTML = '<p style="color: #dc3545;">Paciente no encontrado</p>';
                resultado.style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al buscar paciente');
        });
}

function seleccionarPaciente(documento) {
    document.getElementById('documento_paciente').value = documento;
    document.getElementById('resultadoBusqueda').style.display = 'none';
    document.getElementById('busquedaDocumento').value = '';
    
    // Enfocar el campo de motivo de consulta
    document.getElementById('motivo_consulta').focus();
}

function registrarIngreso() {
    const formData = new FormData(document.getElementById('formIngreso'));
    
    fetch('/api/admisionista/registrar-ingreso', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Ingreso registrado exitosamente');
            document.getElementById('formIngreso').reset();
            cargarEstadisticas();
            cargarIngresosRecientes();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al registrar el ingreso');
        console.error('Error:', error);
    });
}

function mostrarFormularioIngreso() {
    document.getElementById('formIngreso').scrollIntoView({ 
        behavior: 'smooth' 
    });
}

function mostrarFormularioCita() {
    window.location.href = "{{ route('citas.index') }}";
}

function buscarPacienteRapido() {
    document.getElementById('busquedaDocumento').focus();
}

function mostrarModalPaciente(paciente) {
    document.getElementById('modalTitulo').textContent = `Paciente: ${paciente.nombre_completo}`;
    document.getElementById('modalContenido').innerHTML = `
        <p><strong>Documento:</strong> ${paciente.documento_id}</p>
        <p><strong>Edad:</strong> ${paciente.edad} años</p>
        <p><strong>Género:</strong> ${paciente.sexo}</p>
        <p><strong>Teléfono:</strong> ${paciente.telefono || 'No registrado'}</p>
        <p><strong>Correo:</strong> ${paciente.correo || 'No registrado'}</p>
    `;
    document.getElementById('modalPaciente').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalPaciente').style.display = 'none';
}

function registrarIngresoDesdeModal() {
    cerrarModal();
    mostrarFormularioIngreso();
}

// Cerrar modal al hacer click fuera
document.getElementById('modalPaciente').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
@endsection