<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function send_reset_email($recipient_email, $reset_link) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true;
        $mail->Username   = 'hildanekevin16@gmail.com'; 
        $mail->Password   = 'tguv rmch rvci asuk'; 
        $mail->SMTPSecure = 'SSL';
        $mail->Port       = 587;
        
        $mail->setFrom('kevinstore@gmail.com', 'kevinscollection');
        $mail->addAddress($recipient_email);

        $mail->isHTML(true);
        $mail->Subject = 'Reset Password Akun Anda';
        $mail->Body = <<<HTML
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            background-color: #f3f4f6; 
        }
        .container {
            width: 100%;
            max-width: 600px;
            margin: 0 auto;
        }
        .content {
            background-color: #ffffff; /* bg-white */
            padding: 40px;
            border-radius: 8px; /* rounded-lg */
        }
        .button {
            display: inline-block;
            padding: 12px 24px;
            font-size: 16px;
            font-weight: 600;
            color: #ffffff !important;
            background-color: #4f46e5; /* bg-indigo-600 */
            border-radius: 6px; /* rounded-md */
            text-decoration: none;
        }
        @media screen and (max-width: 600px) {
            .content {
                padding: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; width: 100% !important; background-color: #f3f4f6;">
    <table width="100%" border="0" cellpadding="0" cellspacing="0" style="background-color: #f3f4f6;">
        <tr>
            <td align="center" style="padding: 40px 0;">
                <table border="0" cellpadding="0" cellspacing="0" class="container" style="max-width: 600px; margin: 0 auto;">
                    <tr>
                        <td class="content" style="background-color: #ffffff; padding: 40px; border-radius: 8px;">
                            <h1 style="font-family: Arial, sans-serif; font-size: 24px; font-weight: bold; color: #111827; margin: 0 0 20px;">Permintaan Reset Password</h1>
                            <p style="font-family: Arial, sans-serif; font-size: 16px; color: #374151; line-height: 1.5; margin: 0 0 20px;">
                                Halo,
                            </p>
                            <p style="font-family: Arial, sans-serif; font-size: 16px; color: #374151; line-height: 1.5; margin: 0 0 30px;">
                                Kami menerima permintaan untuk mereset password akun Anda. Untuk melanjutkan, silakan klik tombol di bawah ini.
                            </p>
                            <table border="0" cellpadding="0" cellspacing="0" width="100%">
                                <tr>
                                    <td align="center">
                                        <a href="{$reset_link}" target="_blank" class="button" style="display: inline-block; padding: 12px 24px; font-family: Arial, sans-serif; font-size: 16px; font-weight: 600; color: #ffffff; background-color: #4f46e5; border-radius: 6px; text-decoration: none;">
                                            Reset Password Saya
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <p style="font-family: Arial, sans-serif; font-size: 16px; color: #374151; line-height: 1.5; margin: 30px 0 20px;">
                                Link ini akan kedaluwarsa dalam 1 jam. Jika Anda tidak merasa melakukan permintaan ini, Anda bisa mengabaikan email ini dengan aman.
                            </p>
                            <p style="font-family: Arial, sans-serif; font-size: 14px; color: #6b7280; line-height: 1.5; margin: 0;">
                                Best Regards,<br>
                                Kevin's Collection
                            </p>
                        </td>
                    </tr>
                </table>
                </td>
        </tr>
    </table>
</body>
</html>
HTML;

        $mail->AltBody = 'Untuk mereset password, silakan salin dan tempel link berikut di browser Anda: ' . $reset_link;

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Bisa ditambahkan logging error di sini
        return false;
    }
}

?>