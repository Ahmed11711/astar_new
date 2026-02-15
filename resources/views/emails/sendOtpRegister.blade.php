<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Astar Reset Password</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f8; font-family:Arial, Helvetica, sans-serif;">

    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td style="background:#1e293b; padding:20px; text-align:center;">
                <h1 style="color:#ffffff; margin:0;">Astar</h1>
            </td>
        </tr>
        <tr>
            <td style="padding:30px; color:#333;">
                <h2>Hello 👋</h2>
                <p>You requested a verification code. Use the code below to proceed:</p>

                <!-- OTP Code Display -->
                <div style="text-align:center; margin:40px 0;">
                    <span style="
                display:inline-block;
                background-color:#1e293b;
                color:#ffffff;
                padding:15px 30px;
                font-size:20px;
                font-weight:bold;
                border-radius:5px;
                letter-spacing:4px;
            ">
                        {{ $otp }}
                    </span>
                </div>

                <p style="color:#777; font-size:14px;">
                    This code will expire in <strong>60 minutes</strong>.
                    If you didn’t request this, please ignore this email.
                </p>
            </td>
        </tr>
        <tr>
            <td style="background:#1e293b; padding:20px; text-align:center; color:#ffffff; font-size:14px;">
                &copy; {{ date('Y') }} Astar. All rights reserved.
            </td>
        </tr>
    </table>

</body>

</html>