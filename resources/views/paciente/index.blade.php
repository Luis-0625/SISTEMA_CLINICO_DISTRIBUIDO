@extends('layouts.app')

@section('title', 'Panel del Paciente')

@section('content')
<style>
    body {
        margin: 0;
        padding: 0;
        font-family: Arial, sans-serif;
        background-color: #f4f6f9;
    }

    .sidebar {
        width: 260px;
        height: 100vh;
        background-color: #007bff;
        color: #fff;
        position: fixed;
        top: 0;
        left: 0;
        padding: 20px;
        box-sizing: border-box;
    }

    .sidebar h2 {
        font-size: 22px;
        margin-bottom: 30px;
        font-weight: bold;
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

    .content {
        margin-left: 260px;
        padding: 30px;
    }

    .header {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .header h1 {
        font-size: 26px;
        margin: 0;
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

    .card-container {
        margin-top: 30px;
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 20px;
    }

    .card {
        background: #fff;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 4px 10px rgba(0,0,0,0.08);
        text-align: center;
    }

    .card h3 {
        font-size: 20px;
        margin-bottom: 10px;
    }

    .card p {
        font-size: 16px;
        color: #555;
    }

    .card a {
        display: inline-block;
        margin-top: 10px;
        background-color: #007bff;
        color: #fff;
        padding: 8px 14px;
        border-radius: 6px;
        text-decoration: none;
        font-size: 14px;
    }

    .card a:hover {
        background-color: #0056b3;
    }
</style>

<div class="sidebar">
    <h2>Paciente</h2>

    <a href="{{ route('paciente.home') }}" class="active">🏠 Inicio</a>
    <a href="{{ route('paciente.consultas') }}">📋 Mis Consultas</a>
     <a href="{{ route('paciente.resultados') }}">📄 Mis Resultados</a>

</div>

<div class="content">
   <div class="header">
    <h1>Bienvenido al Panel del Paciente</h1>
    
    <button class="logout-btn" onclick="logout()">Cerrar Sesión</button>
</div>

    <div class="card-container">
        <div class="card">
            <h3>Consultas Médicas</h3>
            <p>Visualiza el historial completo de tus consultas.</p>
            <a href="{{ route('paciente.consultas') }}">Ver Consultas</a>
        </div>

        <div class="card">
            <h3>Resultados Clínicos</h3>
            <p>Accede a resultados de laboratorios y estudios.</p>
            <a href="#">Ver Resultados</a>
        </div>

        <div class="card">
            <h3>Citas Médicas</h3>
            <p>Consulta tus citas programadas y próximas fechas.</p>
            <a href="#">Ver Citas</a>
        </div>
    </div>
</div>

@endsection
