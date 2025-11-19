@extends('layouts.app')

@section('title', 'Mis Consultas Médicas')

@section('content')
<style>
    .consultas-container {
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

    .search-section {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        margin-bottom: 25px;
    }

    .search-filters {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 15px;
        align-items: end;
    }

    .filter-group {
        margin-bottom: 0;
    }

    .filter-group label {
        display: block;
        margin-bottom: 5px;
        font-weight: 600;
        color: #333;
    }

    .filter-group input,
    .filter-group select {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
    }

    .btn-primary {
        background: #007bff;
        color: white;
        border: none;
        padding: 10px 20px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 14px;
        font-weight: 600;
        height: 40px;
    }

    .btn-primary:hover {
        background: #0056b3;
    }

    .consultas-list {
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
    }

    .consulta-card {
        border: 1px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        background: #f8f9fa;
        transition: all 0.3s ease;
    }

    .consulta-card:hover {
        background: #e9ecef;
        border-color: #007bff;
    }

    .consulta-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 15px;
    }

    .consulta-fecha {
        font-size: 1.1em;
        font-weight: 600;
        color: #333;
    }

    .consulta-estado {
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 0.8em;
        font-weight: 600;
    }

    .estado-completada {
        background: #d4edda;
        color: #155724;
    }

    .estado-procesando {
        background: #fff3cd;
        color: #856404;
    }

    .estado-pendiente {
        background: #d1ecf1;
        color: #0c5460;
    }

    .consulta-info {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 15px;
        margin-bottom: 15px;
    }

    .info-item {
        display: flex;
        flex-direction: column;
    }

    .info-label {
        font-size: 0.8em;
        color: #666;
        margin-bottom: 2px;
    }

    .info-value {
        font-weight: 600;
        color: #333;
    }

    .consulta-details {
        background: white;
        padding: 15px;
        border-radius: 6px;
        margin-top: 10px;
        border-left: 4px solid #007bff;
    }

    .detail-section {
        margin-bottom: 15px;
    }

    .detail-section:last-child {
        margin-bottom: 0;
    }

    .detail-section h4 {
        margin: 0 0 8px 0;
        color: #007bff;
        font-size: 0.9em;
    }

    .detail-content {
        font-size: 0.9em;
        line-height: 1.4;
        color: #555;
    }

    .btn-detalle {
        background: #6c757d;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 0.8em;
        margin-top: 10px;
    }

    .btn-detalle:hover {
        background: #545b62;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #666;
    }

    .empty-state i {
        font-size: 3em;
        margin-bottom: 15px;
        opacity: 0.5;
    }

    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        margin-top: 25px;
        gap: 15px;
    }

    .pagination button {
        background: #007bff;
        color: white;
        border: none;
        padding: 8px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-size: 0.9em;
    }

    .pagination button:disabled {
        background: #6c757d;
        cursor: not-allowed;
    }

    .pagination-info {
        color: #666;
        font-size: 0.9em;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #007bff;
        border-radius: 50%;
        width: 30px;
        height: 30px;
        animation: spin 1s linear infinite;
        margin: 0 auto 15px;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .search-filters {
            grid-template-columns: 1fr;
        }
        
        .consulta-info {
            grid-template-columns: 1fr;
        }
        
        .consulta-header {
            flex-direction: column;
            gap: 10px;
        }
    }
</style>

<div class="consultas-container">
    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Paciente</h2>
        
        <a href="{{ route('paciente.home') }}">🏠 Inicio</a>
        <a href="{{ route('paciente.consultas') }}" style="background: #0056b3;">📋 Mis Consultas</a>
        <a href="{{ route('paciente.resultados') }}">📄 Mis Resultados</a>
       

        <button class="logout-btn" onclick="logout()" style="margin-top: 20px; width: 100%;">Cerrar Sesión</button>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="header">
            <h1>Mis Consultas Médicas</h1>
            <span style="color: #666;">Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
        </div>

        <!-- Filtros de Búsqueda -->
        <div class="search-section">
            <h3 style="margin-bottom: 20px; color: #333;">Buscar Consultas</h3>
            <div class="search-filters">
                <div class="filter-group">
                    <label for="filtroFechaDesde">Fecha Desde</label>
                    <input type="date" id="filtroFechaDesde">
                </div>
                <div class="filter-group">
                    <label for="filtroFechaHasta">Fecha Hasta</label>
                    <input type="date" id="filtroFechaHasta">
                </div>
                <div class="filter-group">
                    <label for="filtroEspecialidad">Especialidad</label>
                    <select id="filtroEspecialidad">
                        <option value="">Todas las especialidades</option>
                        <option value="Medicina Interna">Medicina Interna</option>
                        <option value="Urgencias">Urgencias</option>
                        <option value="Cardiología">Cardiología</option>
                        <option value="Pediatría">Pediatría</option>
                        <option value="Dermatología">Dermatología</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label for="filtroEstado">Estado</label>
                    <select id="filtroEstado">
                        <option value="">Todos los estados</option>
                        <option value="completada">Completada</option>
                        <option value="procesando">En Proceso</option>
                        <option value="pendiente">Pendiente</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button class="btn-primary" onclick="cargarConsultas()">🔍 Buscar Consultas</button>
                </div>
            </div>
        </div>

        <!-- Lista de Consultas -->
        <div class="consultas-list">
            <h3 style="margin-bottom: 20px; color: #333;">Historial de Consultas</h3>
            <div id="listaConsultas">
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Cargando consultas...</p>
                </div>
            </div>

            <!-- Paginación -->
            <div class="pagination">
                <button id="btnPrev" onclick="cambiarPagina(-1)" disabled>← Anterior</button>
                <span id="paginationInfo" class="pagination-info">Página 1 de 1</span>
                <button id="btnNext" onclick="cambiarPagina(1)">Siguiente →</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal para detalles completos -->
<div id="modalDetalle" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000;">
    <div style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); background: white; padding: 30px; border-radius: 12px; width: 90%; max-width: 700px; max-height: 80vh; overflow-y: auto;">
        <h3 id="modalTitulo" style="margin-bottom: 20px; color: #333;">Detalles de Consulta</h3>
        <div id="modalContenido"></div>
        <div style="margin-top: 25px; text-align: right;">
            <button onclick="cerrarModal()" style="background: #6c757d; color: white; border: none; padding: 10px 20px; border-radius: 6px; cursor: pointer;">Cerrar</button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
let paginaActual = 1;
const consultasPorPagina = 5;
let consultasTotales = 0;

document.addEventListener('DOMContentLoaded', function() {
    // Establecer fechas por defecto (últimos 6 meses)
    const hoy = new Date();
    const haceSeisMeses = new Date();
    haceSeisMeses.setMonth(hoy.getMonth() - 6);
    
    document.getElementById('filtroFechaDesde').value = haceSeisMeses.toISOString().split('T')[0];
    document.getElementById('filtroFechaHasta').value = hoy.toISOString().split('T')[0];
    
    cargarConsultas();
});

function cargarConsultas(pagina = 1) {
    const container = document.getElementById('listaConsultas');
    container.innerHTML = `
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Cargando consultas...</p>
        </div>
    `;

    const fechaDesde = document.getElementById('filtroFechaDesde').value;
    const fechaHasta = document.getElementById('filtroFechaHasta').value;
    const especialidad = document.getElementById('filtroEspecialidad').value;
    const estado = document.getElementById('filtroEstado').value;

    const params = new URLSearchParams({
        page: pagina,
        limit: consultasPorPagina,
        fecha_desde: fechaDesde,
        fecha_hasta: fechaHasta,
        especialidad: especialidad,
        estado: estado
    });

    fetch(`/api/paciente/consultas/{{ Auth::user()->documento_id }}?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            renderConsultas(data.consultas);
            actualizarPaginacion(data);
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="empty-state">
                    <div>⚠️</div>
                    <h3>Error al cargar consultas</h3>
                    <p>No se pudieron cargar las consultas. Por favor, intente nuevamente.</p>
                    <button class="btn-primary" onclick="cargarConsultas()" style="margin-top: 15px;">Reintentar</button>
                </div>
            `;
        });
}

function renderConsultas(consultas) {
    const container = document.getElementById('listaConsultas');
    
    if (!consultas || consultas.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <div>📋</div>
                <h3>No se encontraron consultas</h3>
                <p>No hay consultas médicas que coincidan con los filtros seleccionados.</p>
                <button class="btn-primary" onclick="resetearFiltros()" style="margin-top: 15px;">Mostrar Todas</button>
            </div>
        `;
        return;
    }

    let html = '';
    consultas.forEach(consulta => {
        const fecha = new Date(consulta.fecha_registro).toLocaleDateString('es-ES', {
            year: 'numeric',
            month: 'long',
            day: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });

        const estadoClass = `estado-${consulta.estado_paciente || 'pendiente'}`;
        const estadoText = consulta.estado_paciente ? 
            consulta.estado_paciente.charAt(0).toUpperCase() + consulta.estado_paciente.slice(1) : 
            'Pendiente';

        html += `
        <div class="consulta-card">
            <div class="consulta-header">
                <div class="consulta-fecha">${fecha}</div>
                <span class="consulta-estado ${estadoClass}">${estadoText}</span>
            </div>
            
            <div class="consulta-info">
                <div class="info-item">
                    <span class="info-label">Especialidad</span>
                    <span class="info-value">${consulta.especialidad || 'Consulta General'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Médico</span>
                    <span class="info-value">${consulta.medico || 'No asignado'}</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Entidad de Salud</span>
                    <span class="info-value">${consulta.entidad_salud || 'No especificada'}</span>
                </div>
            </div>

            <div class="consulta-details">
                <div class="detail-section">
                    <h4>Motivo de Consulta</h4>
                    <div class="detail-content">${consulta.motivo_consulta || 'No registrado'}</div>
                </div>
                
                ${consulta.diagnostico_principal ? `
                <div class="detail-section">
                    <h4>Diagnóstico Principal</h4>
                    <div class="detail-content">${consulta.diagnostico_principal}</div>
                </div>
                ` : ''}
                
                ${consulta.plan_tratamiento ? `
                <div class="detail-section">
                    <h4>Plan de Tratamiento</h4>
                    <div class="detail-content">${consulta.plan_tratamiento}</div>
                </div>
                ` : ''}
            </div>

            <button class="btn-detalle" onclick="verDetalleCompleto('${consulta.hc_id}')">
                📖 Ver Detalles Completos
            </button>
        </div>`;
    });

    container.innerHTML = html;
}

function actualizarPaginacion(data) {
    paginaActual = data.current_page || 1;
    consultasTotales = data.total || 0;
    const totalPaginas = data.total_pages || 1;

    document.getElementById('btnPrev').disabled = paginaActual <= 1;
    document.getElementById('btnNext').disabled = paginaActual >= totalPaginas;
    
    document.getElementById('paginationInfo').textContent = 
        `Página ${paginaActual} de ${totalPaginas} - ${consultasTotales} consultas en total`;
}

function cambiarPagina(direccion) {
    cargarConsultas(paginaActual + direccion);
}

function resetearFiltros() {
    const hoy = new Date();
    const haceSeisMeses = new Date();
    haceSeisMeses.setMonth(hoy.getMonth() - 6);
    
    document.getElementById('filtroFechaDesde').value = haceSeisMeses.toISOString().split('T')[0];
    document.getElementById('filtroFechaHasta').value = hoy.toISOString().split('T')[0];
    document.getElementById('filtroEspecialidad').value = '';
    document.getElementById('filtroEstado').value = '';
    
    cargarConsultas(1);
}

function verDetalleCompleto(hcId) {
    fetch(`/api/paciente/consulta/${hcId}`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                mostrarModalDetalle(data.consulta);
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            alert('Error al cargar los detalles de la consulta');
            console.error('Error:', error);
        });
}

function mostrarModalDetalle(consulta) {
    const fecha = new Date(consulta.fecha_registro).toLocaleDateString('es-ES', {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });

    document.getElementById('modalTitulo').textContent = `Consulta - ${fecha}`;
    
    let contenido = `
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
            <div>
                <h4 style="color: #007bff; margin-bottom: 8px;">Información General</h4>
                <p><strong>Médico:</strong> ${consulta.medico || 'No asignado'}</p>
                <p><strong>Especialidad:</strong> ${consulta.especialidad || 'Consulta General'}</p>
                <p><strong>Entidad de Salud:</strong> ${consulta.entidad_salud || 'No especificada'}</p>
                <p><strong>Estado:</strong> <span class="consulta-estado estado-${consulta.estado_paciente || 'pendiente'}">${consulta.estado_paciente || 'Pendiente'}</span></p>
            </div>
            <div>
                <h4 style="color: #007bff; margin-bottom: 8px;">Signos Vitales</h4>
                ${consulta.signos_vitales_peso ? `<p><strong>Peso:</strong> ${consulta.signos_vitales_peso} kg</p>` : ''}
                ${consulta.signos_vitales_talla ? `<p><strong>Talla:</strong> ${consulta.signos_vitales_talla} cm</p>` : ''}
                ${consulta.signos_vitales_imc ? `<p><strong>IMC:</strong> ${consulta.signos_vitales_imc}</p>` : ''}
                ${consulta.signos_vitales_fc ? `<p><strong>Frecuencia Cardíaca:</strong> ${consulta.signos_vitales_fc} lpm</p>` : ''}
                ${consulta.signos_vitales_ta ? `<p><strong>Tensión Arterial:</strong> ${consulta.signos_vitales_ta}</p>` : ''}
            </div>
        </div>

        <div style="margin-bottom: 20px;">
            <h4 style="color: #007bff; margin-bottom: 8px;">Motivo de Consulta</h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                ${consulta.motivo_consulta || 'No registrado'}
            </div>
        </div>

        ${consulta.sintomas_principales ? `
        <div style="margin-bottom: 20px;">
            <h4 style="color: #007bff; margin-bottom: 8px;">Síntomas Principales</h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                ${consulta.sintomas_principales}
            </div>
        </div>
        ` : ''}

        ${consulta.diagnostico_principal ? `
        <div style="margin-bottom: 20px;">
            <h4 style="color: #007bff; margin-bottom: 8px;">Diagnóstico Principal</h4>
            <div style="background: #e8f5e8; padding: 15px; border-radius: 6px;">
                ${consulta.diagnostico_principal}
            </div>
        </div>
        ` : ''}

        ${consulta.plan_tratamiento ? `
        <div style="margin-bottom: 20px;">
            <h4 style="color: #007bff; margin-bottom: 8px;">Plan de Tratamiento</h4>
            <div style="background: #fff3cd; padding: 15px; border-radius: 6px;">
                ${consulta.plan_tratamiento}
            </div>
        </div>
        ` : ''}

        ${consulta.observaciones ? `
        <div style="margin-bottom: 20px;">
            <h4 style="color: #007bff; margin-bottom: 8px;">Observaciones</h4>
            <div style="background: #f8f9fa; padding: 15px; border-radius: 6px;">
                ${consulta.observaciones}
            </div>
        </div>
        ` : ''}
    `;

    document.getElementById('modalContenido').innerHTML = contenido;
    document.getElementById('modalDetalle').style.display = 'block';
}

function cerrarModal() {
    document.getElementById('modalDetalle').style.display = 'none';
}

// Cerrar modal al hacer click fuera
document.getElementById('modalDetalle').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

// Cerrar modal con ESC
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>
@endsection