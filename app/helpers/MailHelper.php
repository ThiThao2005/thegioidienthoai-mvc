<?php

class MailHelper
{
    // API key Resend đọc từ biến môi trường RESEND_API_KEY hoặc app/config/db.local.php ('resend_api_key')
    private static function apiKey()
    {
        if (getenv('RESEND_API_KEY')) {
            return getenv('RESEND_API_KEY');
        }
        if (!class_exists('Database')) {
            require_once __DIR__ . '/../config/Database.php';
        }
        return Database::localConfig()['resend_api_key'] ?? '';
    }
    private static $fromEmail = 'onboarding@resend.dev';
    private static $fromName = 'The Gioi Dien Thoai';

    public static function sendVerifyEmail($email, $fullname, $verifyLink)
    {
        $email = trim($email);
        $fullname = trim($fullname);

        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }

        $html = '
            <div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:20px;border:1px solid #eee;border-radius:10px;">
                <h2 style="color:#222;">Xin chào ' . htmlspecialchars($fullname, ENT_QUOTES, 'UTF-8') . '</h2>

                <p>Cảm ơn bạn đã đăng ký tài khoản tại <strong>Thế Giới Điện Thoại</strong>.</p>

                <p>Vui lòng bấm vào nút bên dưới để xác thực tài khoản:</p>

                <p style="margin:25px 0;">
                    <a href="' . htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8') . '"
                       style="background:#ffd400;color:#000;padding:12px 22px;border-radius:8px;text-decoration:none;font-weight:bold;display:inline-block;">
                        Xác thực tài khoản
                    </a>
                </p>

                <p>Nếu nút không hoạt động, hãy copy link này:</p>

                <p style="word-break:break-all;color:#555;">
                    ' . htmlspecialchars($verifyLink, ENT_QUOTES, 'UTF-8') . '
                </p>

                <hr style="border:none;border-top:1px solid #eee;margin:25px 0;">

                <p style="font-size:13px;color:#888;">
                    Nếu bạn không đăng ký tài khoản, vui lòng bỏ qua email này.
                </p>
            </div>
        ';

        return self::send($email, 'Xác thực tài khoản Thế Giới Điện Thoại', $html);
    }

    public static function sendResetPasswordEmail($email, $fullname, $resetLink)
    {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return false;
        }
        $name = htmlspecialchars((string) $fullname, ENT_QUOTES, 'UTF-8');
        $link = htmlspecialchars($resetLink, ENT_QUOTES, 'UTF-8');
        $html = '
            <div style="font-family:Arial,sans-serif;max-width:600px;margin:auto;padding:24px;border:1px solid #e8e4dc;border-radius:14px;color:#1f3a3d;">
                <h2 style="margin-top:0;">Xin chào ' . $name . '</h2>
                <p>Bạn (hoặc ai đó) vừa yêu cầu đặt lại mật khẩu tại <strong>Thế Giới Điện Thoại</strong>.</p>
                <p style="margin:25px 0;">
                    <a href="' . $link . '" style="background:#2f7d6d;color:#fff;padding:12px 22px;border-radius:10px;text-decoration:none;font-weight:bold;display:inline-block;">Đặt lại mật khẩu</a>
                </p>
                <p>Link có hiệu lực trong 30 phút. Nếu bạn không yêu cầu, hãy bỏ qua email này.</p>
                <p style="word-break:break-all;color:#6b7c80;font-size:13px;">' . $link . '</p>
            </div>';
        return self::send($email, 'Đặt lại mật khẩu - Thế Giới Điện Thoại', $html);
    }

    private static function send($email, $subject, $html)
    {
        if (self::apiKey() === '') {
            error_log('Resend API key chưa được cấu hình.');
            return false;
        }

        $data = [
            'from' => self::$fromName . ' <' . self::$fromEmail . '>',
            'to' => [$email],
            'subject' => $subject,
            'html' => $html
        ];

        $ch = curl_init('https://api.resend.com/emails');

        curl_setopt_array($ch, [
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . self::apiKey(),
                'Content-Type: application/json'
            ],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_SSL_VERIFYPEER => true
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);

        curl_close($ch);

        if (!empty($curlError)) {
            error_log('Resend CURL Error: ' . $curlError);
            return false;
        }

        if ($httpCode >= 200 && $httpCode < 300) {
            return true;
        }

        error_log('Resend API Error HTTP ' . $httpCode . ': ' . $response);
        return false;
    }
}