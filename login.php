
<?php
require_once "auth.php";
require_once "db.php";

if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

$message = "";

if (isset($_GET['timeout'])) {
    $message = "Your session expired due to inactivity. Please log in again.";
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email'] ?? "");
    $password = $_POST['password'] ?? "";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === "") {
        $message = "Enter a valid email and password.";
    } else {
        $stmt = $conn->prepare(
            "SELECT id, username, email, password, role
             FROM users WHERE email = ?"
        );

        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result(
                $id,
                $username,
                $dbEmail,
                $passwordHash,
                $role
            );
            $stmt->fetch();

            if (password_verify($password, $passwordHash)) {
                // Prevent session fixation.
                session_regenerate_id(true);

                $_SESSION['user_id'] = $id;
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $dbEmail;
                $_SESSION['role'] = $role;
                $_SESSION['last_activity'] = time();

                $update = $conn->prepare(
                    "UPDATE users SET last_login = NOW() WHERE id = ?"
                );
                $update->bind_param("i", $id);
                $update->execute();
                $update->close();

                $stmt->close();
                $conn->close();

                header("Location: dashboard.php");
                exit;
            }
        }

        $stmt->close();
        $message = "Invalid email or password.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Secure Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f1f5f9;
            padding: 30px 15px;
        }
        .box {
            max-width: 400px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 10px;
        }
        label {
            display: block;
            margin-top: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            margin-top: 20px;
            background: #2563eb;
            color: white;
            border: 0;
            cursor: pointer;
        }
        .message {
            color: #991b1b;
            background: #fee2e2;
            padding: 10px;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>Secure Login</h2>

    <?php if ($message !== ""): ?>
        <p class="message">
            <?= htmlspecialchars($message) ?>
        </p>
    <?php endif; ?>

    <form method="POST">
        <label for="email">Email</label>
        <input
            type="email"
            id="email"
            name="email"
            maxlength="100"
            autocomplete="username"
            required
        >

        <label for="password">Password</label>
        <input
            type="password"
            id="password"
            name="password"
            autocomplete="current-password"
            required
        >

        <button type="submit">Login</button>
    </form>
</div>
</body>
</html>
