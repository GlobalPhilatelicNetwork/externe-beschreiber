<!DOCTYPE html>
<html>
<body style="font-family: sans-serif; line-height: 1.6;">
    <h2>{{ __('messages.credentials_mail_greeting', ['name' => $user->name]) }}</h2>
    <p>{{ __('messages.credentials_mail_intro') }}</p>
    <table style="border-collapse: collapse; margin: 16px 0;">
        <tr>
            <td style="padding: 8px; font-weight: bold;">{{ __('messages.email') }}:</td>
            <td style="padding: 8px;">{{ $user->email }}</td>
        </tr>
        <tr>
            <td style="padding: 8px; font-weight: bold;">{{ __('auth.password') }}:</td>
            <td style="padding: 8px; font-family: monospace;">{{ $plainPassword }}</td>
        </tr>
    </table>
    <p>
        <a href="{{ $loginUrl }}" style="background: #4f46e5; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">
            {{ __('auth.login') }}
        </a>
    </p>
    <p style="color: #666; font-size: 14px; margin-top: 24px;">{{ __('messages.credentials_mail_footer') }}</p>
</body>
</html>
