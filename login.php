<?php
require_once 'config.php';
require_once 'csrf.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: adlogin.php");
    exit();
}

if (!isset($_POST['csrf_token']) || !validate_csrf_token($_POST['csrf_token'])) {
    $_SESSION['error'] = "Invalid request.";
    header("Location: adlogin.php");
    exit();
}

$username = isset($_POST['username']) ? trim($_POST['username']) : '';
$password = $_POST['password'] ?? '';

if ($username === '' || $password === '') {
    $_SESSION['error'] = "Username and password are required.";
    header("Location: adlogin.php");
    exit();
}

$conn = getDBConnection();
$maxAttempts = 3;
$lockoutSeconds = 300;
$stmt = $conn->prepare("SELECT adname, adpassword, failed_attempts, last_failed_login FROM admin WHERE adname = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $admin = $result->fetch_assoc();
    $lastFailedLogin = !empty($admin['last_failed_login']) ? strtotime($admin['last_failed_login']) : 0;
    $failedAttempts = (int) $admin['failed_attempts'];

    if ($failedAttempts >= $maxAttempts) {
        if ($lastFailedLogin === 0) {
            $stamp = $conn->prepare("UPDATE admin SET last_failed_login = NOW() WHERE adname = ?");
            $stamp->bind_param("s", $admin['adname']);
            $stamp->execute();
            $_SESSION['error'] = "Account locked. Try again later.";
            header("Location: adlogin.php");
            exit();
        }
        $elapsed = time() - $lastFailedLogin;
        if ($elapsed < $lockoutSeconds) {
            $remaining = $lockoutSeconds - $elapsed;
            if ($remaining < 0) {
                $remaining = 0;
            } elseif ($remaining > $lockoutSeconds) {
                $remaining = $lockoutSeconds;
            }
            $remainingMinutes = (int) floor($remaining / 60);
            $remainingSeconds = (int) ($remaining % 60);
            $_SESSION['lockout_remaining'] = $remaining;
            $_SESSION['error'] = sprintf(
                "Account locked. Try again in %d min %02d sec.",
                $remainingMinutes,
                $remainingSeconds
            );
            header("Location: adlogin.php");
            exit();
        }

        $unlock = $conn->prepare("UPDATE admin SET failed_attempts = 0, last_failed_login = NULL WHERE adname = ?");
        $unlock->bind_param("s", $admin['adname']);
        $unlock->execute();
        $failedAttempts = 0;
    }

    $storedPassword = (string) $admin['adpassword'];
    $isHashedPassword = password_get_info($storedPassword)['algo'] !== null;
    $isValidPassword = $isHashedPassword && password_verify($password, $storedPassword);

    if ($isValidPassword) {
        session_regenerate_id(true);
        $_SESSION['admin_username'] = $admin['adname'];
        $_SESSION['LAST_ACTIVITY'] = time();

        $reset = $conn->prepare("UPDATE admin SET failed_attempts = 0, last_failed_login = NULL WHERE adname = ?");
        $reset->bind_param("s", $admin['adname']);
        $reset->execute();

        if (password_needs_rehash($storedPassword, PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $rehash = $conn->prepare("UPDATE admin SET adpassword = ? WHERE adname = ?");
            $rehash->bind_param("ss", $newHash, $admin['adname']);
            $rehash->execute();
        }

        header("Location: addashboard.php");
        exit();
    }

    $failed = $failedAttempts + 1;
    $update = $conn->prepare("UPDATE admin SET failed_attempts = ?, last_failed_login = NOW() WHERE adname = ?");
    $update->bind_param("is", $failed, $admin['adname']);
    $update->execute();

    if ($failed >= $maxAttempts) {
        $remainingMinutes = (int) floor($lockoutSeconds / 60);
        $remainingSeconds = (int) ($lockoutSeconds % 60);
        $_SESSION['lockout_remaining'] = $lockoutSeconds;
        $_SESSION['error'] = sprintf(
            "Account locked. Try again in %d min %02d sec.",
            $remainingMinutes,
            $remainingSeconds
        );
        header("Location: adlogin.php");
        exit();
    }

    $_SESSION['error'] = "Invalid username or password.";
    header("Location: adlogin.php");
    exit();
}

$_SESSION['error'] = "Invalid username or password.";
header("Location: adlogin.php");
exit();
?>
