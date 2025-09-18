<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;

function generateTokens($admin_id) {
    $secret_key = "Traffic_digital!Secure@2025#Key123";

    
    $access_payload = [
        'admin_id' => $admin_id,
        'exp' => time() + (120 * 60)
    ];
    $access_token = JWT::encode($access_payload, $secret_key, 'HS256');

    
    $refresh_payload = [
        'admin_id' => $admin_id,
        'exp' => time() + (60 * 60 * 24 * 5)
    ];
    $refresh_token = JWT::encode($refresh_payload, $secret_key, 'HS256');

    return [
        'access_token' => $access_token,
        'refresh_token' => $refresh_token
    ];
}