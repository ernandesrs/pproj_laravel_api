@component('mail::message')
# Olá {{$user->first_name}}!

Recebemos a sua solicitação de um link de recuperação de senha. Clique no botão abaixo para redefinir sua senha.

@component('mail::button', ['url' => $resetPasswordLink])
Recuperar senha
@endcomponent

Obrigado,<br>
{{ config('app.name') }}
@endcomponent
