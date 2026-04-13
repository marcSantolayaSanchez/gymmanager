<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GymManager – Login</title>
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@700;900&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            min-height: 100vh; background: #09090F;
            display: flex; align-items: center; justify-content: center;
            font-family: 'DM Sans', sans-serif; color: #E0E0F0;
        }
        .login-wrap {
            width: 100%; max-width: 420px; padding: 24px;
        }
        .logo {
            text-align: center; margin-bottom: 40px;
        }
        .logo-icon {
            width: 56px; height: 56px; background: #C8FF00;
            border-radius: 16px; display: flex; align-items: center;
            justify-content: center; font-size: 26px; margin: 0 auto 14px;
        }
        .logo h1 { font-family: 'Syne', sans-serif; font-size: 26px; font-weight: 900; letter-spacing: -.03em; }
        .logo p  { font-size: 13px; color: #52527A; margin-top: 4px; }

        .card {
            background: #14141E; border: 1px solid #1C1C2E;
            border-radius: 20px; padding: 32px;
        }
        .form-group { margin-bottom: 18px; }
        label {
            display: block; font-size: 12px; font-weight: 600;
            color: #8888AA; text-transform: uppercase; letter-spacing: .06em; margin-bottom: 6px;
        }
        input {
            width: 100%; background: #09090F; border: 1px solid #1C1C2E;
            border-radius: 10px; padding: 12px 14px; color: #E0E0F0;
            font-size: 15px; outline: none; transition: border-color .15s;
            font-family: 'DM Sans', sans-serif;
        }
        input:focus { border-color: #C8FF00; }
        input::placeholder { color: #52527A; }
        .btn-submit {
            width: 100%; padding: 13px;
            background: #C8FF00; color: #000;
            border: none; border-radius: 12px;
            font-family: 'Syne', sans-serif; font-weight: 900;
            font-size: 15px; cursor: pointer; margin-top: 8px;
            transition: opacity .15s; letter-spacing: -.01em;
        }
        .btn-submit:hover { opacity: .85; }
        .error {
            background: #FF3B5C18; border: 1px solid #FF3B5C33;
            border-radius: 10px; padding: 12px 14px;
            color: #FF3B5C; font-size: 13px; margin-bottom: 18px;
        }
        .hint {
            margin-top: 20px; padding: 14px; background: #09090F;
            border-radius: 10px; font-size: 12px; color: #52527A;
        }
        .hint strong { color: #8888AA; display: block; margin-bottom: 6px; }
        .hint code { color: #C8FF00; font-size: 11px; }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="logo">
            <div class="logo-icon">🏋️</div>
            <h1>GymManager</h1>
            <p>Panel de Administración</p>
        </div>
        <div class="card">
            @if($errors->any())
                <div class="error">{{ $errors->first() }}</div>
            @endif
            <form method="POST" action="{{ route('login') }}">
                @csrf
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@gymmanager.com" required autofocus>
                </div>
                <div class="form-group">
                    <label>Contraseña</label>
                    <input type="password" name="password" placeholder="••••••••" required>
                </div>
                <button type="submit" class="btn-submit">Entrar →</button>
            </form>
            <div class="hint">
                <strong>Cuentas de prueba:</strong>
                Admin: <code>admin@gymmanager.com</code> / <code>password</code><br>
                Entrenador: <code>carlos@gymmanager.com</code> / <code>password</code><br>
                Cliente: <code>sara@example.com</code> / <code>password</code>
            </div>
        </div>
    </div>
</body>
</html>
