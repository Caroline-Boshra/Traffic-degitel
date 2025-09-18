<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';

header("Content-Type: application/json");

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    msg("Method Not Allowed", 405);
}


$protocol = (
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443)
) ? 'https' : 'http';


$scriptName = $_SERVER['SCRIPT_NAME'] ?? '/';
$projectRoot = dirname(dirname($scriptName)); 


$base_url = rtrim($protocol . '://' . $_SERVER['HTTP_HOST'] . $projectRoot, '/') . '/uploads/';


$query = "SELECT * FROM staff ORDER BY id DESC";
$runQuery = mysqli_query($conn, $query);

if ($runQuery && mysqli_num_rows($runQuery) > 0) {
    $staff = mysqli_fetch_all($runQuery, MYSQLI_ASSOC);

    foreach ($staff as &$member) {
        $raw = $member['image'] ?? '';
        
        $imageName = $raw ? basename(parse_url($raw, PHP_URL_PATH)) : '';
        $member['image'] = $imageName ? $base_url . $imageName : null;
    }

    msg("Staff fetched successfully", 200, $staff);
} else {
    msg("No staff members found", 404);
}

$conn->close();