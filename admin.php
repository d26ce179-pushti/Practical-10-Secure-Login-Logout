
<?php
require_once "auth.php";
require_role('admin');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <p>Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</p>
    <p>Your role: Admin</p>
    <p>You are logged in successfully.</p>
    <a href="logout.php">Logout</a>
</body>
</html>
