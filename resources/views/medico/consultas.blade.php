<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Consultas - Médico</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos personalizados -->
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
      background: rgba(255, 255, 255, 0.15);
      color: #fff;
    }

    .sidebar .nav-link.active {
      background: rgba(255, 255, 255, 0.25);
      color: #fff;
      font-weight: bold;
    }

    /* CONTENIDO PRINCIPAL */
    .main-content {
      margin-left: 260px;
      padding: 40px;
      width: calc(100% - 260px);
    }

    /* Tablas y tarjetas */
    .table {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 3px 10px rgba(0,0,0,0.08);
    }

    .btn-outline-secondary {
      border-radius: 8px;
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
          <a class="nav-link active" href="{{ route('medico.consultas') }}">📋 Consultas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('medico.pacientes') }}">👤 Pacientes</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('medico.agenda') }}">🗓 Agenda</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="{{ route('medico.reportes') }}">📊 Reportes</a>
        </li>
      </ul>

      <form method="POST" action="{{ route('logout') }}" class="mt-4">
        @csrf
        <button type="submit"
          style="background:#dc3545; color:white; border:none; padding:12px; width:100%; border-radius:8px; cursor:pointer;">
          Cerrar Sesión
        </button>
      </form>
    </div>
  </nav>

  <!-- Main Content -->
  <main class="main-content">
    <div class="d-flex justify-content-between align-items-center pb-2 mb-3 border-bottom">
      <h1 class="h2">Consultas Médicas</h1>
      <div>
        <button class="btn btn-sm btn-outline-secondary">Nueva Consulta</button>
      </div>
    </div>

    <!-- Tabla de consultas -->
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Paciente</th>
            <th>Fecha</th>
            <th>Hora</th>
            <th>Estado</th>
            <th>Acciones</th>
          </tr>
        </thead>

        <tbody>
          <tr>
            <td>001</td>
            <td>Juan Pérez</td>
            <td>2024-01-15</td>
            <td>10:00 AM</td>
            <td><span class="badge bg-success">Completada</span></td>
            <td>
              <button class="btn btn-sm btn-info me-1">Ver</button>
              <button class="btn btn-sm btn-warning">Editar</button>
            </td>
          </tr>
          <tr>
            <td>002</td>
            <td>María García</td>
            <td>2024-01-15</td>
            <td>11:00 AM</td>
            <td><span class="badge bg-warning text-dark">Pendiente</span></td>
            <td>
              <button class="btn btn-sm btn-info me-1">Ver</button>
              <button class="btn btn-sm btn-warning">Editar</button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

  </main>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
