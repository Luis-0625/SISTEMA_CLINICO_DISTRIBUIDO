@extends('layouts.app')

@section('title', 'Gestión de Pacientes - Admisionista')

@section('styles')
<style>
    .dashboard-container {
        display: flex;
        min-height: 100vh;
        width: 100%;
    }

    /* Sidebar */
    .sidebar {
        width: 260px;
        background: linear-gradient(180deg, #0d6e6b, #065f5b);
        color: #fff;
        padding: 30px 0;
        box-shadow: 3px 0 10px rgba(0, 0, 0, 0.1);
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
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

    /* Main Content */
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

    .pacientes-container { 
        background: white; 
        padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 3px 15px rgba(0,0,0,0.08); 
    }

    .search-section { 
        display: flex; 
        gap: 15px; 
        margin-bottom: 25px; 
        align-items: end; 
        flex-wrap: wrap;
    }

    .search-section input, .search-section select { 
        padding: 10px; 
        border: 1px solid #ccc; 
        border-radius: 6px; 
        font-size: 14px;
    }

    .btn { 
        padding: 10px 16px; 
        border: none; 
        border-radius: 6px; 
        cursor: pointer; 
        color: white; 
        font-weight: 600; 
        font-size: 14px;
    }

    .btn-primary { background: #0d9488; }
    .btn-primary:hover { background: #0b8078; }

    .btn-success { background: #28a745; }
    .btn-success:hover { background: #218838; }

    .btn-info { background: #17a2b8; }
    .btn-info:hover { background: #138496; }

    .btn-warning { background: #ffc107; color: #212529; }
    .btn-warning:hover { background: #e0a800; }

    .btn-danger { background: #dc3545; }
    .btn-danger:hover { background: #c82333; }

    .pacientes-table { 
        width: 100%; 
        border-collapse: collapse; 
    }

    .pacientes-table th, .pacientes-table td { 
        padding: 12px; 
        text-align: left; 
        border-bottom: 1px solid #eee; 
    }

    .pacientes-table th { 
        background: #0d9488; 
        color: white; 
        font-weight: 600;
    }

    .pacientes-table tr:hover { 
        background: #f6f9fa; 
    }

    .badge { 
        padding: 4px 8px; 
        border-radius: 12px; 
        font-size: 0.8em; 
        font-weight: 600;
    }

    .badge-success { background: #d4edda; color: #155724; }
    .badge-warning { background: #fff3cd; color: #856404; }
    .badge-danger { background: #f8d7da; color: #721c24; }

    .pagination-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #eee;
    }

    .loading {
        text-align: center;
        padding: 40px;
        color: #666;
    }

    .loading-spinner {
        border: 3px solid #f3f3f3;
        border-top: 3px solid #0d9488;
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
        .main-content {
            margin-left: 0;
            width: 100%;
            padding: 20px;
        }
        
        .sidebar {
            display: none;
        }
        
        .search-section {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-section input, 
        .search-section select {
            width: 100%;
        }
    }
</style>
@endsection

@section('content')
<div class="dashboard-container">
    <!-- Sidebar -->
    <aside class="sidebar">
        <h2>Admisionista</h2>
        <ul class="nav-links">
            <li><a href="{{ route('admisionista.index') }}">🏠 Inicio</a></li>
            <li><a href="{{ route('admisionista.ingresos') }}">📥 Ingresos</a></li>
            <li><a href="{{ route('citas.index') }}">📅 Citas</a></li>
            <li><a href="{{ route('admisionista.pacientes') }}" style="background: rgba(255,255,255,0.1);">👥 Pacientes</a></li>
            <li><a href="{{ route('admisionista.reportes') }}">📊 Reportes</a></li>
        </ul>
        <button type="button" onclick="logout()" class="logout-btn">Cerrar sesión</button>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
        <div class="header">
            <h1>Gestión de Pacientes</h1>
            <div class="user-info">
                <span>Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
            </div>
        </div>

        <div class="pacientes-container">
            <!-- Barra de Búsqueda y Filtros -->
            <div class="search-section">
                <div style="flex: 1;">
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Buscar Paciente</label>
                    <input type="text" id="searchInput" placeholder="Documento, nombre, email..." style="width: 100%;">
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Estado</label>
                    <select id="filterEstado" style="width: 150px;">
                        <option value="">Todos</option>
                        <option value="activo">Activo</option>
                        <option value="inactivo">Inactivo</option>
                    </select>
                </div>
                <div>
                    <label style="display: block; margin-bottom: 5px; font-weight: 600;">Género</label>
                    <select id="filterGenero" style="width: 120px;">
                        <option value="">Todos</option>
                        <option value="M">Masculino</option>
                        <option value="F">Femenino</option>
                    </select>
                </div>
                <div style="display: flex; gap: 10px; align-items: end;">
                    <button onclick="buscarPacientes(1)" class="btn btn-primary">🔍 Buscar</button>
                    <button onclick="nuevoPaciente()" class="btn btn-success">➕ Nuevo</button>
                </div>
            </div>

            <!-- Tabla de Pacientes -->
            <div style="overflow-x: auto;">
                <table class="pacientes-table">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre Completo</th>
                            <th>Edad</th>
                            <th>Género</th>
                            <th>Teléfono</th>
                            <th>Última Consulta</th>
                            <th>Estado</th>
                            <th style="width: 180px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody id="pacientesBody">
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                                <div class="loading">
                                    <div class="loading-spinner"></div>
                                    <p>Cargando pacientes...</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Paginación -->
            <div class="pagination-container">
                <div id="paginationInfo" style="color: #666; font-size: 0.9em;"></div>
                <div style="display: flex; gap: 10px;">
                    <button onclick="cambiarPagina(-1)" id="btnPrev" disabled class="btn btn-info">← Anterior</button>
                    <button onclick="cambiarPagina(1)" id="btnNext" class="btn btn-info">Siguiente →</button>
                </div>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
let paginaActual = 1;
const porPagina = 10;

// Cargar pacientes al iniciar
document.addEventListener('DOMContentLoaded', function() {
    buscarPacientes(1);
});

function buscarPacientes(pagina = 1) {
    const searchTerm = document.getElementById('searchInput').value;
    const estado = document.getElementById('filterEstado').value;
    const genero = document.getElementById('filterGenero').value;

    // Mostrar loading
    const tbody = document.getElementById('pacientesBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Buscando pacientes...</p>
                </div>
            </td>
        </tr>`;

    const params = new URLSearchParams({
        page: pagina,
        limit: porPagina,
        search: searchTerm || '',
        estado: estado || '',
        genero: genero || ''
    });

    fetch(`/api/admisionista/pacientes?${params}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            renderPacientes(data.pacientes || data.data || []);
            actualizarPaginacion(data);
        })
        .catch(error => {
            console.error('Error:', error);
            tbody.innerHTML = `
                <tr>
                    <td colspan="8" style="text-align: center; padding: 40px; color: #dc3545;">
                        <strong>Error al cargar pacientes</strong><br>
                        <small>Verifique su conexión e intente nuevamente</small>
                    </td>
                </tr>`;
        });
}

function renderPacientes(pacientes) {
    const tbody = document.getElementById('pacientesBody');
    
    if (!pacientes || pacientes.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8" style="text-align: center; padding: 40px; color: #666;">
                    <div>👥</div>
                    <strong>No se encontraron pacientes</strong><br>
                    <small>Intente con otros términos de búsqueda</small>
                </td>
            </tr>`;
        return;
    }

    let html = '';
    pacientes.forEach(paciente => {
        const genero = paciente.sexo === 'M' ? 'Masculino' : 
                      paciente.sexo === 'F' ? 'Femenino' : 
                      paciente.sexo || 'No especificado';
        
        const ultimaConsulta = paciente.ultima_consulta ? 
            new Date(paciente.ultima_consulta).toLocaleDateString('es-ES') : 
            'Nunca';

        const estado = paciente.activo !== undefined ? paciente.activo : true;
        
        html += `
        <tr>
            <td><strong>${paciente.documento_id}</strong></td>
            <td>
                <div style="font-weight: 600;">${paciente.nombre_completo}</div>
                <small style="color: #666;">${paciente.correo || 'Sin email'}</small>
            </td>
            <td>${paciente.edad || 'N/A'} años</td>
            <td>${genero}</td>
            <td>${paciente.telefono || 'N/A'}</td>
            <td>${ultimaConsulta}</td>
            <td>
                <span class="badge ${estado ? 'badge-success' : 'badge-warning'}">
                    ${estado ? 'Activo' : 'Inactivo'}
                </span>
            </td>
            <td>
                <div style="display: flex; gap: 5px; flex-wrap: wrap;">
                    <button onclick="verPaciente('${paciente.documento_id}')" class="btn btn-info" style="padding: 6px 10px; font-size: 0.8em;">👁️ Ver</button>
                    <button onclick="editarPaciente('${paciente.documento_id}')" class="btn btn-warning" style="padding: 6px 10px; font-size: 0.8em;">✏️ Editar</button>
                    <button onclick="historialPaciente('${paciente.documento_id}')" class="btn btn-primary" style="padding: 6px 10px; font-size: 0.8em;">📋 Historial</button>
                </div>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

function actualizarPaginacion(data) {
    paginaActual = data.current_page || 1;
    const total = data.total || 0;
    const totalPaginas = data.total_pages || Math.ceil(total / porPagina);
    
    document.getElementById('btnPrev').disabled = paginaActual <= 1;
    document.getElementById('btnNext').disabled = paginaActual >= totalPaginas;
    
    document.getElementById('paginationInfo').textContent = 
        `Página ${paginaActual} de ${totalPaginas} • Total: ${total} pacientes`;
}

function cambiarPagina(direccion) {
    buscarPacientes(paginaActual + direccion);
}

function nuevoPaciente() {
    // Implementar modal de nuevo paciente
    alert('Funcionalidad: Nuevo Paciente\n\nAquí se abrirá un formulario para registrar un nuevo paciente en el sistema.');
}

function verPaciente(documentoId) {
    // Redirigir a vista de detalle del paciente
    alert(`Ver paciente: ${documentoId}\n\nRedirigiendo a detalles del paciente...`);
    // window.location.href = `/admisionista/pacientes/${documentoId}`;
}

function editarPaciente(documentoId) {
    // Abrir modal de edición
    alert(`Editar paciente: ${documentoId}\n\nAquí se abrirá un formulario para editar los datos del paciente.`);
}

function historialPaciente(documentoId) {
    // Redirigir a historial médico
    alert(`Historial médico: ${documentoId}\n\nRedirigiendo al historial clínico del paciente...`);
    // window.location.href = `/admisionista/pacientes/${documentoId}/historial`;
}

// Búsqueda en tiempo real
let searchTimeout;
document.getElementById('searchInput').addEventListener('input', function() {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => buscarPacientes(1), 800);
});

// Buscar al presionar Enter
document.getElementById('searchInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        buscarPacientes(1);
    }
});

// Función de logout global (debe estar en layout)
function logout() {
    if (confirm('¿Está seguro de que desea cerrar sesión?')) {
        // Crear formulario dinámico para logout
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("logout") }}';
        
        const csrfToken = document.createElement('input');
        csrfToken.type = 'hidden';
        csrfToken.name = '_token';
        csrfToken.value = '{{ csrf_token() }}';
        
        form.appendChild(csrfToken);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection