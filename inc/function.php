<?php 
function msg($msg, $code, $data = null) {
    header("Content-Type: application/json"); 
    http_response_code($code);
    echo json_encode([
        "status" => $code,
        "message" => $msg,
        "data" => $data
    ]);
    exit;
}