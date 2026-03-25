<?php
/**
 * Authentication Helper
 */

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/helpers.php';

if (!isLoggedIn()) {
    checkRememberToken();
}

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

function login($username, $password, $remember = false) {
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
    
    if ($remember) {
        $token = bin2hex(random_bytes(32));
        $expires = date('Y-m-d H:i:s', strtotime('+30 days'));
        
        db()->query(
            "UPDATE users SET remember_token = ?, remember_token_expires = ? WHERE id = ?",
            [$token, $expires, $user['id']]
        );
        
        setcookie('remember_token', $token, time() + (86400 * 30), '/', '', false, true);
    }
    
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_role'] = $user['role'];
    $_SESSION['user_name'] = $user['full_name'];
    $_SESSION['login_time'] = time();
    
    logActivity($user['id'], 'login', 'User logged in');
    
    return true;
}

function logout() {
    if (isset($_SESSION['user_id'])) {
        db()->query("UPDATE users SET remember_token = NULL, remember_token_expires = NULL WHERE id = ?", [$_SESSION['user_id']]);
    }
    
    if (isset($_COOKIE['remember_token'])) {
        setcookie('remember_token', '', time() - 3600, '/');
    }
    
    unset($_SESSION['user_id']);
    unset($_SESSION['user_role']);
    unset($_SESSION['user_name']);
    unset($_SESSION['login_time']);
    session_destroy();
}

function checkRememberToken() {
    if (isset($_COOKIE['remember_token']) && !isLoggedIn()) {
        $token = $_COOKIE['remember_token'];
        
        $user = db()->fetchOne(
            "SELECT id, username, role, full_name, remember_token_expires FROM users WHERE remember_token = ?",
            [$token]
        );
        
        if ($user && strtotime($user['remember_token_expires']) > time()) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_role'] = $user['role'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['login_time'] = time();
            
            return true;
        }
    }
    return false;
}

function logActivity($userId, $action, $description = '') {
    try {
        db()->insert('activity_log', [
            'user_id' => $userId,
            'action' => $action,
            'description' => $description,
            'ip_address' => getClientIP()
        ]);
    } catch (Exception $e) {
        // Silent fail for activity logging
    }
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
