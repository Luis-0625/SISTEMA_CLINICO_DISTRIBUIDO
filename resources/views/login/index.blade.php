@extends('layouts.app')

@section('title', 'Sistema de Historia Clínica Electrónica | HCE')

@section('content')

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Sistema de Historia Clínica Electrónica | HCE</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">

        <style>
            /* --- Tus estilos siguen igual, no fueron modificados --- */
            :root {
                --primary: #0cbaba;
                --secondary: #2c698d;
                --accent: #00a8cc;
                --light: #f5f9fc;
                --white: #ffffff;
                --gray: #777;
                --danger: #e63946;
            }

            * {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
                font-family: "Poppins", sans-serif;
            }

            body {
                background: linear-gradient(135deg, var(--primary), var(--secondary));
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                overflow: hidden;
            }

            .container {
                display: flex;
                background: rgba(255, 255, 255, 0.15);
                box-shadow: 0 8px 32px rgba(31, 38, 135, 0.37);
                backdrop-filter: blur(12px);
                border-radius: 20px;
                overflow: hidden;
                width: 900px;
                max-width: 95%;
                color: var(--white);
                border: 1px solid rgba(255, 255, 255, 0.18);
            }

            .left-panel {
                flex: 1;
                background: linear-gradient(160deg, var(--secondary), var(--primary));
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                padding: 40px;
                color: #fff;
            }

            .left-panel img {
                width: 80%;
                max-width: 350px;
                margin-bottom: 30px;
            }

            .right-panel {
                flex: 1;
                background: rgba(255, 255, 255, 0.9);
                padding: 50px;
                display: flex;
                flex-direction: column;
                justify-content: center;
                color: var(--secondary);
            }

            .right-panel h3 {
                font-size: 1.8rem;
                font-weight: 600;
                margin-bottom: 10px;
            }

            .error {
                display: none;
                background: #ffe5e5;
                color: var(--danger);
                border-radius: 8px;
                padding: 10px;
                text-align: center;
                margin-bottom: 15px;
                font-size: 0.9rem;
            }

            .form-group {
                margin-bottom: 20px;
                position: relative;
            }

            input,
            select {
                width: 100%;
                padding: 12px 45px 12px 12px;
                border: 1px solid #ccc;
                border-radius: 10px;
                font-size: 15px;
                color: var(--secondary);
                outline: none;
            }

            label {
                font-weight: 600;
                margin-bottom: 5px;
                display: block;
            }

            .toggle-password {
                position: absolute;
                right: 12px;
                top: 38px;
                cursor: pointer;
                color: var(--gray);
                font-size: 1.1rem;
            }

            .login-btn {
                margin-top: 25px;
                background: var(--primary);
                border: none;
                border-radius: 10px;
                padding: 14px;
                width: 100%;
                color: var(--white);
                font-size: 1rem;
                font-weight: 600;
                cursor: pointer;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            @media (max-width: 768px) {
                .container {
                    flex-direction: column;
                }

                .left-panel {
                    display: none;
                }
            }
        </style>
    </head>

    <div class="container">
        <div class="left-panel">
            <img src="https://cdn-icons-png.flaticon.com/512/2966/2966483.png" alt="Historia clínica electrónica">
            <h2>Sistema de Historia Clínica Electrónica</h2>
            <p>Gestiona la información clínica, citas y registros médicos de forma segura.</p>
        </div>

        <div class="right-panel">
            <h3>Acceso al Sistema</h3>
            <p>Introduce tus credenciales para continuar</p>

            <div class="error" id="errorMessage">Documento, contraseña o rol incorrectos.</div>

            <form method="POST" action="{{ route('login.store') }}">
                @csrf

                <div class="form-group">
                    <label for="documento_id">Número de Documento</label>
                    <input type="text" id="documento_id" name="documento_id" placeholder="Ej: 1045678901" required>
                </div>

                <div class="form-group">
                    <label for="contrasena">Contraseña</label>
                    <input type="password" id="contrasena" name="contrasena" placeholder="••••••••" required>
                    <span class="toggle-password" id="togglePassword">👁️</span>
                </div>

                <div class="form-group">
                    <label for="rol_id">Seleccionar Rol</label>
                    <select name="rol_id" id="rol_id" required>
                        <option value="" disabled selected>Seleccione una opción</option>
                        <option value="paciente">Paciente</option>
                        <option value="medico">Medico</option>
                        <option value="admisionista">Admisionista</option>
                    </select>
                </div>

                <button type="submit" class="login-btn">
                    <span>Ingresar</span>
                </button>
            </form>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        const password = document.getElementById('contrasena');
        const togglePassword = document.getElementById('togglePassword');

        togglePassword.addEventListener('click', () => {
            password.type = password.type === 'password' ? 'text' : 'password';
            togglePassword.textContent = password.type === 'password' ? '👁️' : '🙈';
        });
    </script>
@endsection
