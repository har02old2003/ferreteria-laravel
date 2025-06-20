@component('mail::layout')
{{-- Header --}}
@slot('header')
@component('mail::header', ['url' => config('app.url')])
{{ config('app.name') }}
@endcomponent
@endslot

{{-- Body --}}
<div style="text-align: center; margin-bottom: 30px;">
    <h1 style="color: #2563eb; font-size: 24px; margin-bottom: 20px;">¡Bienvenido a Ferrechincha!</h1>
    <p style="font-size: 16px; color: #4b5563; margin-bottom: 30px;">
        Gracias por registrarte. Para comenzar a usar tu cuenta, por favor verifica tu dirección de correo electrónico.
    </p>
</div>

{{ $slot }}

{{-- Subcopy --}}
@isset($subcopy)
@slot('subcopy')
@component('mail::subcopy')
{{ $subcopy }}
@endcomponent
@endslot
@endisset

{{-- Footer --}}
@slot('footer')
@component('mail::footer')
© {{ date('Y') }} {{ config('app.name') }}. Todos los derechos reservados.
@endcomponent
@endslot
@endcomponent
