<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';
require_once '../inc/auth.php';  


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    msg("Method Not Allowed", 405);
}

$admin_id = getAdminIdFromToken();


$id = (int)($_POST['id'] ?? 0);
if ($id <= 0) {
    msg("Invalid staff ID", 400);
}


$query = "SELECT * FROM staff WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
if (!$staff) {
    msg("Staff member not found", 404);
}
$stmt->close();


$name = isset($_POST['name']) ? htmlspecialchars(trim($_POST['name'])) : $staff['name'];
$specialization = isset($_POST['specialization']) ? htmlspecialchars(trim($_POST['specialization'])) : $staff['specialization'];
$email = isset($_POST['email']) ? htmlspecialchars(trim($_POST['email'])) : $staff['email'];


if (isset($_POST['name']) && empty($name)) {
    msg("Name is required", 400);
}
if (isset($_POST['specialization']) && empty($specialization)) {
    msg("Specialization is required", 400);
}
if (isset($_POST['email'])) {
    
    if (empty($email)) {
        msg("Email is required", 400);
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        msg("This email is not valid", 400);
    }

   
    $query = "SELECT id FROM staff WHERE email = ? AND id != ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        msg("This email is already used by another staff", 403);
    }
    $stmt->close();
}


$imageName = $staff['image']; 
if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
    $image = $_FILES['image'];
    $tmpName = $image['tmp_name'];
    $size = $image['size'] / (1024 * 1024);
    $ext = strtolower(pathinfo($image['name'], PATHINFO_EXTENSION));
    $newName = uniqid() . '.' . $ext;
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (!in_array($ext, $allowed)) {
        msg("Invalid image type", 400);
    }
    if ($size > 1) {
        msg("Image must be less than 1MB", 400);
    }

    $uploadPath = "../uploads/$newName";
    if (!move_uploaded_file($tmpName, $uploadPath)) {
        msg("Failed to upload image", 500);
    }

   
    if (!empty($imageName) && file_exists("../uploads/$imageName")) {
        unlink("../uploads/$imageName");
    }

    $imageName = $newName;
}


$query = "UPDATE staff SET name = ?, specialization = ?, email = ?, image = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ssssi", $name, $specialization, $email, $imageName, $id);

if ($stmt->execute()) {
   
       $staff=[
        "id" => $stmt->insert_id,
        "name" => $name,
        "specialization" => $specialization,
        "email" => $email,
        "image" => "http://localhost/TrafficDigital/uploads/$imageName",   
    ];
    msg("Staff updated successfully", 200 ,$staff);
      
} else {
    msg("Update failed", 500);
}

$stmt->close();
$conn->close();