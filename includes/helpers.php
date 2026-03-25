<?php
/**
 * Helper Functions
 */

function sanitize($string) {
    return htmlspecialchars(trim($string), ENT_QUOTES, 'UTF-8');
}

function escape($string) {
    return htmlspecialchars($string ?? '', ENT_QUOTES, 'UTF-8');
}

function formatLogo($siteName) {
    if (strpos($siteName, '.') !== false) {
        $parts = explode('.', $siteName, 2);
        return '<span class="esk">' . escape($parts[0]) . '</span><span class="dot">.</span><span class="dev">' . escape($parts[1]) . '</span>';
    }
    return '<span class="esk">' . escape($siteName) . '</span>';
}

function redirect($url) {
    header("Location: " . $url);
    exit;
}

function old($field, $default = '') {
    return $_POST[$field] ?? $default;
}

function sessionFlash($key) {
    if (isset($_SESSION['flash'][$key])) {
        $message = $_SESSION['flash'][$key];
        unset($_SESSION['flash'][$key]);
        return $message;
    }
    return null;
}

function setFlash($key, $message) {
    $_SESSION['flash'][$key] = $message;
}

function isAjax() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
           strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';
}

function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

function slugify($text) {
    $text = preg_replace('/[^a-z0-9]+/i', '-', strtolower($text));
    return trim($text, '-');
}

function generateSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

function truncate($text, $length = 100, $suffix = '...') {
    if (strlen($text) > $length) {
        return substr($text, 0, $length) . $suffix;
    }
    return $text;
}

function formatDate($date, $format = 'M d, Y') {
    return date($format, strtotime($date));
}

function timeAgo($datetime) {
    $time = strtotime($datetime);
    $diff = time() - $time;
    
    if ($diff < 60) return $diff . ' seconds ago';
    if ($diff < 3600) return floor($diff / 60) . ' minutes ago';
    if ($diff < 86400) return floor($diff / 3600) . ' hours ago';
    if ($diff < 2592000) return floor($diff / 86400) . ' days ago';
    
    return date('M d, Y', $time);
}

function getClientIP() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        return $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    }
}

function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function isValidURL($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

function randomString($length = 32) {
    return bin2hex(random_bytes($length / 2));
}

function getSetting($key, $default = null) {
    static $settings = null;
    
    if ($settings === null) {
        $db = db();
        $rows = $db->fetchAll("SELECT setting_key, setting_value FROM settings");
        foreach ($rows as $row) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
    }
    
    return $settings[$key] ?? $default;
}

function uploadImage($file, $directory = 'uploads/') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return false;
    }
    
    $maxSize = MAX_FILE_SIZE;
    $allowedTypes = ALLOWED_IMAGE_TYPES;
    
    if ($file['size'] > $maxSize) {
        return ['error' => 'File size exceeds maximum allowed'];
    }
    
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mimeType = $finfo->file($file['tmp_name']);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['error' => 'Invalid file type'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '_' . time() . '.' . $extension;
    $uploadPath = ROOT_PATH . $directory . $filename;
    
    if (!is_dir(dirname($uploadPath))) {
        mkdir(dirname($uploadPath), 0755, true);
    }
    
    if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
        return ['filename' => $filename, 'path' => $directory . $filename];
    }
    
    return ['error' => 'Failed to upload file'];
}

function deleteImage($filename, $directory = 'uploads/') {
    $path = ROOT_PATH . $directory . $filename;
    if (file_exists($path)) {
        return unlink($path);
    }
    return false;
}

function paginate($total, $perPage, $currentPage) {
    $totalPages = ceil($total / $perPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'has_prev' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages,
        'prev_page' => $currentPage - 1,
        'next_page' => $currentPage + 1,
        'offset' => ($currentPage - 1) * $perPage
    ];
}
