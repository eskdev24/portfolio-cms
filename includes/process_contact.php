<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$subject = sanitize($_POST['subject'] ?? '');
$phone = sanitize($_POST['phone'] ?? '');
$message = sanitize($_POST['message'] ?? '');

if (empty($name)) {
    jsonResponse(['success' => false, 'message' => 'Name is required']);
}

if (empty($email) || !isValidEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Valid email is required']);
}

if (empty($message)) {
    jsonResponse(['success' => false, 'message' => 'Message is required']);
}

try {
    db()->insert('messages', [
        'name' => $name,
        'email' => $email,
        'subject' => $subject,
        'phone' => $phone,
        'message' => $message,
        'ip_address' => getClientIP(),
        'status' => 'unread'
    ]);
    
    jsonResponse([
        'success' => true, 
        'message' => 'Message sent successfully! I\'ll get back to you soon.'
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}
