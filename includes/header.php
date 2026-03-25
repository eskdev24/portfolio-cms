<?php
/**
 * Header Template
 */

$currentPage = basename($_SERVER['PHP_SELF'], '.php');
$siteName = getSetting('site_name', 'esk.dev');
$siteTagline = getSetting('site_tagline', 'Web Developer & Designer');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?php echo escape(getSetting('site_description', '')); ?>">
    <meta name="author" content="<?php echo escape($siteName); ?>">
    
    <title><?php echo isset($pageTitle) ? escape($pageTitle) . ' | ' : ''; ?><?php echo escape($siteName); ?></title>
    
    <?php $favicon = getSetting('favicon'); ?>
    <?php if (!empty($favicon) && file_exists(ROOT_PATH . 'uploads/' . $favicon)): ?>
        <link rel="icon" type="image/x-icon" href="<?php echo SITE_URL; ?>/uploads/<?php echo escape($favicon); ?>">
    <?php else: ?>
        <link rel="icon" type="image/png" href="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACAAAAAgCAYAAABzenr0AAAABHNCSVQICAgIfAhkiAAAAAlwSFlzAAALEwAACxMBAJqcGAAAAZ5JREFUWIXtl7FuwzAMRB+NsGnXDs0SnKKdOi6gS5cuobN0SJcuXbp0yk2n0+nSIZ2gBEJYv4S/EsX2e2P/YEcA/H8iwOVy+R6Px/fL5fJ9uVy+z+fz93Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/t0Or1Pp9P7dDq9T6fT+3Q6vU+n0/l1+f4HAJfV3hQ4b0OQAAAAASUVORK5CYII=">
    <?php endif; ?>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link rel="stylesheet" href="<?php echo SITE_URL; ?>/assets/css/main.css">
    
    <?php if (isset($extraCSS)): ?>
        <?php foreach ($extraCSS as $css): ?>
            <link rel="stylesheet" href="<?php echo $css; ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>
<body>
    <div id="page-wrapper">
        <header class="header" id="header">
            <nav class="navbar container">
                <a href="<?php echo SITE_URL; ?>/index.php" class="logo">
                    <span class="logo-text"><?php echo formatLogo($siteName); ?></span>
                </a>
                
                <div class="nav-menu" id="navMenu">
                    <ul class="nav-list">
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/index.php" 
                               class="nav-link <?php echo $currentPage === 'index' ? 'active' : ''; ?>">
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/about.php" 
                               class="nav-link <?php echo $currentPage === 'about' ? 'active' : ''; ?>">
                                About
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/portfolio.php" 
                               class="nav-link <?php echo $currentPage === 'portfolio' ? 'active' : ''; ?>">
                                Portfolio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/blog.php" 
                               class="nav-link <?php echo $currentPage === 'blog' || $currentPage === 'single' ? 'active' : ''; ?>">
                                Blog
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="<?php echo SITE_URL; ?>/contact.php" 
                               class="nav-link <?php echo $currentPage === 'contact' ? 'active' : ''; ?>">
                                Contact
                            </a>
                        </li>
                    </ul>
                    
                    <div class="nav-actions">
                        <button class="theme-toggle" id="themeToggle" aria-label="Toggle theme">
                            <i class="fas fa-moon"></i>
                        </button>
                        <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                            <i class="fas fa-paper-plane"></i> Hire Me
                        </a>
                    </div>
                </div>
                
                <button class="nav-toggle" id="navToggle" aria-label="Toggle navigation">
                    <span></span>
                    <span></span>
                    <span></span>
                </button>
            </nav>
        </header>
        
        <main class="main">
