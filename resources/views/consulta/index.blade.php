@extends('layouts.app')

@section('title', 'Panel del Paciente - Consultas Médicas')

@section('content')
<style>
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Poppins', sans-serif;
    }

    body {
        display: flex;
        min-height: 100vh;
        background-color: #f0f4f8;
    }

    .sidebar {
        width: 260px;
        background-color: #007bff;
        color: white;
        display: flex;
        flex-direction: column;
        padding: 25px;
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        box-shadow: 2px 0 8px rgba(0,0,0,0.15);
    }

    .sidebar h2 {
        text-align: center;
        margin-bottom: 40px;
        font-size: 1.7em;
        letter-spacing: 1px;
    }

    .sidebar a {
        text-decoration: none;
        color: white;
        padding: 12px;
        border-radius: 8px;
        margin-bottom: 10px;
        display: block;
        font-size: 1.1em;
        transition: background 0.3s, padding-left 0.3s;
    }

    .sidebar a:hover {
        background: rgba(255,255,255,0.25);
        padding-left: 18px;
    }

    .main {
        flex: 1;
        margin-left: 260px;
        padding: 40px;
        background-color: #ffffff;
        min-height: 100vh;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
    }

    .header h1 {
        color: #333;
        font-size: 2em;
    }

    .logout-btn {
        background-color: #ff4757;
        border: none;
        color: white;
        padding: 10px 18px;
        border-radius: 8px;
        cursor: pointer;
        transition: background 0.3s;
    }

    .search-bar {
        display: flex;
        gap: 10px;
        margin-bottom: 30px;
    }

    .search-bar input {
        flex: 1;
        padding: 12px;
        border: 1px solid #ccc;
        border-radius: 10px;
        font-size: 1.1em;
    }

    .search-bar button {
        background-color: #007bff;
        color: white;
        border: none;
        padding: 12px 22px;
        border-radius: 8px;
        cursor: pointer;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background: white;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 0 10px rgba(0,0,0,0.08);
    }

    th, td {
        padding: 16px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    th {
        background-color: #007bff;
        color: white;
    }

    tr:hover {
        background-color: #f1f7ff;
    }

    .empty {
        text-align: center;
        padding: 25px;
        color: #777;
    }
</style>

<div class="sidebar">
    <h2>Paciente</h2>
    <a href="{{ route('paciente.home') }}">🏠 Inicio</a>
    <a href="{{ route('paciente.consultas') }}" style="background: rgba(0,0,0,0.2)">📋 Mis Consultas</a>
</div>

<div class="main">
    <div class="header">
        <h1>Consultas Médicas</h1>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button class="logout-btn" type="submit">Cerrar Sesión</button>
        </form>
    </div>

    <div class="search-bar">
        <input type="text" id="documentoInput" placeholder="Ingrese su número de documento">
        <button onclick="buscarConsultas()">Buscar</button>
    </div>

    <table id="tablaConsultas">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Especialidad</th>
                <th>Profesional</th>
                <th>Motivo</th>
                <th>Diagnóstico</th>
                <th>Tratamiento</th>
                <th>Estado</th>
            </tr>
        </thead>

        <tbody id="consultaBody">
            <tr>
                <td colspan="7" class="empty">Ingrese su número de documento para ver sus consultas</td>
            </tr>
        </tbody>
    </table>
</div>

@endsection

@section('scripts')
<script>
function buscarConsultas() {
    const doc = document.getElementById("documentoInput").value.trim();
    const tbody = document.getElementById("consultaBody");

    if (!doc) return alert("Ingrese su número de documento.");

    fetch(`/api/paciente/consultas/${doc}`)
        .then(r => r.json())
        .then(data => {
            tbody.innerHTML = "";

            if (!data.length) {
                tbody.innerHTML = `<tr><td colspan="7" class="empty">No hay consultas</td></tr>`;
                return;
            }

            data.forEach(c => {
                tbody.innerHTML += `
                    <tr>
                        <td>${c.fecha}</td>
                        <td>${c.especialidad}</td>
                        <td>${c.profesional}</td>
                        <td>${c.motivo}</td>
                        <td>${c.diagnostico}</td>
                        <td>${c.tratamiento}</td>
                        <td>${c.estado}</td>
                    </tr>`;
            });
        })
        .catch(() => alert("Error consultando datos"));
}
</script>
@endsection
