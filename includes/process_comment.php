<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(['success' => false, 'message' => 'Invalid request method'], 405);
}

$post_id = isset($_POST['post_id']) ? (int)$_POST['post_id'] : 0;
$name = sanitize($_POST['name'] ?? '');
$email = sanitize($_POST['email'] ?? '');
$comment = sanitize($_POST['comment'] ?? '');

if (empty($post_id)) {
    jsonResponse(['success' => false, 'message' => 'Post ID is required']);
}

if (empty($name)) {
    jsonResponse(['success' => false, 'message' => 'Name is required']);
}

if (empty($email) || !isValidEmail($email)) {
    jsonResponse(['success' => false, 'message' => 'Valid email is required']);
}

if (empty($comment)) {
    jsonResponse(['success' => false, 'message' => 'Comment is required']);
}

$post = db()->fetchOne("SELECT id FROM blog_posts WHERE id = ?", [$post_id]);
if (!$post) {
    jsonResponse(['success' => false, 'message' => 'Post not found'], 404);
}

try {
    db()->insert('blog_comments', [
        'post_id' => $post_id,
        'name' => $name,
        'email' => $email,
        'comment' => $comment,
        'status' => 'pending',
        'ip_address' => getClientIP()
    ]);
    
    logActivity(null, 'new_comment', "New comment from {$name} on post ID {$post_id}");
    
    jsonResponse([
        'success' => true, 
        'message' => 'Comment submitted successfully! Your comment will be visible after review.'
    ]);
} catch (Exception $e) {
    jsonResponse(['success' => false, 'message' => 'Failed to submit comment. Please try again.']);
}
