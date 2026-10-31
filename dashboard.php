
<?php
require_once "auth.php";
require_login();

if ($_SESSION['role'] === 'admin') {
    header("Location: admin.php");
    exit;
}

if ($_SESSION['role'] === 'student') {
    header("Location: student.php");
    exit;
}

http_response_code(403);
exit("Invalid user role.");
?>
