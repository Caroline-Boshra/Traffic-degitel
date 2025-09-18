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


$query = "SELECT image FROM staff WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

if (!$staff) {
    msg("Staff not found", 404);
}


if (!empty($staff['image']) && file_exists("../uploads/" . $staff['image'])) {
    unlink("../uploads/" . $staff['image']);
}


$query = "DELETE FROM staff WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
if ($stmt->execute()) {
    msg("Staff deleted successfully", 200);
} else {
    msg("Failed to delete staff", 500);
}
$stmt->close();
$conn->close();