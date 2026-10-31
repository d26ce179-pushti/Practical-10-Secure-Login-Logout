
<?php
// Session cookie security settings must be set before session_start().
ini_set('session.use_strict_mode', '1');

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false, // Local XAMPP HTTP only; use true with HTTPS.
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$timeout = 300; // 5 minutes

if (isset($_SESSION['user_id'])) {
    if (
        isset($_SESSION['last_activity']) &&
        time() - $_SESSION['last_activity'] >= $timeout
    ) {
        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();

            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params['path'],
                $params['domain'] ?? '',
                $params['secure'],
                $params['httponly']
            );
        }

        session_destroy();
        header("Location: login.php?timeout=1");
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function require_login(): void {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }
}

function require_role(string $requiredRole): void {
    require_login();

    if (($_SESSION['role'] ?? '') !== $requiredRole) {
        http_response_code(403);
        exit("403 Forbidden: You are not authorized to access this page.");
    }
}
?>
