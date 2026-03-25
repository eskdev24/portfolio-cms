<?php
/**
 * Portfolio CMS Configuration
 */

define('NAME', 'Eugene Simpson');
define('SITE_NAME', 'esk.dev');
define('SITE_TAGLINE', 'Web Developer & Graphic Designer');
define('SITE_URL', 'http://localhost/apps/portfolio-cms');
define('ADMIN_URL', SITE_URL . '/admin');

// Database Configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_cms');
define('DB_USER', 'root');
define('DB_PASS', '');

// Paths
define('ROOT_PATH', dirname(__DIR__) . '/');
define('INCLUDES_PATH', ROOT_PATH . 'includes/');
define('UPLOADS_PATH', ROOT_PATH . 'uploads/');
define('ASSETS_PATH', ROOT_PATH . 'assets/');

// Session Configuration
define('SESSION_LIFETIME', 86400);
define('COOKIE_NAME', 'portfolio_session');

// Pagination
define('ITEMS_PER_PAGE', 6);
define('BLOG_PER_PAGE', 6);

// Upload Settings
define('MAX_FILE_SIZE', 5242880);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/gif', 'image/webp']);

// Timezone
date_default_timezone_set('UTC');

// Error Reporting (Disable in production)
ini_set('display_errors', 0);
error_reporting(0);

// Start Session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
