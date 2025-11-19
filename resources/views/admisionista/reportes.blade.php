@extends('layouts.app')

@section('title', 'Reportes y Estadísticas')

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

    .reportes-container { 
        background: white; 
        padding: 25px; 
        border-radius: 12px; 
        box-shadow: 0 3px 15px rgba(0,0,0,0.08); 
    }

    .filtros-avanzados { 
        background: #f8f9fa; 
        padding: 20px; 
        border-radius: 8px; 
        margin-bottom: 25px; 
    }

    .metricas-grid { 
        display: grid; 
        grid-template-columns: repeat(4, 1fr); 
        gap: 20px; 
        margin-bottom: 30px; 
    }

    .metrica-card { 
        background: white; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
        text-align: center; 
        border-left: 4px solid #0d9488; 
    }

    .metrica-valor { 
        font-size: 2em; 
        font-weight: bold; 
        color: #0d9488; 
        margin: 10px 0; 
    }

    .graficos-grid { 
        display: grid; 
        grid-template-columns: 1fr 1fr; 
        gap: 25px; 
        margin-bottom: 30px; 
    }

    .grafico-card { 
        background: white; 
        padding: 20px; 
        border-radius: 8px; 
        box-shadow: 0 2px 8px rgba(0,0,0,0.1); 
    }

    .export-buttons { 
        display: flex; 
        gap: 10px; 
        margin-top: 20px; 
        flex-wrap: wrap;
    }

    .btn-export { 
        background: #28a745; 
        color: white; 
        border: none; 
        padding: 10px 16px; 
        border-radius: 6px; 
        cursor: pointer; 
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-export:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .btn-pdf { background: #dc3545; }
    .btn-excel { background: #28a745; }
    .btn-csv { background: #17a2b8; }
    .btn-print { background: #6c757d; }

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

    .reporte-info {
        background: #e7f3ff;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        border-left: 4px solid #0d6e6b;
    }

    @media print {
        .sidebar, .export-buttons, .filtros-avanzados {
            display: none !important;
        }
        .main-content {
            margin-left: 0 !important;
            width: 100% !important;
            padding: 20px !important;
        }
        .reportes-container {
            box-shadow: none !important;
        }
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
        
        .metricas-grid,
        .graficos-grid {
            grid-template-columns: 1fr;
        }
        
        .export-buttons {
            flex-direction: column;
        }
    }
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
@endsection

@section('content')
<div class="dashboard-container">
    <aside class="sidebar">
        <h2>Admisionista</h2>
        <ul class="nav-links">
            <li><a href="{{ route('admisionista.index') }}">🏠 Inicio</a></li>
            <li><a href="{{ route('admisionista.ingresos') }}">📥 Ingresos</a></li>
            <li><a href="{{ route('citas.index') }}">📅 Citas</a></li>
            <li><a href="{{ route('admisionista.pacientes') }}">👥 Pacientes</a></li>
            <li><a href="{{ route('admisionista.reportes') }}" style="background: rgba(255,255,255,0.1);">📊 Reportes</a></li>
        </ul>
        <button type="button" onclick="logout()" class="logout-btn">Cerrar sesión</button>
    </aside>

    <main class="main-content">
        <div class="header">
            <h1>Reportes y Estadísticas</h1>
            <div class="user-info">
                <span>Bienvenido, {{ Auth::user()->nombre_usuario }}</span>
            </div>
        </div>

        <div class="reportes-container">
            <!-- Información del Reporte -->
            <div class="reporte-info">
                <strong>📊 Reporte del Sistema</strong>
                <div style="margin-top: 5px; font-size: 0.9em;">
                    Generado el: <span id="fechaGeneracion">{{ now()->format('d/m/Y H:i') }}</span> | 
                    Usuario: {{ Auth::user()->nombre_usuario }}
                </div>
            </div>

            <!-- Filtros Avanzados -->
            <div class="filtros-avanzados">
                <h3 style="margin-bottom: 15px; color: #065f5b;">Filtros del Reporte</h3>
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 15px; align-items: end;">
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Fecha Desde</label>
                        <input type="date" id="fechaDesde" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Fecha Hasta</label>
                        <input type="date" id="fechaHasta" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                    </div>
                    <div>
                        <label style="display: block; margin-bottom: 5px; font-weight: 600;">Tipo de Reporte</label>
                        <select id="tipoReporte" style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 6px;">
                            <option value="general">General</option>
                            <option value="consultas">Consultas</option>
                            <option value="pacientes">Pacientes</option>
                            <option value="ingresos">Ingresos</option>
                            <option value="financiero">Financiero</option>
                        </select>
                    </div>
                    <div>
                        <button onclick="generarReporte()" style="background: #0d9488; color: white; border: none; padding: 12px 16px; border-radius: 6px; cursor: pointer; width: 100%; font-weight: 600;">
                            📊 Generar Reporte
                        </button>
                    </div>
                </div>
            </div>

            <!-- Métricas Principales -->
            <div class="metricas-grid" id="metricasContainer">
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Cargando métricas...</p>
                </div>
            </div>

            <!-- Gráficos -->
            <div class="graficos-grid">
                <div class="grafico-card">
                    <h4 style="margin-bottom: 15px; color: #065f5b;">Consultas por Especialidad</h4>
                    <canvas id="chartEspecialidades" height="250"></canvas>
                </div>
                <div class="grafico-card">
                    <h4 style="margin-bottom: 15px; color: #065f5b;">Pacientes por Edad</h4>
                    <canvas id="chartEdades" height="250"></canvas>
                </div>
                <div class="grafico-card">
                    <h4 style="margin-bottom: 15px; color: #065f5b;">Consultas por Mes</h4>
                    <canvas id="chartMensual" height="250"></canvas>
                </div>
                <div class="grafico-card">
                    <h4 style="margin-bottom: 15px; color: #065f5b;">Diagnósticos Comunes</h4>
                    <canvas id="chartDiagnosticos" height="250"></canvas>
                </div>
            </div>

            <!-- Tabla de Datos Detallados -->
            <div class="grafico-card">
                <h4 style="margin-bottom: 15px; color: #065f5b;">Datos Detallados</h4>
                <div style="overflow-x: auto;">
                    <table style="width: 100%; border-collapse: collapse;" id="tablaDetallesData">
                        <thead>
                            <tr style="background: #0d9488; color: white;">
                                <th style="padding: 12px; text-align: left;">Fecha</th>
                                <th style="padding: 12px; text-align: left;">Paciente</th>
                                <th style="padding: 12px; text-align: left;">Documento</th>
                                <th style="padding: 12px; text-align: left;">Médico</th>
                                <th style="padding: 12px; text-align: left;">Especialidad</th>
                                <th style="padding: 12px; text-align: left;">Diagnóstico</th>
                                <th style="padding: 12px; text-align: left;">Estado</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDetalles">
                            <tr>
                                <td colspan="7" style="padding: 20px; text-align: center; color: #666;">
                                    <div class="loading">
                                        <div class="loading-spinner"></div>
                                        <p>Cargando datos detallados...</p>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Botones de Exportación -->
            <div class="export-buttons">
                <button onclick="exportarPDF()" class="btn-export btn-pdf">📄 Exportar PDF</button>
                <button onclick="exportarExcel()" class="btn-export btn-excel">📊 Exportar Excel</button>
                <button onclick="exportarCSV()" class="btn-export btn-csv">📋 Exportar CSV</button>
                <button onclick="imprimirReporte()" class="btn-export btn-print">🖨️ Imprimir</button>
            </div>
        </div>
    </main>
</div>
@endsection

@section('scripts')
<script>
// Variables globales
let charts = {};
let datosReporte = {
    metricas: {},
    detalles: [],
    graficos: {}
};

// Cargar reporte al iniciar
document.addEventListener('DOMContentLoaded', function() {
    // Establecer fechas por defecto (último mes)
    const hoy = new Date();
    const haceUnMes = new Date();
    haceUnMes.setMonth(hoy.getMonth() - 1);
    
    document.getElementById('fechaDesde').value = haceUnMes.toISOString().split('T')[0];
    document.getElementById('fechaHasta').value = hoy.toISOString().split('T')[0];
    
    generarReporte();
});

function generarReporte() {
    const fechaDesde = document.getElementById('fechaDesde').value;
    const fechaHasta = document.getElementById('fechaHasta').value;
    const tipoReporte = document.getElementById('tipoReporte').value;

    // Mostrar loading
    mostrarLoading();

    const params = new URLSearchParams({
        fecha_desde: fechaDesde,
        fecha_hasta: fechaHasta,
        tipo: tipoReporte
    });

    // Actualizar fecha de generación
    document.getElementById('fechaGeneracion').textContent = new Date().toLocaleString('es-ES');

    // Cargar métricas
    fetch(`/api/admisionista/reportes/metricas?${params}`)
        .then(r => r.json())
        .then(data => {
            datosReporte.metricas = data.metricas || {};
            renderMetricas(datosReporte.metricas);
        })
        .catch(error => {
            console.error('Error cargando métricas:', error);
            document.getElementById('metricasContainer').innerHTML = 
                '<div style="text-align: center; padding: 20px; color: #dc3545;">Error cargando métricas</div>';
        });

    // Cargar gráficos
    fetch(`/api/admisionista/reportes/graficos?${params}`)
        .then(r => r.json())
        .then(data => {
            datosReporte.graficos = data.graficos || {};
            renderGraficos(datosReporte.graficos);
        })
        .catch(error => {
            console.error('Error cargando gráficos:', error);
        });

    // Cargar datos detallados
    fetch(`/api/admisionista/reportes/detalles?${params}`)
        .then(r => r.json())
        .then(data => {
            datosReporte.detalles = data.detalles || [];
            renderDetalles(datosReporte.detalles);
        })
        .catch(error => {
            console.error('Error cargando detalles:', error);
            document.getElementById('tablaDetalles').innerHTML = 
                '<tr><td colspan="7" style="text-align: center; padding: 20px; color: #dc3545;">Error cargando datos</td></tr>';
        });
}

function mostrarLoading() {
    document.getElementById('metricasContainer').innerHTML = `
        <div class="loading">
            <div class="loading-spinner"></div>
            <p>Cargando métricas...</p>
        </div>`;
    
    document.getElementById('tablaDetalles').innerHTML = `
        <tr>
            <td colspan="7" style="padding: 20px; text-align: center; color: #666;">
                <div class="loading">
                    <div class="loading-spinner"></div>
                    <p>Cargando datos detallados...</p>
                </div>
            </td>
        </tr>`;
}

function renderMetricas(metricas) {
    const container = document.getElementById('metricasContainer');
    
    const metricasData = [
        { label: 'Total Consultas', valor: metricas.total_consultas || 0, icon: '📋' },
        { label: 'Pacientes Atendidos', valor: metricas.pacientes_atendidos || 0, icon: '👥' },
        { label: 'Ingresos Registrados', valor: metricas.total_ingresos || 0, icon: '📥' },
        { label: 'Citas Programadas', valor: metricas.total_citas || 0, icon: '📅' }
    ];

    let html = '';
    metricasData.forEach(metrica => {
        html += `
        <div class="metrica-card">
            <div style="font-size: 1.5em; margin-bottom: 10px;">${metrica.icon}</div>
            <div class="metrica-valor">${metrica.valor}</div>
            <div style="color: #666; font-size: 0.9em;">${metrica.label}</div>
        </div>`;
    });
    container.innerHTML = html;
}

function renderGraficos(graficos) {
    // Destruir gráficos existentes
    Object.values(charts).forEach(chart => chart.destroy());
    charts = {};

    // Datos de ejemplo si la API no responde
    const datosEjemplo = {
        especialidades: {
            labels: ['Medicina General', 'Urgencias', 'Pediatría', 'Cardiología', 'Dermatología'],
            data: [45, 30, 25, 15, 10]
        },
        edades: {
            labels: ['0-18', '19-35', '36-50', '51-65', '65+'],
            data: [20, 35, 25, 15, 5]
        },
        mensual: {
            labels: ['Ene', 'Feb', 'Mar', 'Abr', 'May', 'Jun'],
            data: [65, 59, 80, 81, 56, 55]
        },
        diagnosticos: {
            labels: ['Hipertensión', 'Diabetes', 'Gripe', 'Dolor lumbar', 'Ansiedad'],
            data: [25, 20, 15, 12, 8]
        }
    };

    const datos = Object.keys(graficos).length > 0 ? graficos : datosEjemplo;

    // Gráfico de especialidades
    const ctx1 = document.getElementById('chartEspecialidades').getContext('2d');
    charts.especialidades = new Chart(ctx1, {
        type: 'doughnut',
        data: {
            labels: datos.especialidades.labels,
            datasets: [{
                data: datos.especialidades.data,
                backgroundColor: ['#0d9488', '#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });

    // Gráfico de edades
    const ctx2 = document.getElementById('chartEdades').getContext('2d');
    charts.edades = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: datos.edades.labels,
            datasets: [{
                label: 'Pacientes',
                data: datos.edades.data,
                backgroundColor: '#0d9488'
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico mensual
    const ctx3 = document.getElementById('chartMensual').getContext('2d');
    charts.mensual = new Chart(ctx3, {
        type: 'line',
        data: {
            labels: datos.mensual.labels,
            datasets: [{
                label: 'Consultas',
                data: datos.mensual.data,
                borderColor: '#0d9488',
                backgroundColor: 'rgba(13, 148, 136, 0.1)',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Gráfico de diagnósticos
    const ctx4 = document.getElementById('chartDiagnosticos').getContext('2d');
    charts.diagnosticos = new Chart(ctx4, {
        type: 'polarArea',
        data: {
            labels: datos.diagnosticos.labels,
            datasets: [{
                data: datos.diagnosticos.data,
                backgroundColor: ['#0d9488', '#3b82f6', '#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                }
            }
        }
    });
}

function renderDetalles(detalles) {
    const tbody = document.getElementById('tablaDetalles');
    
    // Datos de ejemplo si la API no responde
    const datosEjemplo = [
        {
            fecha: '2024-01-15',
            paciente: 'Carlos Arias',
            documento: '1001001001',
            medico: 'Dra. Andrea Borrero',
            especialidad: 'Medicina Interna',
            diagnostico: 'Hipertensión esencial',
            estado: 'Completado'
        },
        {
            fecha: '2024-01-14',
            paciente: 'María Gómez',
            documento: '1002002002',
            medico: 'Dr. Luis Pineda',
            especialidad: 'Endocrinología',
            diagnostico: 'Diabetes mellitus',
            estado: 'Completado'
        }
    ];

    const datos = detalles.length > 0 ? detalles : datosEjemplo;

    if (datos.length === 0) {
        tbody.innerHTML = '<tr><td colspan="7" style="padding: 20px; text-align: center; color: #666;">No hay datos para mostrar</td></tr>';
        return;
    }

    let html = '';
    datos.forEach(item => {
        html += `
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 12px;">${item.fecha}</td>
            <td style="padding: 12px;">${item.paciente}</td>
            <td style="padding: 12px;">${item.documento || 'N/A'}</td>
            <td style="padding: 12px;">${item.medico}</td>
            <td style="padding: 12px;">${item.especialidad}</td>
            <td style="padding: 12px;">${item.diagnostico}</td>
            <td style="padding: 12px;">
                <span style="padding: 4px 8px; border-radius: 12px; font-size: 0.8em; background: ${item.estado === 'Completado' ? '#d4edda' : '#fff3cd'}; color: ${item.estado === 'Completado' ? '#155724' : '#856404'};">
                    ${item.estado}
                </span>
            </td>
        </tr>`;
    });
    tbody.innerHTML = html;
}

// ==================== FUNCIONES DE EXPORTACIÓN ====================

function exportarPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    // Configuración del documento
    doc.setFontSize(16);
    doc.setTextColor(6, 95, 91);
    doc.text('REPORTE DEL SISTEMA - MUNDO PIE', 105, 20, { align: 'center' });
    
    doc.setFontSize(10);
    doc.setTextColor(100, 100, 100);
    doc.text(`Generado el: ${new Date().toLocaleString('es-ES')}`, 105, 30, { align: 'center' });
    doc.text(`Usuario: {{ Auth::user()->nombre_usuario }}`, 105, 35, { align: 'center' });
    
    // Métricas
    doc.setFontSize(12);
    doc.setTextColor(0, 0, 0);
    doc.text('MÉTRICAS PRINCIPALES', 20, 50);
    
    let yPosition = 60;
    const metricas = [
        { label: 'Total Consultas', valor: datosReporte.metricas.total_consultas || 0 },
        { label: 'Pacientes Atendidos', valor: datosReporte.metricas.pacientes_atendidos || 0 },
        { label: 'Ingresos Registrados', valor: datosReporte.metricas.total_ingresos || 0 },
        { label: 'Citas Programadas', valor: datosReporte.metricas.total_citas || 0 }
    ];
    
    metricas.forEach((metrica, index) => {
        doc.setFillColor(240, 240, 240);
        doc.rect(20, yPosition + (index * 10), 170, 8, 'F');
        doc.text(`${metrica.label}:`, 25, yPosition + 5 + (index * 10));
        doc.text(metrica.valor.toString(), 160, yPosition + 5 + (index * 10), { align: 'right' });
    });
    
    // Datos detallados
    yPosition += 60;
    doc.text('DATOS DETALLADOS', 20, yPosition);
    yPosition += 10;
    
    const headers = [['Fecha', 'Paciente', 'Documento', 'Médico', 'Especialidad', 'Diagnóstico', 'Estado']];
    const data = datosReporte.detalles.map(item => [
        item.fecha,
        item.paciente,
        item.documento || 'N/A',
        item.medico,
        item.especialidad,
        item.diagnostico,
        item.estado
    ]);
    
    doc.autoTable({
        startY: yPosition,
        head: headers,
        body: data.length > 0 ? data : [['No hay datos disponibles']],
        theme: 'grid',
        headStyles: {
            fillColor: [13, 148, 136],
            textColor: 255
        },
        styles: {
            fontSize: 8,
            cellPadding: 2
        }
    });
    
    // Guardar PDF
    doc.save(`reporte-mundo-pie-${new Date().toISOString().split('T')[0]}.pdf`);
}

function exportarExcel() {
    // Preparar datos para Excel
    const datos = datosReporte.detalles.map(item => ({
        'Fecha': item.fecha,
        'Paciente': item.paciente,
        'Documento': item.documento || 'N/A',
        'Médico': item.medico,
        'Especialidad': item.especialidad,
        'Diagnóstico': item.diagnostico,
        'Estado': item.estado
    }));
    
    if (datos.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    
    // Crear workbook y worksheet
    const ws = XLSX.utils.json_to_sheet(datos);
    const wb = XLSX.utils.book_new();
    XLSX.utils.book_append_sheet(wb, ws, 'Reporte');
    
    // Generar y descargar archivo
    XLSX.writeFile(wb, `reporte-mundo-pie-${new Date().toISOString().split('T')[0]}.xlsx`);
}

function exportarCSV() {
    const datos = datosReporte.detalles;
    
    if (datos.length === 0) {
        alert('No hay datos para exportar');
        return;
    }
    
    // Encabezados CSV
    let csv = 'Fecha,Paciente,Documento,Médico,Especialidad,Diagnóstico,Estado\n';
    
    // Datos CSV
    datos.forEach(item => {
        csv += `"${item.fecha}","${item.paciente}","${item.documento || 'N/A'}","${item.medico}","${item.especialidad}","${item.diagnostico}","${item.estado}"\n`;
    });
    
    // Crear y descargar archivo
    const blob = new Blob([csv], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    link.setAttribute('href', url);
    link.setAttribute('download', `reporte-mundo-pie-${new Date().toISOString().split('T')[0]}.csv`);
    link.style.visibility = 'hidden';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

function imprimirReporte() {
    window.print();
}

// Función de logout
function logout() {
    if (confirm('¿Está seguro de que desea cerrar sesión?')) {
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