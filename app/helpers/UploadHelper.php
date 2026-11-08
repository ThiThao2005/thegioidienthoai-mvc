<?php
class UploadHelper
{
    /**
     * Trả về đuôi file an toàn dựa trên NỘI DUNG ảnh thật (jpg/png/webp/gif),
     * hoặc null nếu không phải ảnh. Không bao giờ dùng đuôi file người dùng gửi lên,
     * để tránh upload file .php giả dạng ảnh.
     */
    public static function imageExtension($tmpPath)
    {
        if (!is_string($tmpPath) || !is_file($tmpPath) || @getimagesize($tmpPath) === false) {
            return null;
        }
        $map = [
            'image/jpeg' => 'jpg',
            'image/png'  => 'png',
            'image/webp' => 'webp',
            'image/gif'  => 'gif',
        ];
        return $map[mime_content_type($tmpPath)] ?? null;
    }
}
