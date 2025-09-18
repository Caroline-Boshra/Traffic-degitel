<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';
require_once '../inc/auth.php';

use Firebase\JWT\JWT;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$password = htmlspecialchars(trim($_POST['password'] ?? ''));


if (empty($email) ) {
    msg("Email is required", 400);
}elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("Invalid email format", 400);
}
if (empty($password)) {
    
    msg("password is required", 400);
    
}

$query = "SELECT * FROM admins WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$admin = $result->fetch_assoc();
$stmt->close();

if (!$admin || !password_verify($password, $admin['password'])) {
    msg("Invalid email or password", 401);
}


$payload = [
    "admin_id" => $admin['id'],
    "email" => $admin['email'],
    "exp" => time() + (60 * 60 * 24 * 7)  
];
$secret_key = "Traffic_digital!Secure@2025#Key123";
$jwt = JWT::encode($payload, $secret_key, 'HS256');

 
$data = [
    "id" => $admin['id'],
    "name" => $admin['name'],
    "email" => $admin['email'],
    "token" => $jwt
];

msg("Login successful", 200, $data);