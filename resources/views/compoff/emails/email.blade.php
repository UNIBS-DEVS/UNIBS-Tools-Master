<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>User Created</title>
</head>
<body>
    <h2>Hello {{ $user->name }},</h2>

    <p>Your account has been created successfully.</p>

    <p><strong>Email:</strong> {{ $user->email }}</p>
    <p><strong>Password:</strong> {{ $plainPassword }}</p>

    <p>Please login and change your password.</p>

    <br>
    <p>Thanks,<br>Your Company</p>
</body>
</html>