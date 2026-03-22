<?php
$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$unreadMessages = db()->count('messages', "status = 'unread'");
$pendingComments = db()->count('blog_comments', "status = 'pending'");
$user = getCurrentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' | ' : ''; ?>Admin Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/admin.css">
</head>
<body>
    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="sidebar-logo">
                    Eugene<span>.</span>Simpson
                </a>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/dashboard.php" class="nav-link <?php echo $currentPage === 'dashboard' ? 'active' : ''; ?>">
                                <i class="fas fa-tachometer-alt"></i>
                                <span>Dashboard</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Content</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_projects.php" class="nav-link <?php echo $currentPage === 'manage_projects' || $currentPage === 'add_project' || $currentPage === 'edit_project' ? 'active' : ''; ?>">
                                <i class="fas fa-briefcase"></i>
                                <span>Projects</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_blog.php" class="nav-link <?php echo $currentPage === 'manage_blog' || $currentPage === 'add_post' || $currentPage === 'edit_post' ? 'active' : ''; ?>">
                                <i class="fas fa-blog"></i>
                                <span>Blog Posts</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_skills.php" class="nav-link <?php echo $currentPage === 'manage_skills' ? 'active' : ''; ?>">
                                <i class="fas fa-code"></i>
                                <span>Skills</span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_testimonials.php" class="nav-link <?php echo $currentPage === 'manage_testimonials' ? 'active' : ''; ?>">
                                <i class="fas fa-quote-left"></i>
                                <span>Testimonials</span>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Communication</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_messages.php" class="nav-link <?php echo $currentPage === 'manage_messages' ? 'active' : ''; ?>">
                                <i class="fas fa-envelope"></i>
                                <span>Messages</span>
                                <?php if ($unreadMessages > 0): ?>
                                    <span class="nav-badge"><?php echo $unreadMessages; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/manage_comments.php" class="nav-link <?php echo $currentPage === 'manage_comments' ? 'active' : ''; ?>">
                                <i class="fas fa-comments"></i>
                                <span>Comments</span>
                                <?php if ($pendingComments > 0): ?>
                                    <span class="nav-badge"><?php echo $pendingComments; ?></span>
                                <?php endif; ?>
                            </a>
                        </li>
                    </ul>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Settings</div>
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo ADMIN_URL; ?>/settings.php" class="nav-link <?php echo $currentPage === 'settings' ? 'active' : ''; ?>">
                                <i class="fas fa-cog"></i>
                                <span>Settings</span>
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>
            
            <div style="padding: 1.5rem; border-top: 1px solid var(--border-color); margin-top: auto;">
                <a href="<?php echo SITE_URL; ?>" target="_blank" class="btn btn-secondary btn-sm" style="width: 100%; margin-bottom: 0.5rem;">
                    <i class="fas fa-external-link-alt"></i> View Website
                </a>
                <a href="<?php echo ADMIN_URL; ?>/logout.php" class="btn btn-secondary btn-sm" style="width: 100%;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </aside>
        
        <main class="main-content">
            <header class="admin-header">
                <div class="header-left">
                    <button class="menu-toggle" id="menuToggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1 class="header-title"><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                </div>
                
                <div class="header-right">
                    <div class="header-actions">
                        <a href="<?php echo ADMIN_URL; ?>/manage_messages.php" class="header-btn" title="Messages">
                            <i class="fas fa-envelope"></i>
                            <?php if ($unreadMessages > 0): ?>
                                <span class="badge"></span>
                            <?php endif; ?>
                        </a>
                    </div>
                    
                    <div class="user-dropdown" id="userDropdown">
                        <div class="user-avatar">
                            <?php echo strtoupper(substr($user['full_name'] ?? 'A', 0, 1)); ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo escape($user['full_name'] ?? 'Admin'); ?></span>
                            <span class="user-role"><?php echo escape($user['role'] ?? 'admin'); ?></span>
                        </div>
                        <i class="fas fa-chevron-down" style="font-size: 0.75rem; color: var(--text-muted);"></i>
                    </div>
                </div>
            </header>
            
            <div class="admin-body">
                <?php if ($flash = sessionFlash('success')): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo escape($flash); ?></span>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
                
                <?php if ($flash = sessionFlash('error')): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo escape($flash); ?></span>
                        <button class="alert-close" onclick="this.parentElement.remove()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                <?php endif; ?>
