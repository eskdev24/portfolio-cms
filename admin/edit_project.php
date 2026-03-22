<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Edit Project';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$project = db()->fetchOne("SELECT * FROM projects WHERE id = ?", [$id]);

if (!$project) {
    setFlash('error', 'Project not found.');
    redirect(ADMIN_URL . '/manage_projects.php');
}

$categories = db()->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $client = sanitize($_POST['client'] ?? '');
    $project_url = sanitize($_POST['project_url'] ?? '');
    $github_url = sanitize($_POST['github_url'] ?? '');
    $technologies = sanitize($_POST['technologies'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = sanitize($_POST['status'] ?? 'draft');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($description)) {
        $errors['description'] = 'Description is required';
    }
    
    if (empty($errors)) {
        $data = [
            'title' => $title,
            'description' => $description,
            'content' => $content,
            'category_id' => $category_id,
            'client' => $client,
            'project_url' => $project_url,
            'github_url' => $github_url,
            'technologies' => $technologies,
            'featured' => $featured,
            'status' => $status,
            'sort_order' => $sort_order
        ];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if ($project['image']) {
                deleteImage($project['image'], 'uploads/projects/');
            }
            $upload = uploadImage($_FILES['image'], 'uploads/projects/');
            if (isset($upload['filename'])) {
                $data['image'] = $upload['filename'];
            }
        }
        
        if (isset($_POST['remove_image'])) {
            if ($project['image']) {
                deleteImage($project['image'], 'uploads/projects/');
            }
            $data['image'] = null;
        }
        
        db()->update('projects', $data, 'id = :id', ['id' => $id]);
        
        setFlash('success', 'Project updated successfully!');
        redirect(ADMIN_URL . '/manage_projects.php');
    }
}
?>
<?php require_once 'header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Project</h3>
        <a href="manage_projects.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo escape($project['title']); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <div class="form-error"><?php echo escape($errors['title']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $project['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Short Description *</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo escape($project['description']); ?></textarea>
                <div class="form-hint">Brief description shown in project cards</div>
                <?php if (isset($errors['description'])): ?>
                    <div class="form-error"><?php echo escape($errors['description']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="10"><?php echo escape($project['content']); ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" value="<?php echo escape($project['client'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Project URL</label>
                    <input type="url" name="project_url" class="form-control" value="<?php echo escape($project['project_url'] ?? ''); ?>" placeholder="https://">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" value="<?php echo escape($project['github_url'] ?? ''); ?>" placeholder="https://github.com/">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Technologies</label>
                    <input type="text" name="technologies" class="form-control" value="<?php echo escape($project['technologies'] ?? ''); ?>" placeholder="PHP, MySQL, JavaScript">
                    <div class="form-hint">Comma-separated list</div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Project Image</label>
                <?php if ($project['image']): ?>
                    <div class="file-preview" style="margin-bottom: 1rem;">
                        <div style="width: 60px; height: 60px; background: var(--bg-tertiary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="color: var(--text-muted);"></i>
                        </div>
                        <div class="file-preview-info">
                            <div class="file-preview-name">Current Image</div>
                        </div>
                        <label style="display: flex; align-items: center; gap: 0.5rem; cursor: pointer;">
                            <input type="checkbox" name="remove_image" value="1"> Remove
                        </label>
                    </div>
                <?php endif; ?>
                <div class="file-upload" id="fileUpload">
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="file-upload-text">
                        Click to upload new image
                    </div>
                    <div class="file-upload-hint">
                        PNG, JPG, GIF, WEBP (Max 5MB)
                    </div>
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                </div>
                <div id="filePreview" style="display: none;"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="<?php echo (int)$project['sort_order']; ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo $project['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $project['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="featured" id="featured" value="1" <?php echo $project['featured'] ? 'checked' : ''; ?>>
                    <label for="featured">Featured Project</label>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Project
                </button>
                <a href="manage_projects.php" class="btn btn-secondary">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.getElementById('fileUpload').addEventListener('click', function() {
    document.getElementById('imageInput').click();
});

document.getElementById('imageInput').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const preview = document.getElementById('filePreview');
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.innerHTML = `
                <div class="file-preview">
                    <img src="${e.target.result}" alt="Preview">
                    <div class="file-preview-info">
                        <div class="file-preview-name">${file.name}</div>
                        <div class="file-preview-size">${(file.size / 1024).toFixed(2)} KB</div>
                    </div>
                </div>
            `;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    }
});
</script>

<?php require_once 'footer.php'; ?>
