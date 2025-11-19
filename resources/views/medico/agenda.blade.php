<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda - Médico</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css' rel='stylesheet' />

    <style>
        body {
            background: #eef3f7;
        }

        /* === SIDEBAR IGUAL A CONSULTAS === */
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

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.85);
            padding: 12px 22px;
            font-size: 16px;
            margin-bottom: 6px;
            display: block;
            transition: 0.2s;
        }

        .sidebar .nav-link:hover {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border-radius: 6px;
        }

        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.25);
            color: white;
            font-weight: bold;
            border-radius: 6px;
        }

        /* Botón cerrar sesión */
        .logout-btn {
            margin-top: 20px;
            background: #c0392b !important;
            color: white !important;
            border-radius: 8px;
            width: 85%;
            margin-left: 20px;
            padding: 10px;
            border: none;
        }

        /* MAIN CONTENT */
        .main-content {
            margin-left: 260px;
            padding: 40px;
        }

        #calendar {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0px 4px 10px rgba(0,0,0,0.08);
        }
    </style>
</head>
<body>

    <div class="container-fluid">
        <div class="row">

            <!-- Sidebar igual a CONSULTAS -->
            <nav class="col-md-3 col-lg-2 sidebar">
                <div class="position-sticky pt-3">
                 
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

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="logout-btn">
                            Cerrar Sesión
                        </button>
                    </form>
                </div>
            </nav>

            <!-- Main content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 main-content">

                <div 
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Agenda Médica</h1>

                    <div class="btn-toolbar mb-2 mb-md-0">
                        <button class="btn btn-outline-success btn-sm">
                            Nueva Cita
                        </button>
                    </div>
                </div>

                <div id="calendar"></div>

            </main>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js'></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var calendarEl = document.getElementById('calendar');

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                locale: 'es',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },

                events: [
                    {
                        title: 'Consulta - Juan Pérez',
                        start: '2024-01-15T10:00:00',
                        end: '2024-01-15T11:00:00'
                    },
                    {
                        title: 'Consulta - María García',
                        start: '2024-01-15T11:00:00',
                        end: '2024-01-15T12:00:00'
                    }
                ]
            });

            calendar.render();
        });
    </script>

</body>
</html>
