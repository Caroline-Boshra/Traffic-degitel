<?php 

require_once '../inc/connection.php';

$createAdmins = "CREATE TABLE admins (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
email VARCHAR(100) UNIQUE,
password VARCHAR(255)
)";
$createClients = "CREATE TABLE clients (
id INT AUTO_INCREMENT PRIMARY KEY,
full_name VARCHAR(100),
email VARCHAR(100),
phone VARCHAR(20),
service TEXT,
message TEXT,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$createStaff = " CREATE TABLE staff (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100),
specialization TEXT,
email VARCHAR(100),
image VARCHAR(255),
created_by INT,
FOREIGN KEY (created_by) REFERENCES admins(id)
)";

$conn->query($createAdmins);
$conn->query($createClients);
$conn->query($createStaff);

echo "Tables added successfully";