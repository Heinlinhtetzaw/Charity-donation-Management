<?php
// Secure session settings
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_strict_mode', 1);

// If using HTTPS, enable this:
# ini_set('session.cookie_secure', 1);

session_start();

// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dmssystem');

// Admin signup invite code
// Leave empty to allow first admin creation, then generate/store a one-time code.
define('ADMIN_INVITE_CODE', '');

function get_admin_invite_record() {
    $code = trim(ADMIN_INVITE_CODE);
    if ($code !== '') {
        return ['code' => $code, 'used' => false];
    }
    $path = __DIR__ . '/data/admin_invite_code.json';
    if (is_readable($path)) {
        $data = json_decode(file_get_contents($path), true);
        if (is_array($data)) {
            return [
                'code' => isset($data['code']) ? (string) $data['code'] : '',
                'used' => !empty($data['used']),
            ];
        }
    }
    return ['code' => '', 'used' => false];
}

function generate_admin_invite_code() {
    return bin2hex(random_bytes(4));
}

function store_admin_invite_record($code, $used = false) {
    $dir = __DIR__ . '/data';
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
    $payload = json_encode(['code' => $code, 'used' => (bool) $used], JSON_PRETTY_PRINT);
    file_put_contents($dir . '/admin_invite_code.json', $payload);
}

// Create connection function
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Database connection failed.");
    }
    return $conn;
}
?> 
