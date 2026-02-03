<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Astar OTP</title>
</head>
<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0">
<tr>
    <td style="background:#1e293b; padding:20px; text-align:center;">
        <h1 style="color:#ffffff; margin:0;">Astar</h1>
    </td>
<tr>
    <td style="padding:30px; color:#333;">
        <h2>Hello 👋</h2>
        <p>Use the following OTP code to complete your action:</p>
        <div style="
            text-align:center;
            font-size:32px;
            font-weight:bold;
            letter-spacing:6px;
            margin:30px 0;
            color:#1e293b;
        ">
            {{ $otp }}
        </div>
        <p>This code will expire in <strong>5 minutes</strong>.</p>
        <p style="color:#777; font-size:14px;">
            If you didn’t request this, please ignore this email.
        </p>
    </td>
</tr>
<tr>
    <td style="background:#1e293b; padding:20px; text-align:center; color:#ffffff; font-size:14px;">
        &copy; {{ date('Y') }} Astar. All rights reserved.
    </td>
</table>

</body>
</html>
