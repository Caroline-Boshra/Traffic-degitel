<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';
require_once '../inc/auth.php';  

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}

$admin_id = getAdminIdFromToken();

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) {
    msg("Invalid staff ID", 400);
}

$query = "SELECT * FROM staff WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$staff = $result->fetch_assoc();
$stmt->close();

if (!$staff) {
    msg("Staff not found", 404);
}


$staff['image'] = !empty($staff['image']) 
    ? 'http://localhost/TrafficDigital/uploads/' . $staff['image'] 
    : null;

msg("Staff fetched successfully", 200, $staff);