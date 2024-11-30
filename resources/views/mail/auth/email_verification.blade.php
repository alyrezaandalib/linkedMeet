<x-mail::message>
# Email Verification Code

Hello,

Thank you for registering with us! Please use the verification code below to confirm your email address:

**Verification Code:** `{{ $user->verification_code }}`

If you did not request this code, please ignore this message.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
