
<?php
require_once "auth.php";
require_role('student');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard</title>
</head>
<body>
    <h1>Student Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    <p>Your role: Student</p>
    <p>You are logged in successfully.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
