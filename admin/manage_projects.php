<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Projects';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $project = db()->fetchOne("SELECT image FROM projects WHERE id = ?", [$id]);
    
    if ($project && $project['image']) {
        deleteImage($project['image'], 'uploads/projects/');
    }
    
    db()->delete('projects', 'id = ?', [$id]);
    setFlash('success', 'Project deleted successfully.');
    redirect(ADMIN_URL . '/manage_projects.php');
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$whereClause = '1=1';
$params = [];

if ($status) {
    $whereClause .= " AND p.status = ?";
    $params[] = $status;
}

if ($search) {
    $whereClause .= " AND (p.title LIKE ? OR p.description LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$totalProjects = db()->count('projects p', $whereClause, $params);
$pagination = paginate($totalProjects, ITEMS_PER_PAGE, $page);

$projects = db()->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM projects p 
     LEFT JOIN categories c ON p.category_id = c.id 
     WHERE {$whereClause}
     ORDER BY p.sort_order ASC, p.created_at DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

$categories = db()->fetchAll("SELECT * FROM categories ORDER BY name ASC");
?>
<?php require_once 'header.php'; ?>

<div class="filter-bar">
    <div class="filter-group">
        <form action="" method="GET" class="search-box">
            <i class="fas fa-search"></i>
            <input type="text" name="search" placeholder="Search projects..." value="<?php echo escape($search); ?>">
        </form>
        
        <select class="form-select" style="width: auto;" onchange="location.href = '?status=' + this.value + '<?php echo $search ? '&search=' . escape($search) : ''; ?>';">
            <option value="">All Status</option>
            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
    </div>
    
    <a href="add_project.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Project
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-briefcase"></i>
                </div>
                <h3 class="empty-state-title">No projects found</h3>
                <p class="empty-state-text">Start by adding your first project.</p>
                <a href="add_project.php" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Add Project
                </a>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Featured</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($projects as $project): ?>
                        <tr>
                            <td>
                                <?php if (!empty($project['image']) && file_exists(ROOT_PATH . 'uploads/projects/' . $project['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/projects/<?php echo escape($project['image']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: var(--bg-tertiary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: var(--text-muted);"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_project.php?id=<?php echo $project['id']; ?>" style="font-weight: 500;">
                                    <?php echo escape($project['title']); ?>
                                </a>
                            </td>
                            <td><?php echo escape($project['category_name'] ?? '-'); ?></td>
                            <td>
                                <?php if ($project['featured']): ?>
                                    <i class="fas fa-star" style="color: var(--warning);"></i>
                                <?php else: ?>
                                    <i class="far fa-star" style="color: var(--text-muted);"></i>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="status-badge status-<?php echo $project['status']; ?>">
                                    <?php echo ucfirst($project['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="edit_project.php?id=<?php echo $project['id']; ?>" class="btn btn-sm btn-icon btn-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $project['id']; ?>" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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
    
    <?php if ($pagination['total_pages'] > 1): ?>
        <div class="card-footer">
            <div class="pagination">
                <?php if ($pagination['has_prev']): ?>
                    <a href="?page=<?php echo $pagination['prev_page']; ?><?php echo $status ? '&status=' . escape($status) : ''; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == $pagination['current_page']): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . escape($status) : ''; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pagination['has_next']): ?>
                    <a href="?page=<?php echo $pagination['next_page']; ?><?php echo $status ? '&status=' . escape($status) : ''; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
