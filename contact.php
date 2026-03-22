<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Contact';
?>
<?php require_once 'includes/header.php'; ?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">Get In Touch</h1>
            <p class="section-subtitle">
                Have a project in mind? Let's work together to create something amazing.
            </p>
        </div>
        
        <div class="contact-grid">
            <div class="contact-info fade-in">
                <h2>Let's Talk About Your Project</h2>
                <p>
                    I'm always excited to connect with new people and discuss potential collaborations.
                    Whether you have a specific project in mind or just want to say hello, feel free to reach out!
                </p>
                
                <div style="margin-top: 2rem;">
                    <?php if ($email = getSetting('email')): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-envelope"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Email</h4>
                                <a href="mailto:<?php echo escape($email); ?>"><?php echo escape($email); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($phone = getSetting('phone')): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-phone"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Phone</h4>
                                <a href="tel:<?php echo preg_replace('/[^0-9+]/', '', $phone); ?>"><?php echo escape($phone); ?></a>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($address = getSetting('address')): ?>
                        <div class="contact-item">
                            <div class="contact-icon">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div class="contact-details">
                                <h4>Location</h4>
                                <p><?php echo escape($address); ?></p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div style="margin-top: 2rem;">
                    <h4 style="margin-bottom: 1rem;">Follow Me</h4>
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
            </div>
            
            <div class="contact-form-wrapper fade-in">
                <form action="<?php echo SITE_URL; ?>/includes/process_contact.php" method="POST" id="contactForm">
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="name">Name *</label>
                            <input type="text" id="name" name="name" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="email">Email *</label>
                            <input type="email" id="email" name="email" class="form-control" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" class="form-control">
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="subject">Subject</label>
                            <input type="text" id="subject" name="subject" class="form-control">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label class="form-label" for="message">Message *</label>
                        <textarea id="message" name="message" class="form-control" 
                                  rows="6" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%;">
                        <i class="fas fa-paper-plane"></i> Send Message
                    </button>
                </form>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary); padding: 4rem 0;">
    <div class="container">
        <div style="text-align: center; max-width: 600px; margin: 0 auto;">
            <h2 style="margin-bottom: 1rem;">Availability</h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem;">
                I'm currently available for freelance projects and collaborations.
                Feel free to reach out if you'd like to work together!
            </p>
            <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                <i class="fas fa-calendar"></i> Schedule a Call
            </a>
        </div>
    </div>
</section>

<style>
.contact-toast {
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    color: white;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: var(--shadow-lg);
    transform: translateX(120%);
    transition: transform 0.4s ease;
    z-index: 9999;
    max-width: 350px;
}

.contact-toast.show {
    transform: translateX(0);
}

.contact-toast.success {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.contact-toast.error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.contact-toast i {
    font-size: 1.25rem;
}
</style>

<div id="contactToast" class="contact-toast">
    <i class="fas fa-check-circle" id="contactToastIcon"></i>
    <span id="contactToastMessage"></span>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const contactForm = document.getElementById('contactForm');
    const toast = document.getElementById('contactToast');
    const toastIcon = document.getElementById('contactToastIcon');
    const toastMessage = document.getElementById('contactToastMessage');
    
    function showToast(message, isSuccess) {
        toastMessage.textContent = message;
        toastIcon.className = isSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        toast.className = 'contact-toast ' + (isSuccess ? 'success' : 'error');
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }
    
    if (contactForm) {
        contactForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Sending...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, true);
                    contactForm.reset();
                } else {
                    showToast(data.message || 'An error occurred', false);
                }
            })
            .catch(error => {
                showToast('Failed to send message. Please try again.', false);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
