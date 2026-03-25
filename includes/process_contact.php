<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

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
    
    logActivity(null, 'contact_form', "New contact message from {$name} ({$email})");
    
    $formspreeEndpoint = getSetting('formspree_endpoint');
    if ($formspreeEndpoint) {
        $formspreeData = [
            'name' => $name,
            'email' => $email,
            'subject' => $subject,
            'phone' => $phone,
            'message' => $message
        ];
        
        $ch = curl_init($formspreeEndpoint);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($formspreeData));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Accept: application/json']);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_exec($ch);
        curl_close($ch);
    }
    
    jsonResponse([
        'success' => true, 
        'message' => 'Message sent successfully! I\'ll get back to you soon.'
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to send message. Please try again.']);
}
