<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
</head>

<body>

    <p>Hello,</p>

    <p>
        Please find attached the
        <strong>
            {{ ucwords(str_replace('_', ' ', $type)) }}
        </strong>
        sales report.
    </p> 

    <p>
        If you have any questions, please contact the administrator.
    </p>

    <br>
    
    <p>
        Regards,<br>
        UNI Business Solutions | Tools
    </p>

</body>

</html>
