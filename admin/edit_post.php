<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Edit Blog Post';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$post = db()->fetchOne("SELECT * FROM blog_posts WHERE id = ?", [$id]);

if (!$post) {
    setFlash('error', 'Post not found.');
    redirect(ADMIN_URL . '/manage_blog.php');
}

$categories = db()->fetchAll("SELECT * FROM categories ORDER BY name ASC");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = sanitize($_POST['title'] ?? '');
    $excerpt = sanitize($_POST['excerpt'] ?? '');
    $content = $_POST['content'] ?? '';
    $category_id = !empty($_POST['category_id']) ? (int)$_POST['category_id'] : null;
    $tags = sanitize($_POST['tags'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;
    $status = sanitize($_POST['status'] ?? 'draft');
    $published_at = !empty($_POST['published_at']) ? $_POST['published_at'] : null;
    
    if (empty($title)) {
        $errors['title'] = 'Title is required';
    }
    
    if (empty($excerpt)) {
        $errors['excerpt'] = 'Excerpt is required';
    }
    
    if (empty($errors)) {
        $data = [
            'title' => $title,
            'excerpt' => $excerpt,
            'content' => $content,
            'category_id' => $category_id,
            'tags' => $tags,
            'featured' => $featured,
            'status' => $status,
            'published_at' => $status === 'published' ? ($published_at ?: date('Y-m-d H:i:s')) : null
        ];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if ($post['image']) {
                deleteImage($post['image'], 'uploads/blog/');
            }
            $upload = uploadImage($_FILES['image'], 'uploads/blog/');
            if (isset($upload['filename'])) {
                $data['image'] = $upload['filename'];
            }
        }
        
        if (isset($_POST['remove_image'])) {
            if ($post['image']) {
                deleteImage($post['image'], 'uploads/blog/');
            }
            $data['image'] = null;
        }
        
        db()->update('blog_posts', $data, 'id = :id', ['id' => $id]);
        
        setFlash('success', 'Post updated successfully!');
        redirect(ADMIN_URL . '/manage_blog.php');
    }
}
?>
<?php require_once 'header.php'; ?>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Edit Blog Post</h3>
        <a href="manage_blog.php" class="btn btn-sm btn-secondary">
            <i class="fas fa-arrow-left"></i> Back
        </a>
    </div>
    
    <div class="card-body">
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Title *</label>
                    <input type="text" name="title" class="form-control" value="<?php echo escape($post['title']); ?>" required>
                    <?php if (isset($errors['title'])): ?>
                        <div class="form-error"><?php echo escape($errors['title']); ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category_id" class="form-select">
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?php echo $cat['id']; ?>" <?php echo $post['category_id'] == $cat['id'] ? 'selected' : ''; ?>>
                                <?php echo escape($cat['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Excerpt *</label>
                <textarea name="excerpt" class="form-control" rows="3" required><?php echo escape($post['excerpt']); ?></textarea>
                <?php if (isset($errors['excerpt'])): ?>
                    <div class="form-error"><?php echo escape($errors['excerpt']); ?></div>
                <?php endif; ?>
            </div>
            
            <div class="form-group">
                <label class="form-label">Content *</label>
                <div id="contentEditor"><?php echo $post['content']; ?></div>
                <input type="hidden" name="content" id="hiddenContent">
                <div class="form-hint">Full blog post content (use toolbar to format)</div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Tags</label>
                    <input type="text" name="tags" class="form-control" value="<?php echo escape($post['tags'] ?? ''); ?>" placeholder="web development, tutorial">
                </div>
                
                <div class="form-group">
                    <label class="form-label">Publish Date</label>
                    <input type="datetime-local" name="published_at" class="form-control" 
                           value="<?php echo $post['published_at'] ? date('Y-m-d\TH:i', strtotime($post['published_at'])) : ''; ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label class="form-label">Featured Image</label>
                <?php if ($post['image']): ?>
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
                        <?php echo $post['image'] ? 'Upload new image' : 'Click to upload image'; ?>
                    </div>
                    <input type="file" name="image" id="imageInput" accept="image/*" style="display: none;">
                </div>
                <div id="filePreview" style="display: none;"></div>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-select">
                        <option value="draft" <?php echo $post['status'] === 'draft' ? 'selected' : ''; ?>>Draft</option>
                        <option value="published" <?php echo $post['status'] === 'published' ? 'selected' : ''; ?>>Published</option>
                    </select>
                </div>
                
                <div class="form-group" style="display: flex; align-items: center; padding-top: 1.5rem;">
                    <div class="checkbox-wrapper">
                        <input type="checkbox" name="featured" id="featured" value="1" <?php echo $post['featured'] ? 'checked' : ''; ?>>
                        <label for="featured">Featured Post</label>
                    </div>
                </div>
            </div>
            
            <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Update Post
                </button>
                <a href="manage_blog.php" class="btn btn-secondary">
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

<link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
<script>
var quill = new Quill('#contentEditor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'align': [] }],
            ['link', 'image'],
            ['blockquote', 'code-block'],
            ['clean']
        ]
    },
    placeholder: 'Write your blog post content here...'
});

document.querySelector('form').addEventListener('submit', function() {
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
});
</script>

<style>
#contentEditor { height: 400px; }
.ql-toolbar { border-radius: var(--radius-md) var(--radius-md) 0 0; }
.ql-container { border-radius: 0 0 var(--radius-md) var(--radius-md); font-family: inherit; font-size: 1rem; }
</style>

<?php require_once 'footer.php'; ?>
