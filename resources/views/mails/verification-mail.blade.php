@component('mail::message')
# {{$user->first_name}}, sua conta foi registrada com sucesso!

{{$user->first_name}}, você criou uma conta no site {{config("app.name")}} no dia {{date("d/m/Y")}} às {{date("H:i:s")}}! Agora precisamos apenas que você confirme a criação da sua conta clicando no link abaixo.

@component('mail::button', ['url' => $verificationLink])
Confirmar criação de conta
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
