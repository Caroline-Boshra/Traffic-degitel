<?php

require_once '../inc/connection.php';
require_once '../inc/function.php';
require_once '../inc/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$admin_id = getAdminIdFromToken(); 

$name = htmlspecialchars(trim($_POST['name'] ?? ''));
$specialization = htmlspecialchars(trim($_POST['specialization'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));

if (empty($name)){
    msg("Name is required",400);
}
    
if (empty($specialization)){ 
    msg("Specialization is required",400);
}

if (empty($email)) {
    msg("Email is required", 400);
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("This email is not valid", 400);
}


$sql = "SELECT id FROM staff WHERE email = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();
if ($stmt->num_rows > 0) {
    msg("This email is already registered",403);
}
$stmt->close();


if (!isset($_FILES['image'])) {
    msg("Image is required",400);
}

$image = $_FILES['image'];
$imageName = $image['name'];
$tmpName   = $image['tmp_name'];
$size      = $image['size'] / (1024 * 1024); 
$error     = $image['error'];
$ext       = strtolower(pathinfo($imageName, PATHINFO_EXTENSION));
$newName   = uniqid() . '.' . $ext;

$allowed = ['jpg', 'jpeg', 'png', 'gif'];
if (!in_array($ext, $allowed)) {
    msg("Invalid file type. Allowed: jpg, jpeg, png, gif",400);
}
if ($size > 1) {
    msg("Image must be less than 1MB",400);
}
if ($error !== 0) {
    msg("Error uploading file",400);
}

$uploadDir = "../uploads/";
if (!is_dir($uploadDir)) {
    msg("Upload directory does not exist", 500);
}
if (!is_writable($uploadDir)) {
    msg("Upload directory is not writable", 500);
}

$uploadPath = $uploadDir . $newName;
if (!move_uploaded_file($tmpName, $uploadPath)) {
    error_log("Failed to move uploaded file: " . print_r(error_get_last(), true));
    msg("Failed to upload image. Please try again.", 500);
}


$sql = "INSERT INTO staff (name, specialization, email, image) VALUES (?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("ssss", $name, $specialization, $email, $newName);

if ($stmt->execute()) {
    
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    $scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
    $projectRoot = dirname(dirname($scriptName)); 
    $baseUrl = rtrim($protocol . "://" . $_SERVER['HTTP_HOST'] . $projectRoot, '/') . '/uploads/';
    $imageUrl = $baseUrl . $newName;

    $staff = [
        "id" => $stmt->insert_id,
        "name" => $name,
        "specialization" => $specialization,
        "email" => $email,
        "image" => $imageUrl,  
    ];
    msg("Data added successfully", 200, $staff);
} else {
    msg("Something went wrong", 500);
}

$stmt->close();
$conn->close();