<?php
/**
 * Authentication Helper
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        redirect(ADMIN_URL . '/login.php');
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        setFlash('error', 'Access denied. Admin privileges required.');
        redirect(ADMIN_URL . '/dashboard.php');
    }
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    
    return db()->fetchOne(
        "SELECT id, username, email, full_name, role, avatar FROM users WHERE id = ?",
        [$_SESSION['user_id']]
    );
}

function login($username, $password) {
    $user = db()->fetchOne(
        "SELECT id, username, password, role, full_name FROM users WHERE username = ? OR email = ?",
        [$username, $username]
    );
    
    if (!$user) {
        return false;
    }
    
    if (!password_verify($password, $user['password'])) {
        return false;
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['login_time'] = time();
    
    return true;
}

function logout() {
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_name']);
    unset($_SESSION['login_time']);
    session_destroy();
}

function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
}

function verifyPassword($password, $hash) {
    return password_verify($password, $hash);
}

function updateLastLogin($userId) {
    db()->update('users', ['updated_at' => date('Y-m-d H:i:s')], 'id = :id', ['id' => $userId]);
}
