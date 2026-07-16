<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <title>User Created</title>
</head>

<body>
    <h2>Hello {{ $user->name }},</h2>

    @if ($isUpdate)
        <p>Your account has been updated successfully.</p>
    @else
        <p>Your account has been created successfully.</p>
    @endif

    <p><strong>Email:</strong> {{ $user->email }}</p>

    @if ($plainPassword)
        <p><strong>Password:</strong> {{ $plainPassword }}</p>
    @endif

    @if ($plainPassword)
        <p>Please visit <a href="{{ config('app.url') }}"> {{ config('app.name') }} </a> login and change your password.
        </p>
    @endif

    <br>
    <p>Thanks,<br> UNI Business Solutions</p>
</body>

</html>
