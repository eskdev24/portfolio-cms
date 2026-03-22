<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Add Project';

$categories = db()->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $slug = generateSlug($title);
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
            'slug' => $slug,
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
            $upload = uploadImage($_FILES['image'], 'uploads/projects/');
            if (isset($upload['filename'])) {
                $data['image'] = $upload['filename'];
            }
        }
        
        db()->insert('projects', $data);
        
        setFlash('success', 'Project added successfully!');
        redirect(ADMIN_URL . '/manage_projects.php');
    }
}
?>
<?php require_once 'header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Add New Project</h3>
        <a href="manage_projects.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo old('title'); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <div class="form-error"><?php echo escape($errors['title']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo old('category_id') == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Short Description *</label>
                <textarea name="description" class="form-control" rows="3" required><?php echo old('description'); ?></textarea>
                <div class="form-hint">Brief description shown in project cards (max 200 characters)</div>
                <?php if (isset($errors['description'])): ?>
                    <div class="form-error"><?php echo escape($errors['description']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Content</label>
                <textarea name="content" class="form-control" rows="10"><?php echo old('content'); ?></textarea>
                <div class="form-hint">Full project details</div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Client</label>
                    <input type="text" name="client" class="form-control" value="<?php echo old('client'); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Project URL</label>
                    <input type="url" name="project_url" class="form-control" value="<?php echo old('project_url'); ?>" placeholder="https://">
                </div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">GitHub URL</label>
                    <input type="url" name="github_url" class="form-control" value="<?php echo old('github_url'); ?>" placeholder="https://github.com/">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Technologies</label>
                    <input type="text" name="technologies" class="form-control" value="<?php echo old('technologies'); ?>" placeholder="PHP, MySQL, JavaScript">
                    <div class="form-hint">Comma-separated list</div>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Project Image</label>
                <div class="file-upload" id="fileUpload">
                    <div class="file-upload-icon">
                        <i class="fas fa-cloud-upload-alt"></i>
                    </div>
                    <div class="file-upload-text">
                        Click to upload or drag and drop
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
                    <input type="number" name="sort_order" class="form-control" value="<?php echo old('sort_order', 0); ?>">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo old('status') === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo old('status') === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <div class="checkbox-wrapper">
                    <input type="checkbox" name="featured" id="featured" value="1" <?php echo old('featured') ? 'checked' : ''; ?>>
                    <label for="featured">Featured Project</label>
                </div>
                <div class="form-hint">Featured projects appear on homepage</div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Save Project
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
