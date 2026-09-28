<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <style>
        body { font-family: sans-serif; margin: 40px; }
        .error { color: #dc2626; font-size: 13px; }
        .success { color: #16a34a; font-weight: bold; }
    </style>
</head>
<body>
    <h2>Formulario de Registro</h2>

    @if(session('success'))
        <p class="success">{{ session('success') }}</p>
    @endif

    <form action="{{ route('register.store') }}" method="POST">
        @csrf
        <div>
            <label>Nombre Completo:</label><br>
            <input type="text" name="name" value="{{ old('name') }}">
            @error('name') <br><span class="error">{{ $message }}</span> @enderror
        </div><br>

        <div>
            <label>Correo Electrónico:</label><br>
            <input type="email" name="email" value="{{ old('email') }}">
            @error('email') <br><span class="error">{{ $message }}</span> @enderror
        </div><br>

        <div>
            <label>Contraseña:</label><br>
            <input type="password" name="password">
            @error('password') <br><span class="error">{{ $message }}</span> @enderror
        </div><br>

        <div>
            <label>Confirmar Contraseña:</label><br>
            <input type="password" name="password_confirmation">
        </div><br>

        <button type="submit">Registrarse</button>
    </form>
</body>
</html>