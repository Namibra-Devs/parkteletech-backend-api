@component('mail::message')
Hello Employee,

{{ $body }}

{{-- @component('mail::button', ['url' => "sammple url"])
View Order
@endcomponent --}}

Regards,<br>
{{ config('app.name') }}
@endcomponent
