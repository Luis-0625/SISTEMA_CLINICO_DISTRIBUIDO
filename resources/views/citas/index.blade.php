@extends('layouts.app')

@section('title', 'Gestión de Citas Médicas')

@section('styles')
<style>
    .citas-container {
        display: flex;
        min-height: 100vh;
    }

    .sidebar {
        width: 260px;
        background: #007bff;
        color: #fff;
        padding: 20px;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
    }

    .sidebar h2 {
        font-size: 22px;
        margin-bottom: 30px;
        font-weight: bold;
        text-align: center;
    }

    .sidebar a {
        display: block;
        padding: 12px;
        margin-bottom: 10px;
        color: #fff;
        text-decoration: none;
        border-radius: 6px;
        font-size: 16px;
    }

    .sidebar a:hover,
    .sidebar a.active {
        background-color: #0056b3;
    }

    .main-content {
        margin-left: 260px;
        padding: 30px;
        width: calc(100% - 260px);
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid #ddd;
    }

    .header h1 {
        font-size: 26px;
        margin: 0;
        color: #333;
    }

    .logout-btn {
        background-color: #dc3545;
        padding: 10px 18px;
        border: none;
        color: #fff;
        border-radius: 6px;
        font-size: 15px;
        cursor: pointer;
    }

    .logout-btn:hover {
        background-color: #c82333;
    }

    .citas-layout {
        display: grid;
        grid-template-columns: 2fr 1fr;
        gap: 30px;
        margin-top: 20px;
    }

    .calendar-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .actions-section {
        display: flex;
        flex-direction: column;
        gap: 20px;
    }

    .form-cita {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .citas-list {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        max-height: 400px;
        overflow-y: auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: #007bff;
        outline: none;
        box-shadow: 0 0 0 2px rgba(0,123,255,0.25);
    }

    .btn-primary {
        background: #007bff;
        color: white;
        border: none;
        padding: 12px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        width: 100%;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .btn-success {
        background: #28a745;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
    }

    .btn-warning {
        background: #ffc107;
        color: #212529;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
        margin-right: 5px;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
        border: none;
        padding: 8px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 12px;
    }

    .cita-item {
        padding: 15px;
        border: 1px solid #eee;
        border-radius: 8px;
        margin-bottom: 10px;
        background: #f8f9fa;
    }

    .cita-item:hover {
        background: #e9ecef;
    }

    .cita-header {
        display: flex;
        justify-content: space-between;
        align-items: start;
        margin-bottom: 10px;
    }

    .cita-paciente {
        font-weight: 600;
        color: #333;
    }

    .cita-fecha {
        color: #666;
        font-size: 0.9em;
    }

    .cita-estado {
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.8em;
        font-weight: 600;
    }

    .estado-programada { background: #d1ecf1; color: #0c5460; }
    .estado-confirmada { background: #d4edda; color: #155724; }
    .estado-completada { background: #e2e3e5; color: #383d41; }
    .estado-cancelada { background: #f8d7da; color: #721c24; }

    .legend {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-top: 15px;
        padding: 15px;
        background: #f8f9fa;
        border-radius: 6px;
    }

    .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
        font-size: 0.8em;
    }

    .legend-color {
        width: 12px;
        height: 12px;
        border-radius: 2px;
    }
</style>

<!-- FullCalendar CSS -->
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />
@endsection

@section('content')
<div class="citas-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>{{ ucfirst(Auth::user()->rol_id) }}</h2>
        
        @if(Auth::user()->rol_id === 'admisionista')
            <a href="{{ route('admisionista.index') }}">🏠 Inicio</a>
            <a href="{{ route('citas.index') }}" style="background: #0056b3;">📅 Citas</a>
            <a href="{{ route('admisionista.pacientes') }}">👥 Pacientes</a>
            <a href="{{ route('admisionista.reportes') }}">📊 Reportes</a>
        @elseif(Auth::user()->rol_id === 'medico')
            <a href="{{ route('medico.index') }}">🏠 Inicio</a>
            <a href="{{ route('citas.index') }}" style="background: #0056b3;">📅 Citas</a>
            <a href="{{ route('medico.consultas') }}">📋 Consultas</a>
            <a href="{{ route('medico.pacientes') }}">👥 Pacientes</a>
        @else
            <a href="{{ route('paciente.home') }}">🏠 Inicio</a>
            <a href="{{ route('citas.index') }}" style="background: #0056b3;">📅 Mis Citas</a>
            <a href="{{ route('paciente.consultas') }}">📋 Mis Consultas</a>
            <a href="{{ route('paciente.resultados') }}">📄 Mis Resultados</a>
        @endif

        <button class="logout-btn" onclick="logout()" style="margin-top: 20px; width: 100%;">Cerrar Sesión</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Gestión de Citas Médicas</h1>
            <span style="color: #666;">Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
        </div>

        <div class="citas-layout">
            <!-- Calendario -->
            <div class="calendar-section">
                <h3 style="margin-bottom: 20px; color: #333;">Calendario de Citas</h3>
                <div id="calendar"></div>
                
                <!-- Leyenda -->
                <div class="legend">
                    <div class="legend-item">
                        <div class="legend-color" style="background: #007bff;"></div>
                        <span>Programada</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #28a745;"></div>
                        <span>Confirmada</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #ffc107;"></div>
                        <span>En Proceso</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #6c757d;"></div>
                        <span>Completada</span>
                    </div>
                    <div class="legend-item">
                        <div class="legend-color" style="background: #dc3545;"></div>
                        <span>Cancelada</span>
                    </div>
                </div>
            </div>

            <!-- Panel de Acciones -->
            <div class="actions-section">
                @if(Auth::user()->rol_id === 'admisionista')
                <!-- Formulario para agendar citas (solo admisionista) -->
                <div class="form-cita">
                    <h4 style="margin-bottom: 20px; color: #333;">Agendar Nueva Cita</h4>
                    <form id="formCita">
                        @csrf
                        <div class="form-group">
                            <label for="documento_paciente">Documento del Paciente</label>
                            <input type="text" id="documento_paciente" name="documento_paciente" required>
                        </div>
                        <div class="form-group">
                            <label for="documento_medico">Médico</label>
                            <select id="documento_medico" name="documento_medico" required>
                                <option value="">Seleccionar médico</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="fecha_cita">Fecha y Hora</label>
                            <input type="datetime-local" id="fecha_cita" name="fecha_cita" required>
                        </div>
                        <div class="form-group">
                            <label for="especialidad">Especialidad</label>
                            <input type="text" id="especialidad" name="especialidad" required>
                        </div>
                        <div class="form-group">
                            <label for="motivo_consulta">Motivo de Consulta</label>
                            <textarea id="motivo_consulta" name="motivo_consulta" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn-primary">📅 Agendar Cita</button>
                    </form>
                </div>
                @endif

                <!-- Lista de Citas Próximas -->
                <div class="citas-list">
                    <h4 style="margin-bottom: 15px; color: #333;">
                        @if(Auth::user()->rol_id === 'paciente')
                            Mis Próximas Citas
                        @else
                            Citas Próximas
                        @endif
                    </h4>
                    <div id="listaCitasProximas">
                        <p style="text-align: center; color: #666; padding: 20px;">
                            Cargando citas...
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles de cita -->
<div id="modalCita" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 500px;">
        <h3 id="modalTitulo" style="margin-bottom: 20px;">Detalles de Cita</h3>
        <div id="modalContenido"></div>
        <div style="margin-top: 25px; text-align: right;">
            <button onclick="cerrarModal()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<!-- FullCalendar JS -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales/es.js'></script>

<script>
let calendar;
let medicos = [];

document.addEventListener('DOMContentLoaded', function() {
    // Inicializar calendario
    const calendarEl = document.getElementById('calendar');
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: '/api/citas',
        eventClick: function(info) {
            mostrarDetallesCita(info.event.id);
        },
        dateClick: function(info) {
            if ('{{ Auth::user()->rol_id }}' === 'admisionista') {
                document.getElementById('fecha_cita').value = info.dateStr + 'T10:00';
            }
        }
    });
    calendar.render();

    // Cargar datos iniciales
    cargarMedicos();
    cargarCitasProximas();

    // Configurar formulario de cita
    @if(Auth::user()->rol_id === 'admisionista')
    document.getElementById('formCita').addEventListener('submit', function(e) {
        e.preventDefault();
        agendarCita();
    });

    // Auto-completar especialidad cuando se selecciona médico
    document.getElementById('documento_medico').addEventListener('change', function() {
        const medico = medicos.find(m => m.documento_id == this.value);
        if (medico) {
            document.getElementById('especialidad').value = medico.especialidad;
        }
    });
    @endif
});

function cargarMedicos() {
    @if(Auth::user()->rol_id === 'admisionista')
    fetch('/api/citas/medicos')
        .then(r => r.json())
        .then(data => {
            medicos = data;
            const select = document.getElementById('documento_medico');
            select.innerHTML = '<option value="">Seleccionar médico</option>';
            data.forEach(medico => {
                const option = document.createElement('option');
                option.value = medico.documento_id;
                option.textContent = `${medico.nombre} - ${medico.especialidad}`;
                select.appendChild(option);
            });
        });
    @endif
}

function cargarCitasProximas() {
    const container = document.getElementById('listaCitasProximas');
    
    fetch('/api/citas/proximas')
        .then(r => r.json())
        .then(data => {
            if (!data.length) {
                container.innerHTML = '<p style="text-align: center; color: #666; padding: 20px;">No hay citas próximas</p>';
                return;
            }

            let html = '';
            data.forEach(cita => {
                const fecha = new Date(cita.fecha_cita).toLocaleString('es-ES');
                html += `
                <div class="cita-item">
                    <div class="cita-header">
                        <div>
                            <div class="cita-paciente">
                                @if(Auth::user()->rol_id === 'paciente')
                                    ${cita.medico_nombre}
                                @else
                                    ${cita.paciente_nombre}
                                @endif
                            </div>
                            <div class="cita-fecha">${fecha}</div>
                        </div>
                        <span class="cita-estado estado-${cita.estado}">${cita.estado}</span>
                    </div>
                    <div style="margin-bottom: 10px;">
                        <strong>Motivo:</strong> ${cita.motivo_consulta}<br>
                        <strong>Especialidad:</strong> ${cita.especialidad}
                    </div>
                    <div style="display: flex; gap: 5px;">
                        <button onclick="mostrarDetallesCita('${cita.cita_id}')" class="btn-success">👁️ Ver</button>
                        @if(Auth::user()->rol_id !== 'paciente')
                        <button onclick="cambiarEstado('${cita.cita_id}', 'confirmada')" class="btn-warning">✅ Confirmar</button>
                        <button onclick="cambiarEstado('${cita.cita_id}', 'cancelada')" class="btn-danger">❌ Cancelar</button>
                        @endif
                    </div>
                </div>`;
            });
            container.innerHTML = html;
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = '<p style="text-align: center; color: #dc3545; padding: 20px;">Error al cargar citas</p>';
        });
}

function agendarCita() {
    const formData = new FormData(document.getElementById('formCita'));
    
    fetch('/api/citas', {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Cita agendada exitosamente');
            document.getElementById('formCita').reset();
            calendar.refetchEvents();
            cargarCitasProximas();
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        alert('Error al agendar la cita');
        console.error('Error:', error);
    });
}

function mostrarDetallesCita(citaId) {
    fetch(`/api/citas/${citaId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                const cita = data.cita;
                const fecha = new Date(cita.fecha_cita).toLocaleString('es-ES');
                
                document.getElementById('modalTitulo').textContent = 'Detalles de Cita';
                document.getElementById('modalContenido').innerHTML = `
                    <div style="line-height: 1.6;">
                        <p><strong>Paciente:</strong> ${cita.paciente_nombre}</p>
                        <p><strong>Médico:</strong> ${cita.medico_nombre}</p>
                        <p><strong>Especialidad:</strong> ${cita.especialidad}</p>
                        <p><strong>Fecha y Hora:</strong> ${fecha}</p>
                        <p><strong>Motivo:</strong> ${cita.motivo_consulta}</p>
                        <p><strong>Estado:</strong> <span class="cita-estado estado-${cita.estado}">${cita.estado}</span></p>
                        <p><strong>Creado por:</strong> ${cita.creador_nombre || 'Sistema'}</p>
                        ${cita.observaciones ? `<p><strong>Observaciones:</strong> ${cita.observaciones}</p>` : ''}
                    </div>
                    <div style="margin-top: 20px; display: flex; gap: 10px;">
                        <button onclick="cambiarEstado('${citaId}', 'confirmada')" class="btn-success">✅ Confirmar</button>
                        <button onclick="cambiarEstado('${citaId}', 'completada')" class="btn-warning">📝 Completar</button>
                        <button onclick="cambiarEstado('${citaId}', 'cancelada')" class="btn-danger">❌ Cancelar</button>
                    </div>
                `;
                document.getElementById('modalCita').style.display = 'block';
            } else {
                alert('Error: ' + data.message);
            }
        });
}

function cambiarEstado(citaId, nuevoEstado) {
    if (!confirm(`¿Está seguro de que desea cambiar el estado a "${nuevoEstado}"?`)) {
        return;
    }

    fetch(`/api/citas/${citaId}/estado`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ estado: nuevoEstado })
    })
    .then(r => r.json())
    .then(data => {
        if (data.success) {
            alert('Estado actualizado correctamente');
            cerrarModal();
            calendar.refetchEvents();
            cargarCitasProximas();
        } else {
            alert('Error: ' + data.message);
        }
    });
}

function cerrarModal() {
    document.getElementById('modalCita').style.display = 'none';
}

// Cerrar modal al hacer click fuera
document.getElementById('modalCita').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});
</script>
@endsection