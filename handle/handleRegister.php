<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';
require_once '../inc/auth.php'; 

use Firebase\JWT\JWT;

<<<<<<< HEAD
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}
=======
>>>>>>> de80e1728c3d28b4ea6dc5d6b5f84a8dd7719250
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$password = htmlspecialchars(trim($_POST['password'] ?? ''));

if (empty($name)) {
    msg("Name is required",400);
}
if (empty($email)) {
    msg("Email is required",400);
}
elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("Invalid email format",400);
}
if (empty($password)) {
    msg("Please enter your password", 400);
} elseif (strlen($password) < 8) {
    msg("Password must be at least 8 characters", 400);
}


$query = "SELECT id FROM admins WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    msg("Email already exists", 409);
}
$stmt->close();


$hashed_password = password_hash($password, PASSWORD_DEFAULT);
$query = "INSERT INTO admins (name, email, password) VALUES (?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sss", $name, $email, $hashed_password);

if ($stmt->execute()) {
    msg("Admin registered successfully", 200);
} else {
    msg("Registration failed", 500);
}

$stmt->close();
$conn->close();
