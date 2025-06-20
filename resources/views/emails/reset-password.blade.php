<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Recuperación de Contraseña - Ferrechincha</title>
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
    </style>
</head>
<body>
    <div class="header">
        <h1>Ferrechincha</h1>
    </div>
    
    <div class="content">
        <h2>Recuperación de Contraseña</h2>
        
        <p>Hola,</p>
        
        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta en Ferrechincha. Si no realizaste esta solicitud, puedes ignorar este correo.</p>
        
        <p>Para restablecer tu contraseña, haz clic en el siguiente botón:</p>
        
        <center>
            <a href="{{ $resetLink }}" class="button">Restablecer Contraseña</a>
        </center>
        
        <p>Este enlace expirará en 60 minutos.</p>
        
        <p>Si tienes problemas para hacer clic en el botón, copia y pega el siguiente enlace en tu navegador:</p>
        
        <p style="word-break: break-all;">{{ $resetLink }}</p>
        
        <p>Saludos,<br>Equipo de Ferrechincha</p>
    </div>
    
    <div class="footer">
        <p>© {{ date('Y') }} Ferrechincha. Todos los derechos reservados.</p>
    </div>
</body>
</html> 