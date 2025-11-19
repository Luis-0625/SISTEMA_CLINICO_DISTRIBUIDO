@extends('layouts.app')

@section('title', 'Mis Resultados Clínicos')

@section('content')

<style>
    /* --- LAYOUT --- */
    .dashboard-container {
        display: flex;
        min-height: 100vh;
        background: #f4f6f9;
    }

    /* --- SIDEBAR --- */
    .sidebar {
        width: 260px;
        background: #0d6efd;
        color: #fff;
        padding: 25px 20px;
        position: fixed;
        top: 0;
        bottom: 0;
        left: 0;
        box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    }

    .sidebar h2 {
        font-size: 22px;
        margin-bottom: 25px;
        font-weight: bold;
    }

    .sidebar a {
        display: block;
        padding: 12px 14px;
        background: rgba(255, 255, 255, 0.10);
        margin-bottom: 8px;
        color: #fff;
        text-decoration: none;
        border-radius: 8px;
        transition: .2s;
        font-size: 15px;
    }

    .sidebar a:hover {
        background: rgba(255, 255, 255, 0.20);
    }

    .sidebar a.active {
        background: #0056d6 !important;
        font-weight: bold;
    }

    .sidebar button {
        width: 100%;
        padding: 12px;
        border: none;
        background: #dc3545;
        color: white;
        border-radius: 8px;
        cursor: pointer;
        margin-top: 20px;
        font-weight: bold;
    }

    /* --- MAIN CONTENT --- */
    .main-content {
        margin-left: 260px;
        padding: 35px;
        width: calc(100% - 260px);
    }

    .main-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
    }

    .main-header h1 {
        font-weight: 700;
        color: #333;
        font-size: 28px;
    }

    /* --- FILTROS --- */
    .filtros {
        background: white;
        padding: 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        box-shadow: 0 3px 8px rgba(0,0,0,0.06);
    }

    .filtros label {
        font-weight: 600;
        margin-bottom: 6px;
        display: block;
    }

    .filtros input,
    .filtros select {
        padding: 10px 12px;
        border: 1px solid #ccc;
        border-radius: 6px;
        width: 100%;
    }

    .btn-filtrar {
        background: #0d6efd;
        color: #fff;
        border: none;
        padding: 10px 16px;
        border-radius: 6px;
        cursor: pointer;
        font-weight: 600;
        height: fit-content;
        margin-top: 26px;
    }

    /* --- RESULTADOS --- */
    .resultados-grid {
        display: grid;
        gap: 20px;
    }

    .resultado-card {
        background: white;
        padding: 20px;
        border-radius: 12px;
        border-left: 4px solid #0d6efd;
        box-shadow: 0 4px 10px rgba(0,0,0,0.07);
    }

    .estado-tag {
        padding: 5px 10px;
        border-radius: 12px;
        font-size: 0.85em;
        font-weight: 600;
    }

    .estado-normal {
        background: #d4edda;
        color: #155724;
    }

    .estado-anormal {
        background: #f8d7da;
        color: #721c24;
    }

    .resultado-boton {
        padding: 8px 14px;
        border: none;
        border-radius: 6px;
        color: white;
        cursor: pointer;
        font-size: 0.9em;
        font-weight: 600;
    }

    .btn-descargar { background: #28a745; }
    .btn-compartir { background: #17a2b8; }

</style>

<div class="dashboard-container">

    <!-- SIDEBAR -->
    <div class="sidebar">
        <h2>Paciente</h2>

        <a href="{{ route('paciente.home') }}">🏠 Inicio</a>
        <a href="{{ route('paciente.consultas') }}">📋 Mis Consultas</a>
        <a class="active" href="{{ route('paciente.resultados') }}">📄 Mis Resultados</a>
       

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit">Cerrar Sesión</button>
        </form>
    </div>

    <!-- MAIN CONTENT -->
    <div class="main-content">

        <div class="main-header">
            <h1>Mis Resultados Clínicos</h1>
        </div>

        <!-- FILTROS -->
        <div class="filtros">
            <div class="row g-3">

                <div class="col-md-4">
                    <label>Tipo de Examen</label>
                    <select id="filtroTipo">
                        <option value="">Todos los tipos</option>
                        <option value="laboratorio">Laboratorio</option>
                        <option value="imagen">Imágenes</option>
                        <option value="procedimiento">Procedimientos</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>Fecha Desde</label>
                    <input type="date" id="filtroDesde">
                </div>

                <div class="col-md-3">
                    <label>Fecha Hasta</label>
                    <input type="date" id="filtroHasta">
                </div>

                <div class="col-md-2">
                    <button class="btn-filtrar" onclick="cargarResultados()">Filtrar</button>
                </div>
            </div>
        </div>

        <!-- RESULTADOS -->
        <div class="resultados-grid" id="listaResultados">
            <div style="text-align:center; padding:40px; color:#666;">
                <p>Ingrese su documento para ver los resultados</p>
            </div>
        </div>

    </div>
</div>

@endsection


@section('scripts')
<script>
function cargarResultados() {
    const documento = '{{ Auth::user()->documento_id }}';
    const tipo = filtroTipo.value;
    const desde = filtroDesde.value;
    const hasta = filtroHasta.value;

    let url = `/api/paciente/resultados/${documento}`;
    const params = new URLSearchParams();

    if (tipo) params.append('tipo', tipo);
    if (desde) params.append('desde', desde);
    if (hasta) params.append('hasta', hasta);

    if (params.toString()) url += '?' + params.toString();

    fetch(url)
        .then(r => r.json())
        .then(data => {
            const container = listaResultados;

            if (!data.length) {
                container.innerHTML = `
                    <div style="text-align:center; padding:40px; color:#666;">
                        No se encontraron resultados
                    </div>`;
                return;
            }

            let html = '';
            data.forEach(resultado => {
                html += `
                <div class="resultado-card">

                    <div style="display:flex; justify-content:space-between; margin-bottom:12px;">
                        <div>
                            <h3 style="margin:0; color:#333;">${resultado.tipo_examen}</h3>
                            <p style="margin:0; color:#666;">${resultado.fecha_realizacion}</p>
                        </div>

                        <span class="estado-tag ${resultado.estado === 'Normal' ? 'estado-normal' : 'estado-anormal'}">
                            ${resultado.estado}
                        </span>
                    </div>

                    <p><strong>Solicitado por:</strong> ${resultado.medico_solicitante}</p>
                    <p><strong>Procedimiento:</strong> ${resultado.procedimiento}</p>

                    ${resultado.resultados ? `
                    <div style="background:#f8f9fa; padding:12px; border-radius:8px;">
                        <strong>Resultados:</strong>
                        <pre style="white-space:pre-wrap; margin-top:8px;">${resultado.resultados}</pre>
                    </div>` : ''}

                    ${resultado.observaciones ? `
                    <div style="background:#fff3cd; padding:10px; margin-top:10px; border-radius:8px;">
                        <strong>Observaciones:</strong> ${resultado.observaciones}
                    </div>` : ''}

                    <div style="margin-top:15px; display:flex; gap:10px;">
                        <button class="resultado-boton btn-descargar" onclick="descargarResultado('${resultado.id}')">📥 Descargar</button>
                        <button class="resultado-boton btn-compartir" onclick="compartirResultado('${resultado.id}')">🔗 Compartir</button>
                    </div>

                </div>`;
            });

            container.innerHTML = html;
        })
        .catch(err => {
            console.error(err);
            alert("Error al cargar los resultados");
        });
}

function descargarResultado(id) {
    alert("Descargando resultado " + id);
}

function compartirResultado(id) {
    alert("Compartiendo resultado " + id);
}

document.addEventListener('DOMContentLoaded', cargarResultados);
</script>
@endsection
