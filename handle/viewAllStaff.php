<?php 
require_once '../inc/connection.php';
require_once '../inc/function.php';

header("Content-Type: application/json");

$query = "SELECT * FROM staff ORDER BY id DESC";
$runQuery = mysqli_query($conn, $query);

if (mysqli_num_rows($runQuery) > 0) {
    $staff = mysqli_fetch_all($runQuery, MYSQLI_ASSOC);

    foreach ($staff as &$member) {
        $member['image'] = !empty($member['image']) 
            ? 'http://localhost/TrafficDigital/uploads/' . $member['image']
            : null;
    }

    msg("Staff fetched successfully", 200, $staff);
    
} else {
    msg("No staff members found", 404);
}

$conn->close();