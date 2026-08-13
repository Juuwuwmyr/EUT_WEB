<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify your email</title>
</head>
<body style="margin:0;padding:0;background:#f3f4f6;font-family:Arial,Helvetica,sans-serif;">
<table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background:#f3f4f6;padding:32px 16px;">
    <tr>
        <td align="center">
            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:520px;background:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.08);">
                <tr>
                    <td style="background:#0a0a0a;padding:28px 32px;text-align:center;">
                        <div style="font-size:22px;font-weight:700;color:#ffffff;letter-spacing:.5px;">E.U.T Snack House</div>
                        <div style="font-size:12px;color:#9ca3af;margin-top:6px;letter-spacing:1px;text-transform:uppercase;">Email Verification</div>
                    </td>
                </tr>
                <tr>
                    <td style="padding:32px;">
                        <p style="margin:0 0 12px;font-size:16px;color:#111827;">Hi {{ $name }},</p>
                        <p style="margin:0 0 24px;font-size:15px;line-height:1.6;color:#4b5563;">
                            Use the verification code below to complete your signup and activate your account.
                        </p>
                        <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                            <tr>
                                <td align="center" style="background:#fffbeb;border:1px solid #fde68a;border-radius:12px;padding:24px 16px;">
                                    <div style="font-size:12px;color:#92400e;text-transform:uppercase;letter-spacing:1.5px;font-weight:700;margin-bottom:10px;">Your code</div>
                                    <div style="font-size:36px;font-weight:700;letter-spacing:10px;color:#111827;">{{ $code }}</div>
                                </td>
                            </tr>
                        </table>
                        <p style="margin:24px 0 0;font-size:13px;line-height:1.6;color:#6b7280;">
                            This code expires in <strong style="color:#374151;">15 minutes</strong>.
                            Enter it on the verification page to finish creating your account.
                        </p>
                        <p style="margin:16px 0 0;font-size:12px;line-height:1.6;color:#9ca3af;">
                            If you did not request this, you can safely ignore this email.
                        </p>
                    </td>
                </tr>
                <tr>
                    <td style="padding:20px 32px;background:#f9fafb;border-top:1px solid #e5e7eb;text-align:center;">
                        <p style="margin:0;font-size:11px;color:#9ca3af;">&copy; {{ date('Y') }} E.U.T Snack House. All rights reserved.</p>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
