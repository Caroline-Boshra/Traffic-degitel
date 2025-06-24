<?php
require_once '../inc/connection.php';
require_once '../inc/function.php';


$full_name = htmlspecialchars(trim($_POST['full_name'] ?? ''));
$email = htmlspecialchars(trim($_POST['email'] ?? ''));
$phone = htmlspecialchars(trim($_POST['phone'] ?? ''));
$service = htmlspecialchars(trim($_POST['service'] ?? ''));
$message = htmlspecialchars(trim($_POST['message'] ?? ''));

if (empty($full_name)) {
    msg("Name is required", 400);
}


if (empty($email)) {
    msg("Email is required", 400);
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    msg("This email is not valid", 400);
}

if (empty($phone)) {
    msg("phone is required", 400);
} elseif (! is_string($phone)) {
    msg("Invalid phone number ", 400);
}elseif (strlen($phone)<11) {
    msg("the number must be 11 chars", 400);
}
if (empty($service)) {
    msg("service is required", 400);
}
if (is_numeric($message)) {
    msg("message must be chars ", 400);
}

$query = "INSERT INTO clients (full_name, email, phone, service, message) VALUES (?, ?, ?, ?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssss", $full_name, $email, $phone, $service, $message);

if ($stmt->execute()) {
    $client = [
        "id" => $stmt->insert_id,
        "full_name" => $full_name,
        "email" => $email,
        "phone" => $phone,
        "service" => $service,
        "message" => $message
    ];
   msg("Thank you for contacting us. We will get back to you soon!", 200, $client);
} else {
    msg("Something went wrong. Please try again", 500);
}


$stmt->close();
$conn->close();
// $subject_user = "Thank you for contacting us!";
// $message_user = "Dear $full_name,\n\nThank you for reaching out to us. We will get back to you as soon as possible.\n\nBest regards,\nTraffic Digital Team";
// mail($email, $subject_user, $message_user);
// $adminEmail = "karolingeorge2011@gmail.com"; // غيري ده لإيميلك أو إيميل الإدارة
// $subject_admin = "New Contact Form Submission";
// $message_admin = "A new client filled the contact form:\n\nName: $full_name\nEmail: $email\nPhone: $phone\nService: $service\nMessage:\n$message";
// mail($adminEmail, $subject_admin, $message_admin);