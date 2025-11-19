<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pacientes - Médico</title>

  <!-- Bootstrap -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- Estilos personalizados (copiados del de Consultas) -->
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
          <a class="nav-link" href="{{ route('medico.consultas') }}">📋 Consultas</a>
        </li>
        <li class="nav-item">
          <a class="nav-link active" href="{{ route('medico.pacientes') }}">👤 Pacientes</a>
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

  <!-- Main content -->
  <main class="main-content">
    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
      <h1 class="h2">Gestión de Pacientes</h1>
      <div class="btn-toolbar mb-2 mb-md-0">
        <button class="btn btn-sm btn-outline-secondary">
          Nuevo Paciente
        </button>
      </div>
    </div>

    <!-- Filtros -->
    <div class="row mb-3">
      <div class="col-md-4">
        <input type="text" class="form-control" placeholder="Buscar paciente...">
      </div>
      <div class="col-md-3">
        <select class="form-select">
          <option>Todos los estados</option>
          <option>Activo</option>
          <option>Inactivo</option>
        </select>
      </div>
    </div>

    <!-- Tabla -->
    <div class="table-responsive">
      <table class="table table-striped table-sm">
        <thead>
          <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Edad</th>
            <th>Teléfono</th>
            <th>Última Visita</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <tr>
            <td>P001</td>
            <td>Juan Pérez</td>
            <td>45</td>
            <td>555-1234</td>
            <td>2024-01-15</td>
            <td>
              <button class="btn btn-sm btn-info me-1">Historial</button>
              <button class="btn btn-sm btn-warning">Editar</button>
            </td>
          </tr>

          <tr>
            <td>P002</td>
            <td>María García</td>
            <td>32</td>
            <td>555-5678</td>
            <td>2024-01-10</td>
            <td>
              <button class="btn btn-sm btn-info me-1">Historial</button>
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
