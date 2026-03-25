<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

if (isLoggedIn()) {
    redirect(ADMIN_URL . '/dashboard.php');
}

$siteName = getSetting('site_name', 'esk.dev');

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter both username and password.';
    } else {
        if (login($username, $password, $remember)) {
            redirect(ADMIN_URL . '/dashboard.php');
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | <?php echo escape(SITE_NAME); ?></title>
    
    <?php $favicon = getSetting('favicon'); ?>
    <?php if (!empty($favicon) && file_exists(ROOT_PATH . 'uploads/' . $favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/uploads/<?php echo escape($favicon); ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAZ5JREFUWIXtl7FuwzAMRB+NsGnXDs0SnKKdOi6gS5cuobN0SJcuXbp0yk2n0+nSIZ2gBEJYv4S/EsX2e2P/YEcA/H8iwOVy+R6Px/fL5fJ9uVy+z+fz93Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/l1+f4HAJfV3hQ4b0OQAAAAASUVORK5CYII=">
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
    <style>
        .password-wrapper {
            position: relative;
        }
        .password-wrapper input {
            padding-right: 3rem;
        }
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-muted);
            cursor: pointer;
            padding: 0.25rem;
        }
        .password-toggle:hover {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">
                    <?php echo formatLogo($siteName); ?>
                </div>
                <p class="login-subtitle">Sign in to your admin account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo escape($error); ?></span>
                </div>
            <?php endif; ?>
            
            <?php if ($flash = sessionFlash('error')): ?>
                <div class="alert alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <span><?php echo escape($flash); ?></span>
                </div>
            <?php endif; ?>
            
            <form action="" method="POST">
                <div class="form-group">
                    <label class="form-label" for="username">Username or Email</label>
                    <input type="text" id="username" name="username" class="form-control" 
                           value="<?php echo old('username'); ?>" required autofocus>
                </div>
                
                <div class="form-group">
                    <label class="form-label" for="password">Password</label>
                    <div class="password-wrapper">
                        <input type="password" id="password" name="password" class="form-control" required>
                        <button type="button" class="password-toggle" onclick="togglePassword()">
                            <i class="fas fa-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <div class="form-group">
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                    <i class="fas fa-sign-in-alt"></i> Sign In
                </button>
            </form>
            
            <div class="login-footer">
                <a href="<?php echo SITE_URL; ?>">
                    <i class="fas fa-arrow-left"></i> Back to Website
                </a>
            </div>
        </div>
    </div>
    
    <script>
    function togglePassword() {
        const passwordInput = document.getElementById('password');
        const toggleIcon = document.getElementById('toggleIcon');
        
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            toggleIcon.classList.remove('fa-eye');
            toggleIcon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            toggleIcon.classList.remove('fa-eye-slash');
            toggleIcon.classList.add('fa-eye');
        }
    }
    </script>
</body>
</html>
