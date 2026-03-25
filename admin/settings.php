<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Settings';

$success = false;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $settings = [
        'name' => sanitize($_POST['name'] ?? ''),
        'site_name' => sanitize($_POST['site_name'] ?? ''),
        'site_tagline' => sanitize($_POST['site_tagline'] ?? ''),
        'site_description' => sanitize($_POST['site_description'] ?? ''),
        'email' => sanitize($_POST['email'] ?? ''),
        'phone' => sanitize($_POST['phone'] ?? ''),
        'whatsapp_number' => sanitize($_POST['whatsapp_number'] ?? ''),
        'formspree_endpoint' => sanitize($_POST['formspree_endpoint'] ?? ''),
        'address' => sanitize($_POST['address'] ?? ''),
        'facebook_url' => sanitize($_POST['facebook_url'] ?? ''),
        'twitter_url' => sanitize($_POST['twitter_url'] ?? ''),
        'linkedin_url' => sanitize($_POST['linkedin_url'] ?? ''),
        'github_url' => sanitize($_POST['github_url'] ?? ''),
        'instagram_url' => sanitize($_POST['instagram_url'] ?? ''),
        'hero_title' => sanitize($_POST['hero_title'] ?? ''),
        'hero_subtitle' => sanitize($_POST['hero_subtitle'] ?? ''),
        'about_description' => sanitize($_POST['about_description'] ?? ''),
    ];
    
    foreach ($settings as $key => $value) {
        $existing = db()->fetchOne("SELECT id FROM settings WHERE setting_key = ?", [$key]);
        
        if ($existing) {
            db()->query(
                "UPDATE settings SET setting_value = ? WHERE setting_key = ?",
                [$value, $key]
            );
        } else {
            db()->insert('settings', [
                'setting_key' => $key,
                'setting_value' => $value
            ]);
        }
    }
    
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['profile_image'], 'uploads/');
        if (isset($upload['filename'])) {
            db()->query(
                "UPDATE users SET avatar = ? WHERE id = ?",
                [$upload['filename'], $_SESSION['user_id']]
            );
        }
    }
    
    if (isset($_FILES['favicon']) && $_FILES['favicon']['error'] === UPLOAD_ERR_OK) {
        $faviconFile = $_FILES['favicon'];
        $allowedTypes = ['image/x-icon', 'image/png', 'image/jpeg', 'image/gif'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($faviconFile['tmp_name']);
        
        if (in_array($mimeType, $allowedTypes) || in_array($faviconFile['type'], ['image/x-icon', 'image/png', 'image/jpeg', 'image/gif'])) {
            $extension = $mimeType === 'image/x-icon' ? 'ico' : 'png';
            $filename = 'favicon.' . $extension;
            $uploadPath = ROOT_PATH . 'uploads/' . $filename;
            
            if (move_uploaded_file($faviconFile['tmp_name'], $uploadPath)) {
                db()->query(
                    "INSERT INTO settings (setting_key, setting_value) VALUES ('favicon', ?) 
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                    [$filename]
                );
            }
        }
    }
    
    if (isset($_FILES['hero_image']) && $_FILES['hero_image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['hero_image'], 'uploads/');
        if ($upload && isset($upload['filename'])) {
            db()->query(
                "INSERT INTO settings (setting_key, setting_value) VALUES ('hero_image', ?) 
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                [$upload['filename']]
            );
        }
    }
    
    if (isset($_FILES['about_image']) && $_FILES['about_image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['about_image'], 'uploads/');
        if ($upload && isset($upload['filename'])) {
            db()->query(
                "INSERT INTO settings (setting_key, setting_value) VALUES ('about_image', ?) 
                 ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                [$upload['filename']]
            );
        }
    }
    
    if (isset($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $cvFile = $_FILES['cv_file'];
        $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($cvFile['tmp_name']);
        
        if (in_array($mimeType, $allowedTypes) || in_array($cvFile['type'], $allowedTypes)) {
            $extension = pathinfo($cvFile['name'], PATHINFO_EXTENSION);
            $filename = 'cv_' . time() . '.' . $extension;
            $uploadPath = ROOT_PATH . 'uploads/' . $filename;
            
            if (move_uploaded_file($cvFile['tmp_name'], $uploadPath)) {
                db()->query(
                    "INSERT INTO settings (setting_key, setting_value) VALUES ('cv_file', ?) 
                     ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)",
                    [$filename]
                );
            }
        }
    }
    
    setFlash('success', 'Settings saved successfully!');
    redirect(ADMIN_URL . '/settings.php');
}

$allSettings = db()->fetchAll("SELECT setting_key, setting_value FROM settings");
$settings = [];
foreach ($allSettings as $s) {
    $settings[$s['setting_key']] = $s['setting_value'];
}

$user = getCurrentUser();
?>
<?php require_once 'header.php'; ?>

<form action="" method="POST" enctype="multipart/form-data">

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Profile Image</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div style="width: 120px; height: 120px; border-radius: 50%; overflow: hidden; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; border: 3px solid var(--primary);">
                <?php if (!empty($user['avatar'])): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($user['avatar']); ?>" alt="Profile" style="width: 100%; height: 100%; object-fit: cover;">
                <?php else: ?>
                    <i class="fas fa-user" style="font-size: 3rem; color: var(--text-muted);"></i>
                <?php endif; ?>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label">Upload New Profile Image</label>
                    <input type="file" name="profile_image" class="form-control" accept="image/*">
                    <div class="form-hint">PNG, JPG, GIF, WEBP (Max 5MB)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Site Images</h3>
    </div>
    <div class="card-body">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
            <div>
                <h4 style="margin-bottom: 1rem;">Favicon</h4>
                <div style="border: 2px dashed var(--border-color); border-radius: var(--radius-lg); padding: 1rem; text-align: center; margin-bottom: 1rem; background: var(--bg-secondary);">
                    <?php if (!empty($settings['favicon'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($settings['favicon']); ?>" alt="Favicon" style="max-width: 32px; max-height: 32px;">
                    <?php else: ?>
                        <i class="fas fa-image" style="font-size: 1.5rem; color: var(--text-muted);"></i>
                    <?php endif; ?>
                </div>
                <div class="form-group">
                    <label class="form-label">Upload Favicon</label>
                    <input type="file" name="favicon" class="form-control" accept=".ico,.png,.jpg,.gif">
                    <div class="form-hint">ICO, PNG, JPG, GIF (32x32 recommended)</div>
                </div>
            </div>
            
            <div>
                <h4 style="margin-bottom: 1rem;">Hero Section Image</h4>
                <div style="border: 2px dashed var(--border-color); border-radius: var(--radius-lg); padding: 1rem; text-align: center; margin-bottom: 1rem; background: var(--bg-secondary);">
                    <?php if (!empty($settings['hero_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($settings['hero_image']); ?>" alt="Hero" style="max-width: 100%; max-height: 200px; border-radius: var(--radius-md);">
                    <?php else: ?>
                        <i class="fas fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                        <p style="color: var(--text-muted); margin-top: 0.5rem;">No image uploaded</p>
                    <?php endif; ?>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="file" name="hero_image" class="form-control" accept="image/*">
                    <div class="form-hint">Recommended size: 450x500px</div>
                </div>
            </div>
            
            <div>
                <h4 style="margin-bottom: 1rem;">About Section Image</h4>
                <div style="border: 2px dashed var(--border-color); border-radius: var(--radius-lg); padding: 1rem; text-align: center; margin-bottom: 1rem; background: var(--bg-secondary);">
                    <?php if (!empty($settings['about_image'])): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($settings['about_image']); ?>" alt="About" style="max-width: 100%; max-height: 200px; border-radius: var(--radius-md);">
                    <?php else: ?>
                        <i class="fas fa-image" style="font-size: 3rem; color: var(--text-muted);"></i>
                        <p style="color: var(--text-muted); margin-top: 0.5rem;">No image uploaded</p>
                    <?php endif; ?>
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <input type="file" name="about_image" class="form-control" accept="image/*">
                    <div class="form-hint">Recommended size: 500x500px</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Resume / CV</h3>
    </div>
    <div class="card-body">
        <div style="display: flex; align-items: center; gap: 2rem; flex-wrap: wrap;">
            <div style="width: 80px; height: 80px; border-radius: var(--radius-lg); overflow: hidden; background: var(--bg-tertiary); display: flex; align-items: center; justify-content: center; border: 2px solid var(--border-color);">
                <?php if (!empty($settings['cv_file'])): ?>
                    <i class="fas fa-file-pdf" style="font-size: 2rem; color: var(--error);"></i>
                <?php else: ?>
                    <i class="fas fa-file-alt" style="font-size: 2rem; color: var(--text-muted);"></i>
                <?php endif; ?>
            </div>
            <div style="flex: 1; min-width: 250px;">
                <?php if (!empty($settings['cv_file'])): ?>
                    <p style="margin-bottom: 0.5rem;"><strong>Current CV:</strong> <?php echo escape($settings['cv_file']); ?></p>
                    <p style="color: var(--success); font-size: 0.875rem; margin-bottom: 1rem;">
                        <i class="fas fa-check-circle"></i> CV uploaded and ready for download
                    </p>
                <?php else: ?>
                    <p style="color: var(--text-muted); margin-bottom: 0.5rem;">No CV uploaded yet</p>
                <?php endif; ?>
                <div class="form-group" style="margin-bottom: 0;">
                    <label class="form-label">Upload Resume / CV</label>
                    <input type="file" name="cv_file" class="form-control" accept=".pdf,.doc,.docx">
                    <div class="form-hint">PDF, DOC, DOCX (Max 10MB)</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">General Settings</h3>
    </div>
    <div class="card-body">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Your Name</label>
                    <input type="text" name="name" class="form-control" 
                           value="<?php echo escape($settings['name'] ?? 'Eugene Simpson'); ?>">
                    <div class="form-hint">Your full name (e.g., Eugene Simpson)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Site Name</label>
                    <input type="text" name="site_name" class="form-control" 
                           value="<?php echo escape($settings['site_name'] ?? 'esk.dev'); ?>">
                    <div class="form-hint">Website name (e.g., esk.dev)</div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Site Tagline</label>
                <input type="text" name="site_tagline" class="form-control" 
                       value="<?php echo escape($settings['site_tagline'] ?? 'Web Developer & Designer'); ?>">
            </div>
            
            <div class="form-group">
                <label class="form-label">Site Description</label>
                <textarea name="site_description" class="form-control" rows="3"><?php echo escape($settings['site_description'] ?? ''); ?></textarea>
            </div>
            
            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">
            
            <h4 style="margin-bottom: 1.5rem;">Hero Section Content</h4>
            
            <div class="form-group">
                <label class="form-label">Hero Title</label>
                <input type="text" name="hero_title" class="form-control" 
                       value="<?php echo escape($settings['hero_title'] ?? 'Web Developer & Graphic Designer'); ?>" 
                       placeholder="e.g., Web Developer & Graphic Designer">
            </div>
            
            <div class="form-group">
                <label class="form-label">Hero Subtitle</label>
                <textarea name="hero_subtitle" class="form-control" rows="3" 
                          placeholder="Brief description about yourself"><?php echo escape($settings['hero_subtitle'] ?? ''); ?></textarea>
            </div>
            
            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">
            
            <h4 style="margin-bottom: 1.5rem;">About Section</h4>
            
            <div class="form-group">
                <label class="form-label">About Description</label>
                <textarea name="about_description" class="form-control" rows="5" 
                          placeholder="Write your about section content here..."><?php echo escape($settings['about_description'] ?? ''); ?></textarea>
            </div>
            
            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">
            
            <h4 style="margin-bottom: 1.5rem;">Contact Information</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo escape($settings['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Phone</label>
                    <input type="tel" name="phone" class="form-control" 
                           value="<?php echo escape($settings['phone'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">WhatsApp Number</label>
                    <input type="tel" name="whatsapp_number" class="form-control" 
                           value="<?php echo escape($settings['whatsapp_number'] ?? ''); ?>" placeholder="e.g., +1234567890">
                    <div class="form-hint">Include country code, no + symbol</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Formspree Endpoint</label>
                    <input type="url" name="formspree_endpoint" class="form-control" 
                           value="<?php echo escape($settings['formspree_endpoint'] ?? ''); ?>" placeholder="https://formspree.io/f/xxxxxxxxx">
                    <div class="form-hint">Formspree endpoint for email notifications (optional)</div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Address</label>
                <input type="text" name="address" class="form-control" 
                       value="<?php echo escape($settings['address'] ?? ''); ?>">
            </div>
            
            <hr style="margin: 2rem 0; border: none; border-top: 1px solid var(--border-color);">
            
            <h4 style="margin-bottom: 1.5rem;">Social Media Links</h4>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Facebook URL</label>
                    <input type="url" name="facebook_url" class="form-control" 
                           value="<?php echo escape($settings['facebook_url'] ?? ''); ?>" placeholder="https://facebook.com/">
                </div>
                <div class="form-group">
                    <label class="form-label">Twitter URL</label>
                    <input type="url" name="twitter_url" class="form-control" 
                           value="<?php echo escape($settings['twitter_url'] ?? ''); ?>" placeholder="https://twitter.com/">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">LinkedIn URL</label>
                    <input type="url" name="linkedin_url" class="form-control" 
                           value="<?php echo escape($settings['linkedin_url'] ?? ''); ?>" placeholder="https://linkedin.com/in/">
                </div>
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" 
                           value="<?php echo escape($settings['github_url'] ?? ''); ?>" placeholder="https://github.com/">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Instagram URL</label>
                <input type="url" name="instagram_url" class="form-control" 
                       value="<?php echo escape($settings['instagram_url'] ?? ''); ?>" placeholder="https://instagram.com/">
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save All Settings
                </button>
            </div>
    </div>
</div>

</form>

<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Change Password</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST" id="passwordForm">
            <div class="form-group">
                <label class="form-label">Current Password</label>
                <input type="password" name="current_password" class="form-control">
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">New Password</label>
                    <input type="password" name="new_password" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Confirm New Password</label>
                    <input type="password" name="confirm_password" class="form-control">
                </div>
            </div>
            <div style="margin-top: 1rem;">
                <button type="submit" class="btn btn-secondary">
                    <i class="fas fa-key"></i> Change Password
                </button>
            </div>
        </form>
    </div>
</div>

<?php require_once 'footer.php'; ?>
