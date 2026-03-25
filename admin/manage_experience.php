<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Experience & Education';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $type = $_GET['type'] ?? 'experience';
    
    db()->delete($type, 'id = ?', [$id]);
    setFlash('success', 'Item deleted successfully.');
    redirect(ADMIN_URL . '/manage_experience.php');
}

if (isset($_GET['toggle']) && is_numeric($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $type = $_GET['type'] ?? 'experience';
    
    $item = db()->fetchOne("SELECT status FROM {$type} WHERE id = ?", [$id]);
    if ($item) {
        $newStatus = $item['status'] === 'active' ? 'inactive' : 'active';
        db()->update($type, ['status' => $newStatus], 'id = :id', ['id' => $id]);
    }
    redirect(ADMIN_URL . '/manage_experience.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_experience'])) {
    $year = sanitize($_POST['year'] ?? '');
    $title = sanitize($_POST['title'] ?? '');
    $company = sanitize($_POST['company'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    
    if ($year && $title) {
        db()->insert('experience', [
            'year' => $year,
            'title' => $title,
            'company' => $company,
            'description' => $description,
            'status' => 'active',
            'sort_order' => (int)$_POST['sort_order']
        ]);
        setFlash('success', 'Experience added successfully!');
    }
    redirect(ADMIN_URL . '/manage_experience.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_education'])) {
    $year = sanitize($_POST['year'] ?? '');
    $degree = sanitize($_POST['degree'] ?? '');
    $school = sanitize($_POST['school'] ?? '');
    
    if ($year && $degree) {
        db()->insert('education', [
            'year' => $year,
            'degree' => $degree,
            'school' => $school,
            'status' => 'active',
            'sort_order' => (int)$_POST['sort_order']
        ]);
        setFlash('success', 'Education added successfully!');
    }
    redirect(ADMIN_URL . '/manage_experience.php');
}

$experiences = db()->fetchAll("SELECT * FROM experience ORDER BY sort_order ASC, id DESC");
$educations = db()->fetchAll("SELECT * FROM education ORDER BY sort_order ASC, id DESC");
?>
<?php require_once 'header.php'; ?>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Add Experience</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Year/Period *</label>
                    <input type="text" name="year" class="form-control" placeholder="e.g., 2020 - Present" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Title/Position *</label>
                    <input type="text" name="title" class="form-control" placeholder="e.g., Senior Web Developer" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Company *</label>
                    <input type="text" name="company" class="form-control" placeholder="e.g., Tech Company" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="Brief description of your role"></textarea>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
            </div>
            <button type="submit" name="add_experience" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Experience
            </button>
        </form>
    </div>
</div>

<div class="card" style="margin-bottom: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Add Education</h3>
    </div>
    <div class="card-body">
        <form action="" method="POST">
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Year/Period *</label>
                    <input type="text" name="year" class="form-control" placeholder="e.g., 2014 - 2016" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Degree/Certificate *</label>
                    <input type="text" name="degree" class="form-control" placeholder="e.g., Master's in Computer Science" required>
                </div>
                <div class="form-group">
                    <label class="form-label">School/University *</label>
                    <input type="text" name="school" class="form-control" placeholder="e.g., Stanford University" required>
                </div>
                <div class="form-group">
                    <label class="form-label">Sort Order</label>
                    <input type="number" name="sort_order" class="form-control" value="0">
                </div>
            </div>
            <button type="submit" name="add_education" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Education
            </button>
        </form>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Experience</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($experiences)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-briefcase"></i>
                    </div>
                    <p class="empty-state-text">No experience added yet.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Title</th>
                            <th>Company</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($experiences as $exp): ?>
                            <tr>
                                <td><?php echo escape($exp['year']); ?></td>
                                <td><?php echo escape($exp['title']); ?></td>
                                <td><?php echo escape($exp['company']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $exp['status']; ?>">
                                        <?php echo ucfirst($exp['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?toggle=<?php echo $exp['id']; ?>&type=experience" class="btn btn-sm btn-icon btn-secondary" title="<?php echo $exp['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-toggle-<?php echo $exp['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                        </a>
                                        <a href="?delete=<?php echo $exp['id']; ?>&type=experience" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Education</h3>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($educations)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-graduation-cap"></i>
                    </div>
                    <p class="empty-state-text">No education added yet.</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Year</th>
                            <th>Degree</th>
                            <th>School</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($educations as $edu): ?>
                            <tr>
                                <td><?php echo escape($edu['year']); ?></td>
                                <td><?php echo escape($edu['degree']); ?></td>
                                <td><?php echo escape($edu['school']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $edu['status']; ?>">
                                        <?php echo ucfirst($edu['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?toggle=<?php echo $edu['id']; ?>&type=education" class="btn btn-sm btn-icon btn-secondary" title="<?php echo $edu['status'] === 'active' ? 'Deactivate' : 'Activate'; ?>">
                                            <i class="fas fa-toggle-<?php echo $edu['status'] === 'active' ? 'on' : 'off'; ?>"></i>
                                        </a>
                                        <a href="?delete=<?php echo $edu['id']; ?>&type=education" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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
</div>

<style>
.status-badge.status-active {
    background: rgba(34, 197, 94, 0.1);
    color: #22c55e;
}
.status-badge.status-inactive {
    background: rgba(107, 114, 128, 0.1);
    color: #6b7280;
}
</style>

<?php require_once 'footer.php'; ?>
