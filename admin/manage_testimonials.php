<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Testimonials';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db()->delete('testimonials', 'id = ?', [$id]);
    setFlash('success', 'Testimonial deleted successfully.');
    redirect(ADMIN_URL . '/manage_testimonials.php');
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $testimonial = db()->fetchOne("SELECT status FROM testimonials WHERE id = ?", [$id]);
    if ($testimonial) {
        $newStatus = $testimonial['status'] === 'approved' ? 'pending' : 'approved';
        db()->update('testimonials', ['status' => $newStatus], 'id = :id', ['id' => $id]);
        setFlash('success', 'Testimonial status updated.');
        redirect(ADMIN_URL . '/manage_testimonials.php');
    }
}

$testimonials = db()->fetchAll("SELECT * FROM testimonials ORDER BY sort_order ASC, created_at DESC");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_testimonial'])) {
    $name = sanitize($_POST['name'] ?? '');
    $position = sanitize($_POST['position'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    
    if (empty($name) || empty($content)) {
        $errors[] = 'Name and content are required.';
    } else {
        $data = [
            'name' => $name,
            'position' => $position,
            'company' => $company,
            'content' => $content,
            'rating' => min(5, max(1, $rating)),
            'status' => 'approved',
            'sort_order' => (int)$_POST['sort_order']
        ];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $upload = uploadImage($_FILES['image'], 'uploads/testimonials/');
            if (isset($upload['filename'])) {
                $data['image'] = $upload['filename'];
            }
        }
        
        db()->insert('testimonials', $data);
        
        setFlash('success', 'Testimonial added successfully!');
        redirect(ADMIN_URL . '/manage_testimonials.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_testimonial'])) {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name'] ?? '');
    $position = sanitize($_POST['position'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $content = sanitize($_POST['content'] ?? '');
    $rating = (int)($_POST['rating'] ?? 5);
    
    if (empty($name) || empty($content)) {
        $errors[] = 'Name and content are required.';
    } else {
        $data = [
            'name' => $name,
            'position' => $position,
            'company' => $company,
            'content' => $content,
            'rating' => min(5, max(1, $rating)),
            'sort_order' => (int)$_POST['sort_order']
        ];
        
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            if ($testimonial['image']) {
                deleteImage($testimonial['image'], 'uploads/testimonials/');
            }
            $upload = uploadImage($_FILES['image'], 'uploads/testimonials/');
            if (isset($upload['filename'])) {
                $data['image'] = $upload['filename'];
            }
        }
        
        if (isset($_POST['remove_image'])) {
            if ($testimonial['image']) {
                deleteImage($testimonial['image'], 'uploads/testimonials/');
            }
            $data['image'] = null;
        }
        
        db()->update('testimonials', $data, 'id = :id', ['id' => $id]);
        
        setFlash('success', 'Testimonial updated successfully!');
        redirect(ADMIN_URL . '/manage_testimonials.php');
    }
}
?>
<?php require_once 'header.php'; ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Add New Testimonial</h3>
    </div>
    <div class="card-body">
        <?php if (!empty($errors)): ?>
            <div class="alert alert-error">
                <i class="fas fa-exclamation-circle"></i>
                <span><?php echo escape(implode(', ', $errors)); ?></span>
            </div>
        <?php endif; ?>
        
        <form action="" method="POST" enctype="multipart/form-data">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-hint">JPG, PNG, GIF (Max 2MB)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Position</label>
                    <input type="text" name="position" class="form-control">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Company</label>
                    <input type="text" name="company" class="form-control">
                </div>
                <div class="form-group">
                    <label class="form-label">Rating</label>
                    <select name="rating" class="form-select">
                        <option value="5">5 Stars</option>
                        <option value="4">4 Stars</option>
                        <option value="3">3 Stars</option>
                        <option value="2">2 Stars</option>
                        <option value="1">1 Star</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Content *</label>
                <textarea name="content" class="form-control" rows="3" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary" name="add_testimonial" value="1">
                <i class="fas fa-plus"></i> Add Testimonial
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Testimonials</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($testimonials)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-quote-left"></i>
                </div>
                <h3 class="empty-state-title">No testimonials yet</h3>
                <p class="empty-state-text">Add your first testimonial using the form above.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Testimonial</th>
                        <th>Rating</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($testimonials as $testimonial): ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($testimonial['name']); ?></strong><br>
                                <small><?php echo escape($testimonial['position']); ?><?php echo $testimonial['company'] ? ' at ' . escape($testimonial['company']) : ''; ?></small>
                            </td>
                            <td style="max-width: 300px;">
                                <?php echo escape(truncate($testimonial['content'], 80)); ?>
                            </td>
                            <td>
                                <?php for ($i = 0; $i < 5; $i++): ?>
                                    <i class="fas fa-star" style="color: <?php echo $i < $testimonial['rating'] ? 'var(--warning)' : 'var(--text-muted)'; ?>;"></i>
                                <?php endfor; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $testimonial['status']; ?>">
                                    <?php echo ucfirst($testimonial['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn btn-sm btn-icon btn-secondary" 
                                            onclick="editTestimonial(<?php echo htmlspecialchars(json_encode($testimonial)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $testimonial['id']; ?>" class="btn btn-sm btn-icon btn-danger" onclick="return confirmDelete();">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="modal-overlay" id="editModal">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Edit Testimonial</h3>
            <span class="modal-close" onclick="closeModal('editModal')">
                <i class="fas fa-times"></i>
            </span>
        </div>
        <div class="modal-body">
            <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="update_testimonial" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label class="form-label">Profile Image</label>
                    <input type="file" name="image" class="form-control" accept="image/*">
                    <div class="form-hint">JPG, PNG, GIF (Max 2MB)</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Position</label>
                        <input type="text" name="position" id="edit_position" class="form-control">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Company</label>
                        <input type="text" name="company" id="edit_company" class="form-control">
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Content *</label>
                    <textarea name="content" id="edit_content" class="form-control" rows="3" required></textarea>
                </div>
                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Rating</label>
                        <select name="rating" id="edit_rating" class="form-select">
                            <option value="5">5 Stars</option>
                            <option value="4">4 Stars</option>
                            <option value="3">3 Stars</option>
                            <option value="2">2 Stars</option>
                            <option value="1">1 Star</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Sort Order</label>
                        <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
                    </div>
                </div>
                <div class="modal-footer" style="padding: 0; border: none; margin-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editModal')">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function editTestimonial(testimonial) {
    document.getElementById('edit_id').value = testimonial.id;
    document.getElementById('edit_name').value = testimonial.name;
    document.getElementById('edit_position').value = testimonial.position || '';
    document.getElementById('edit_company').value = testimonial.company || '';
    document.getElementById('edit_content').value = testimonial.content;
    document.getElementById('edit_rating').value = testimonial.rating;
    document.getElementById('edit_sort_order').value = testimonial.sort_order || 0;
    showModal('editModal');
}
</script>

<?php require_once 'footer.php'; ?>
