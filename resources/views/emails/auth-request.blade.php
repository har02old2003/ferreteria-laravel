<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud de Autorización - Ferrechincha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background-color: #1a365d;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 5px 5px 0 0;
        }
        .content {
            background-color: #f8fafc;
            padding: 20px;
            border: 1px solid #e2e8f0;
            border-radius: 0 0 5px 5px;
        }
        .button {
            display: inline-block;
            background-color: #2563eb;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.875rem;
            color: #64748b;
        }
        .user-info {
            background-color: #f1f5f9;
            padding: 15px;
            border-radius: 5px;
            margin: 15px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Ferrechincha</h1>
    </div>
    
    <div class="content">
        <h2>Solicitud de Autorización de Nueva Cuenta</h2>
        
        <p>Hola Administrador,</p>
        
        <p>Se ha registrado un nuevo usuario en el sistema y requiere autorización para acceder.</p>
        
        <div class="user-info">
            <h3>Información del Usuario:</h3>
            <p><strong>Nombre:</strong> {{ $user->name }}</p>
            <p><strong>Correo:</strong> {{ $user->email }}</p>
            <p><strong>Fecha de registro:</strong> {{ $user->created_at->format('d/m/Y H:i') }}</p>
        </div>
        
        <p>Para autorizar o rechazar esta solicitud, por favor ingresa al panel de administración:</p>
        
        <center>
            <a href="{{ $adminUrl }}" class="button">Ir al Panel de Administración</a>
        </center>
        
        <p>Si no reconoces esta actividad, por favor ignora este correo.</p>
        
        <p>Saludos,<br>Sistema de Ferrechincha</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Ferrechincha. Todos los derechos reservados.</p>
    </div>
</body>
</html> 