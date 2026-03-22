<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Skills';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db()->delete('skills', 'id = ?', [$id]);
    setFlash('success', 'Skill deleted successfully.');
    redirect(ADMIN_URL . '/manage_skills.php');
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $skill = db()->fetchOne("SELECT proficiency FROM skills WHERE id = ?", [$id]);
    if ($skill) {
        $newProficiency = $skill['proficiency'] >= 100 ? 50 : min($skill['proficiency'] + 10, 100);
        db()->update('skills', ['proficiency' => $newProficiency], 'id = :id', ['id' => $id]);
        setFlash('success', 'Skill proficiency updated.');
        redirect(ADMIN_URL . '/manage_skills.php');
    }
}

$skills = db()->fetchAll("SELECT * FROM skills ORDER BY category, sort_order ASC");
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_skill'])) {
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? 'development');
    $proficiency = (int)($_POST['proficiency'] ?? 50);
    $icon = sanitize($_POST['icon'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($name)) {
        $errors['name'] = 'Skill name is required';
    } else {
        db()->insert('skills', [
            'name' => $name,
            'slug' => generateSlug($name),
            'category' => $category,
            'proficiency' => min(100, max(0, $proficiency)),
            'icon' => $icon,
            'sort_order' => $sort_order
        ]);
        
        setFlash('success', 'Skill added successfully!');
        redirect(ADMIN_URL . '/manage_skills.php');
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_skill'])) {
    $id = (int)$_POST['id'];
    $name = sanitize($_POST['name'] ?? '');
    $category = sanitize($_POST['category'] ?? 'development');
    $proficiency = (int)($_POST['proficiency'] ?? 50);
    $icon = sanitize($_POST['icon'] ?? '');
    $sort_order = (int)($_POST['sort_order'] ?? 0);
    
    if (empty($name)) {
        $errors['name'] = 'Skill name is required';
    } else {
        db()->update('skills', [
            'name' => $name,
            'category' => $category,
            'proficiency' => min(100, max(0, $proficiency)),
            'icon' => $icon,
            'sort_order' => $sort_order
        ], 'id = :id', ['id' => $id]);
        
        setFlash('success', 'Skill updated successfully!');
        redirect(ADMIN_URL . '/manage_skills.php');
    }
}
?>
<?php require_once 'header.php'; ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Add New Skill</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; align-items: end;">
            <input type="hidden" name="add_skill" value="1">
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Skill Name *</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Category</label>
                <select name="category" class="form-select">
                    <option value="development">Development</option>
                    <option value="design">Design</option>
                    <option value="other">Other</option>
                </select>
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Proficiency (%)</label>
                <input type="number" name="proficiency" class="form-control" value="50" min="0" max="100">
            </div>
            <div class="form-group" style="margin-bottom: 0;">
                <label class="form-label">Icon (FontAwesome)</label>
                <input type="text" name="icon" class="form-control" placeholder="fab fa-html5">
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Skill
            </button>
        </form>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">All Skills</h3>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($skills)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-code"></i>
                </div>
                <h3 class="empty-state-title">No skills yet</h3>
                <p class="empty-state-text">Add your first skill using the form above.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Skill</th>
                        <th>Category</th>
                        <th>Proficiency</th>
                        <th>Icon</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td>
                                <a href="?toggle=<?php echo $skill['id']; ?>" title="Click to increment">
                                    <?php echo escape($skill['name']); ?>
                                </a>
                            </td>
                            <td style="text-transform: capitalize;"><?php echo escape($skill['category']); ?></td>
                            <td>
                                <div style="display: flex; align-items: center; gap: 0.5rem;">
                                    <div style="flex: 1; height: 8px; background: var(--bg-tertiary); border-radius: var(--radius-full); overflow: hidden;">
                                        <div style="width: <?php echo $skill['proficiency']; ?>%; height: 100%; background: var(--primary);"></div>
                                    </div>
                                    <span style="min-width: 40px; text-align: right;"><?php echo $skill['proficiency']; ?>%</span>
                                </div>
                            </td>
                            <td><i class="<?php echo escape($skill['icon'] ?? 'fas fa-code'); ?>"></i></td>
                            <td>
                                <div class="table-actions">
                                    <button type="button" class="btn btn-sm btn-icon btn-secondary" 
                                            onclick="editSkill(<?php echo htmlspecialchars(json_encode($skill)); ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <a href="?delete=<?php echo $skill['id']; ?>" class="btn btn-sm btn-icon btn-danger" onclick="return confirmDelete();">
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
            <h3 class="modal-title">Edit Skill</h3>
            <span class="modal-close" onclick="closeModal('editModal')">
                <i class="fas fa-times"></i>
            </span>
        </div>
        <div class="modal-body">
            <form action="" method="POST">
                <input type="hidden" name="update_skill" value="1">
                <input type="hidden" name="id" id="edit_id">
                <div class="form-group">
                    <label class="form-label">Skill Name *</label>
                    <input type="text" name="name" id="edit_name" class="form-control" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select name="category" id="edit_category" class="form-select">
                        <option value="development">Development</option>
                        <option value="design">Design</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Proficiency (%)</label>
                    <input type="number" name="proficiency" id="edit_proficiency" class="form-control" min="0" max="100">
                </div>
                <div class="form-group">
                    <label class="form-label">Icon (FontAwesome)</label>
                    <input type="text" name="icon" id="edit_icon" class="form-control" placeholder="fab fa-html5">
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" id="edit_sort_order" class="form-control">
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
function editSkill(skill) {
    document.getElementById('edit_id').value = skill.id;
    document.getElementById('edit_name').value = skill.name;
    document.getElementById('edit_category').value = skill.category;
    document.getElementById('edit_proficiency').value = skill.proficiency;
    document.getElementById('edit_icon').value = skill.icon || '';
    document.getElementById('edit_sort_order').value = skill.sort_order || 0;
    showModal('editModal');
}
</script>

<?php require_once 'footer.php'; ?>
