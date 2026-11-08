<?php
// Copy file này thành db.local.php rồi điền thông tin MySQL của InfinityFree
// (lấy ở Control Panel -> MySQL Databases). KHÔNG commit db.local.php lên git.
return [
    'host'     => 'sqlXXX.infinityfree.com', // MySQL Hostname
    'port'     => '3306',
    'name'     => 'if0_XXXXXXXX_my_store',   // MySQL Database Name
    'user'     => 'if0_XXXXXXXX',            // MySQL Username
    'password' => 'MAT_KHAU_VPANEL',         // MySQL Password (mật khẩu tài khoản hosting)

    // API key gửi email xác thực (tạo ở https://resend.com/api-keys)
    'resend_api_key' => 're_XXXXXXXXXXXX',
];
