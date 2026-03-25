        </main>
        
        <footer class="footer">
            <div class="container">
                <div class="footer-grid">
                    <div class="footer-section">
                        <a href="<?php echo SITE_URL; ?>/index.php" class="footer-logo">
                            <span class="logo-text"><?php echo formatLogo(getSetting('site_name', 'esk.dev')); ?></span>
                        </a>
                        <p class="footer-text">
                            Creating beautiful digital experiences through creative design and clean code.
                        </p>
                        <div class="social-links">
                            <?php if ($facebook = getSetting('facebook_url')): ?>
                                <a href="<?php echo escape($facebook); ?>" target="_blank" rel="noopener" aria-label="Facebook">
                                    <i class="fab fa-facebook-f"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($twitter = getSetting('twitter_url')): ?>
                                <a href="<?php echo escape($twitter); ?>" target="_blank" rel="noopener" aria-label="Twitter">
                                    <i class="fab fa-twitter"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($linkedin = getSetting('linkedin_url')): ?>
                                <a href="<?php echo escape($linkedin); ?>" target="_blank" rel="noopener" aria-label="LinkedIn">
                                    <i class="fab fa-linkedin-in"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($github = getSetting('github_url')): ?>
                                <a href="<?php echo escape($github); ?>" target="_blank" rel="noopener" aria-label="GitHub">
                                    <i class="fab fa-github"></i>
                                </a>
                            <?php endif; ?>
                            <?php if ($instagram = getSetting('instagram_url')): ?>
                                <a href="<?php echo escape($instagram); ?>" target="_blank" rel="noopener" aria-label="Instagram">
                                    <i class="fab fa-instagram"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="footer-section">
                        <h4 class="footer-title">Quick Links</h4>
                        <ul class="footer-links">
                            <li><a href="<?php echo SITE_URL; ?>/index.php">Home</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/about.php">About Me</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/portfolio.php">Portfolio</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/blog.php">Blog</a></li>
                            <li><a href="<?php echo SITE_URL; ?>/contact.php">Contact</a></li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4 class="footer-title">Services</h4>
                        <ul class="footer-links">
                            <li>Website Dev</li>
                            <li>Web App Dev</li>
                            <li>Mobile App Dev</li>
                            <li>UI/UX Designs</li>
                            <li>Graphic Design</li>
                            <li>WordPress Sites</li>
                            <li>Database Dev</li>
                        </ul>
                    </div>
                    
                    <div class="footer-section">
                        <h4 class="footer-title">Contact Info</h4>
                        <ul class="footer-contact">
                            <?php if ($email = getSetting('email')): ?>
                                <li>
                                    <i class="fas fa-envelope"></i>
                                    <a href="mailto:<?php echo escape($email); ?>"><?php echo escape($email); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if ($phone = getSetting('phone')): ?>
                                <li>
                                    <i class="fas fa-phone"></i>
                                    <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>"><?php echo escape($phone); ?></a>
                                </li>
                            <?php endif; ?>
                            <?php if ($address = getSetting('address')): ?>
                                <li>
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span><?php echo escape($address); ?></span>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>
                
                <div class="footer-bottom">
                    <p>&copy; <?php echo date('Y'); ?> <?php echo escape(getSetting('site_name', 'esk.dev')); ?>. All Rights Reserved.</p>
                </div>
            </div>
        </footer>
    </div>
    
    <button class="back-to-top" id="backToTop" aria-label="Back to top">
        <i class="fas fa-arrow-up"></i>
    </button>
    
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <?php if (isset($extraJS)): ?>
        <?php foreach ($extraJS as $js): ?>
            <script src="<?php echo $js; ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
