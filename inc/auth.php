<?php
require_once '../vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

function getAdminIdFromToken() {
    $headers = apache_request_headers();
    $authHeader = $headers['Authorization'] ?? '';

    if (!$authHeader || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
        msg("Token not provided", 401);
    }

    $jwt = $matches[1];
    $secret_key = "Traffic_digital!Secure@2025#Key123";

    try {
        $decoded = JWT::decode($jwt, new Key($secret_key, 'HS256'));
        return $decoded->admin_id;
    } catch (Exception $e) {
        msg("Invalid or expired token", 401);
    }
}