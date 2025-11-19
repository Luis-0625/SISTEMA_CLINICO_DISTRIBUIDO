<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes - Médico</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Estilos personalizados (MISMOS QUE CONSULTAS / PACIENTES) -->
    <style>
        /* Layout general */
        .dashboard-container { display: flex; min-height: 100vh; }

        /* SIDEBAR */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #0d6e6b, #065f5b);
            color: #fff;
            padding: 30px 0;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
        }

        .sidebar h5 {
            text-align: center;
            margin-bottom: 25px;
            font-weight: bold;
        }

        .sidebar .nav-link {
            color: #d9f7f5;
            padding: 12px 25px;
            font-size: 16px;
            transition: 0.2s;
            border-radius: 6px;
            display: block;
        }

        .sidebar .nav-link:hover {
            background: rgba(255,255,255,0.15);
            color: #fff;
        }

        .sidebar .nav-link.active {
            background: rgba(255,255,255,0.25);
            color: #fff;
            font-weight: bold;
        }

        /* CONTENIDO PRINCIPAL */
        .main-content {
            margin-left: 260px;
            padding: 40px;
            width: calc(100% - 260px);
        }

        /* Tarjetas, tablas y elementos */
        .card {
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.08);
        }

        .table {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
        }

        .btn-outline-secondary,
        .btn-outline-danger {
            border-radius: 8px;
        }

        .card-header {
            border-top-left-radius: 12px !important;
            border-top-right-radius: 12px !important;
        }
    </style>

</head>
<body>

<div class="dashboard-container">

    <!-- Sidebar -->
    <nav class="sidebar">
        <div class="position-sticky px-3">
            <h5>Panel Médico</h5>

            <ul class="nav flex-column">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medico.index') }}">🏠 Inicio</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medico.consultas') }}">📋 Consultas</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medico.pacientes') }}">👤 Pacientes</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('medico.agenda') }}">🗓 Agenda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('medico.reportes') }}">📊 Reportes</a>
                </li>
            </ul>

            <form method="POST" action="{{ route('logout') }}" class="mt-4">
                @csrf
                <button type="submit"
                        style="background:#dc3545; color:white; border:none; padding:12px;
                               width:100%; border-radius:8px; cursor:pointer;">
                    Cerrar Sesión
                </button>
            </form>
        </div>
    </nav>

    <!-- Main content -->
    <main class="main-content">

        <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
            <h1 class="h2">Reportes Médicos</h1>
            <button class="btn btn-sm btn-outline-danger">Exportar PDF</button>
        </div>

        <!-- Estadísticas -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card text-white bg-primary">
                    <div class="card-body">
                        <h5 class="card-title">Consultas Hoy</h5>
                        <h2 class="card-text">8</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body">
                        <h5 class="card-title">Pacientes Activos</h5>
                        <h2 class="card-text">45</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body">
                        <h5 class="card-title">Citas Pendientes</h5>
                        <h2 class="card-text">12</h2>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body">
                        <h5 class="card-title">Ingresos Mes</h5>
                        <h2 class="card-text">$5,200</h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros de reportes -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title mb-0">Generar Reporte</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">

                    <div class="col-md-3">
                        <label>Fecha Inicio</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Fecha Fin</label>
                        <input type="date" class="form-control">
                    </div>

                    <div class="col-md-3">
                        <label>Tipo de Reporte</label>
                        <select class="form-select">
                            <option>Consultas por Médico</option>
                            <option>Pacientes por Edad</option>
                            <option>Ingresos Mensuales</option>
                            <option>Procedimientos Realizados</option>
                        </select>
                    </div>

                    <div class="col-md-3 d-flex align-items-end">
                        <button class="btn btn-primary w-100">Generar</button>
                    </div>

                </div>
            </div>
        </div>

        <!-- Placeholder de gráfico -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Consultas Mensuales</h5>
            </div>
            <div class="card-body">
                <div style="height: 300px; background: #f8f9fa; display: flex;
                            align-items: center; justify-content: center;">
                    <p class="text-muted">El gráfico se mostrará aquí</p>
                </div>
            </div>
        </div>

    </main>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
