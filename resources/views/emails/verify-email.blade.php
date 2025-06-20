@component('mail::message')
# ¡Hola!

Por favor, haz clic en el botón de abajo para verificar tu dirección de correo electrónico.

@component('mail::button', ['url' => $url, 'color' => 'primary'])
Verificar correo electrónico
@endcomponent

Si no creaste una cuenta, no es necesario realizar ninguna acción.

¡Saludos!,<br>
{{ config('app.name') }}
@endcomponent 