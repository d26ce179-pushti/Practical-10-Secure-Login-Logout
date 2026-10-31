
<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$conn = new mysqli("localhost", "root", "", "practical10");
$conn->set_charset("utf8mb4");

$users = [
    [
        "Admin User",
        "admin@example.com",
        "Admin@12345",
        "admin"
    ],
    [
        "Student User",
        "student@example.com",
        "Student@12345",
        "student"
    ]
];

$stmt = $conn->prepare(
    "INSERT INTO users (username, email, password, role)
     VALUES (?, ?, ?, ?)
     ON DUPLICATE KEY UPDATE email = VALUES(email)"
);

foreach ($users as $user) {
    [$username, $email, $plainPassword, $role] = $user;
    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    $stmt->bind_param(
        "ssss",
        $username,
        $email,
        $hash,
        $role
    );
    $stmt->execute();
}

$stmt->close();
$conn->close();

echo "Test accounts are ready. Delete setup.php now.";
?>
