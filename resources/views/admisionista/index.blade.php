@extends('layouts.app')

@section('title', 'Panel del Admisionista - Mundo Pie')

@section('styles')
    <style>
        html, body {
            height: 100%;
            font-family: 'Poppins', sans-serif;
            background-color: #f3f6f8;
            margin: 0;
            padding: 0;
        }

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
            grid-template-columns: repeat(4, 1fr);
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

        /* Search */
        .search-bar {
            display: flex;
            justify-content: flex-end;
            margin-bottom: 25px;
            gap: 10px;
        }

        .search-bar input {
            width: 320px;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            outline: none;
        }

        .search-bar input:focus {
            border-color: #0d9488;
            box-shadow: 0 0 4px rgba(13, 148, 136, 0.4);
        }

        .search-bar button {
            background-color: #0d9488;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 12px 22px;
            cursor: pointer;
            font-weight: 600;
            font-size: 0.95em;
            transition: 0.3s;
        }

        .search-bar button:hover {
            background-color: #0b8078;
        }

        /* Table */
        table {
            width: 100%;
            background: #fff;
            border-radius: 12px;
            border-collapse: collapse;
            overflow: hidden;
            box-shadow: 0 3px 15px rgba(0, 0, 0, 0.08);
        }

        thead {
            background-color: #0d9488;
            color: #fff;
        }

        th, td {
            padding: 16px;
            border-bottom: 1px solid #eee;
            font-size: 0.95em;
        }

        tr:hover {
            background-color: #f6f9fa;
        }

        .btn {
            padding: 7px 14px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            color: #fff;
            font-size: 0.85em;
            font-weight: 600;
        }

        .btn-view {
            background-color: #3b82f6;
        }

        .btn-edit {
            background-color: #22c55e;
        }

        .btn-delete {
            background-color: #ef4444;
        }

        .btn-view:hover {
            background-color: #1d4ed8;
        }

        .btn-edit:hover {
            background-color: #16a34a;
        }

        .btn-delete:hover {
            background-color: #b91c1c;
        }

        /* Recent Activity */
        .activity-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 15px rgba(0,0,0,0.08);
            margin-top: 30px;
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

        .status-badge {
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 0.8em;
            font-weight: 600;
        }

        .status-urgente { background: #f8d7da; color: #721c24; }
        .status-normal { background: #d1ecf1; color: #0c5460; }
        .status-completado { background: #d4edda; color: #155724; }

        footer {
            margin-top: 45px;
            text-align: center;
            color: #666;
            padding-top: 25px;
        }
    </style>
@endsection

@section('content')
    <div class="dashboard-container">

        {{-- SIDEBAR --}}
        <aside class="sidebar">
            <h2>Admisionista</h2>

            <ul class="nav-links">
                <li><a href="{{ route('admisionista.index') }}" style="background: rgba(255,255,255,0.12);">🏠 Inicio</a></li>
                <li><a href="{{ route('admisionista.ingresos') }}">📥 Gestión de Ingresos</a></li>
                <li><a href="{{ route('admisionista.pacientes') }}">👥 Pacientes</a></li>
                <li><a href="{{ route('citas.index') }}">📅 Citas</a></li>
                <li><a href="{{ route('admisionista.reportes') }}">📊 Reportes</a></li>
            </ul>

            <button class="logout-btn" onclick="logout()">Cerrar sesión</button>
        </aside>

        {{-- MAIN CONTENT --}}
        <main class="main-content">

            {{-- Header --}}
            <div class="header">
                <h1>Panel del Admisionista</h1>
                <div class="user-info">
                    <span>Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
                    <img src="https://cdn-icons-png.flaticon.com/512/2922/2922510.png" alt="usuario">
                </div>
            </div>

            {{-- Stats Grid --}}
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number" id="totalIngresos">0</div>
                    <div class="stat-label">Total Ingresos Hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="pacientesActivos">0</div>
                    <div class="stat-label">Pacientes Activos</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="citasHoy">0</div>
                    <div class="stat-label">Citas para Hoy</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number" id="urgencias">0</div>
                    <div class="stat-label">Casos Urgentes</div>
                </div>
            </div>

            {{-- Quick Actions --}}
            <div class="quick-actions">
                <div class="action-card" onclick="window.location.href='{{ route('admisionista.ingresos') }}'">
                    <div class="action-icon">📥</div>
                    <div class="action-title">Nuevo Ingreso</div>
                    <div class="action-description">Registrar nuevo paciente</div>
                </div>
                <div class="action-card" onclick="window.location.href='{{ route('citas.index') }}'">
                    <div class="action-icon">📅</div>
                    <div class="action-title">Agendar Cita</div>
                    <div class="action-description">Programar cita médica</div>
                </div>
                <div class="action-card" onclick="window.location.href='{{ route('admisionista.pacientes') }}'">
                    <div class="action-icon">👥</div>
                    <div class="action-title">Gestión Pacientes</div>
                    <div class="action-description">Administrar pacientes</div>
                </div>
                <div class="action-card" onclick="window.location.href='{{ route('admisionista.reportes') }}'">
                    <div class="action-icon">📊</div>
                    <div class="action-title">Generar Reportes</div>
                    <div class="action-description">Reportes del sistema</div>
                </div>
            </div>

            {{-- Search Bar --}}
            <div class="search-bar">
                <input type="text" id="buscarDocumento" placeholder="Buscar paciente por documento...">
                <button onclick="buscarPaciente()">Buscar</button>
            </div>

            {{-- Table --}}
            <section>
                <h2 style="color:#065f5b; margin-bottom:15px;">Ingresos Recientes</h2>
                <table id="tablaIngresos">
                    <thead>
                        <tr>
                            <th>Documento</th>
                            <th>Nombre</th>
                            <th>Fecha Ingreso</th>
                            <th>Motivo</th>
                            <th>Estado</th>
                            <th style="width:150px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td colspan="6" style="text-align:center;">Cargando datos...</td>
                        </tr>
                    </tbody>
                </table>
            </section>

            {{-- Recent Activity --}}
            <div class="activity-card">
                <h3 style="color:#065f5b; margin-bottom:20px;">Actividad Reciente</h3>
                <div class="activity-list" id="listaActividad">
                    <div class="activity-item">
                        <div class="activity-icon">⏳</div>
                        <div class="activity-content">
                            <div class="activity-title">Cargando actividad...</div>
                        </div>
                    </div>
                </div>
            </div>

            <footer>
                © 2025 Mundo Pie – Sistema de Historia Clínica del Admisionista
            </footer>

        </main>
    </div>
@endsection

@section('scripts')
    <script>
        // Cargar datos al iniciar
        document.addEventListener('DOMContentLoaded', function() {
            cargarEstadisticas();
            cargarIngresosRecientes();
            cargarActividadReciente();
        });

        function cargarEstadisticas() {
            // Simular carga de estadísticas (en producción sería una API real)
            setTimeout(() => {
                document.getElementById('totalIngresos').textContent = '15';
                document.getElementById('pacientesActivos').textContent = '42';
                document.getElementById('citasHoy').textContent = '8';
                document.getElementById('urgencias').textContent = '3';
            }, 1000);
        }

        function cargarIngresosRecientes() {
            fetch('/api/admisionista/ingresos-recientes')
                .then(res => res.json())
                .then(data => {
                    const tbody = document.querySelector("#tablaIngresos tbody");
                    tbody.innerHTML = "";

                    if (!data || data.length === 0) {
                        tbody.innerHTML = 
                            `<tr><td colspan="6" style="text-align:center;">No se encontraron ingresos recientes</td></tr>`;
                        return;
                    }

                    data.forEach(ingreso => {
                        const fecha = new Date(ingreso.fecha_ingreso).toLocaleDateString('es-ES');
                        const estadoClass = ingreso.entorno_atencion === 'Urgencias' ? 'status-urgente' : 'status-normal';
                        
                        tbody.innerHTML += `
                        <tr>
                            <td>${ingreso.documento_id}</td>
                            <td>${ingreso.nombre_completo}</td>
                            <td>${fecha}</td>
                            <td>${ingreso.motivo_consulta}</td>
                            <td>
                                <span class="status-badge ${estadoClass}">
                                    ${ingreso.entorno_atencion}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-view" onclick="verDetalleIngreso('${ingreso.atencion_id}')">Ver</button>
                                <button class="btn btn-edit" onclick="editarIngreso('${ingreso.atencion_id}')">Editar</button>
                            </td>
                        </tr>`;
                    });
                })
                .catch(error => {
                    console.error('Error:', error);
                    const tbody = document.querySelector("#tablaIngresos tbody");
                    tbody.innerHTML = 
                        `<tr><td colspan="6" style="text-align:center; color: #dc3545;">Error al cargar datos</td></tr>`;
                });
        }

        function cargarActividadReciente() {
            // Simular actividad reciente
            setTimeout(() => {
                const actividades = [
                    { icon: '📥', title: 'Nuevo ingreso registrado', description: 'Carlos Arias - Urgencias', time: 'Hace 5 min' },
                    { icon: '📅', title: 'Cita agendada', description: 'María Gómez - Medicina General', time: 'Hace 15 min' },
                    { icon: '👤', title: 'Paciente dado de alta', description: 'Juan Pérez - Hospitalización', time: 'Hace 1 hora' },
                    { icon: '📋', title: 'Resultados cargados', description: 'Ana López - Laboratorio', time: 'Hace 2 horas' }
                ];

                const container = document.getElementById('listaActividad');
                let html = '';
                
                actividades.forEach(actividad => {
                    html += `
                    <div class="activity-item">
                        <div class="activity-icon">${actividad.icon}</div>
                        <div class="activity-content">
                            <div class="activity-title">${actividad.title}</div>
                            <div class="activity-description">${actividad.description}</div>
                            <div class="activity-time">${actividad.time}</div>
                        </div>
                    </div>`;
                });
                
                container.innerHTML = html;
            }, 1500);
        }

        function buscarPaciente() {
            const doc = document.getElementById("buscarDocumento").value.trim();
            if (!doc) return alert("Ingrese un número de documento.");

            fetch(`/api/admisionista/buscar/${doc}`)
                .then(res => res.json())
                .then(data => {
                    if (data.success && data.data && data.data.length > 0) {
                        const paciente = data.data[0];
                        alert(`Paciente encontrado:\n\nNombre: ${paciente.nombre_completo}\nDocumento: ${paciente.documento_id}\nEdad: ${paciente.edad} años`);
                        
                        // Redirigir a gestión de ingresos con el documento pre-cargado
                        window.location.href = `{{ route('admisionista.ingresos') }}?documento=${paciente.documento_id}`;
                    } else {
                        alert("Paciente no encontrado. Verifique el número de documento.");
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert("Error al buscar paciente. Intente nuevamente.");
                });
        }

        function verDetalleIngreso(atencionId) {
            alert(`Ver detalle del ingreso: ${atencionId}`);
            // En producción, redirigir a vista de detalle o abrir modal
        }

        function editarIngreso(atencionId) {
            alert(`Editar ingreso: ${atencionId}`);
            // En producción, redirigir a formulario de edición
        }

        // Búsqueda en tiempo real
        let searchTimeout;
        document.getElementById('buscarDocumento').addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.trim().length >= 3) {
                    // Opcional: implementar búsqueda en tiempo real
                }
            }, 500);
        });

        // Enter para buscar
        document.getElementById('buscarDocumento').addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                buscarPaciente();
            }
        });
    </script>
@endsection