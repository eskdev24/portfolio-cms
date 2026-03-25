<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Blog Posts';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $post = db()->fetchOne("SELECT image FROM blog_posts WHERE id = ?", [$id]);
    
    if ($post && $post['image']) {
        deleteImage($post['image'], 'uploads/blog/');
    }
    
    db()->delete('blog_posts', 'id = ?', [$id]);
    setFlash('success', 'Post deleted successfully.');
    redirect(ADMIN_URL . '/manage_blog.php');
}

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$search = isset($_GET['search']) ? sanitize($_GET['search']) : '';

$whereClause = '1=1';
$params = [];

if ($status) {
    $whereClause .= " AND bp.status = ?";
    $params[] = $status;
}

if ($search) {
    $whereClause .= " AND (bp.title LIKE ? OR bp.content LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$totalPosts = db()->count('blog_posts bp', $whereClause, $params);
$pagination = paginate($totalPosts, ITEMS_PER_PAGE, $page);

$posts = db()->fetchAll(
    "SELECT bp.*, COALESCE(bp.author_name, u.full_name) as display_author, c.name as category_name 
     FROM blog_posts bp 
     LEFT JOIN users u ON bp.author_id = u.id 
     LEFT JOIN categories c ON bp.category_id = c.id 
     WHERE {$whereClause}
     ORDER BY bp.published_at DESC 
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
            <input type="text" name="search" placeholder="Search posts..." value="<?php echo escape($search); ?>">
        </form>
        
        <select class="form-select" style="width: auto;" onchange="location.href = '?status=' + this.value + '<?php echo $search ? '&search=' . escape($search) : ''; ?>';">
            <option value="">All Status</option>
            <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
            <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
        </select>
    </div>
    
    <a href="add_post.php" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add Post
    </a>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($posts)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-blog"></i>
                </div>
                <h3 class="empty-state-title">No posts found</h3>
                <p class="empty-state-text">Start by writing your first blog post.</p>
                <a href="add_post.php" class="btn btn-primary" style="margin-top: 1rem;">
                    <i class="fas fa-plus"></i> Add Post
                </a>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Image</th>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Category</th>
                        <th>Views</th>
                        <th>Status</th>
                        <th style="width: 150px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $post): ?>
                        <tr>
                            <td>
                                <?php if (!empty($post['image']) && file_exists(ROOT_PATH . 'uploads/blog/' . $post['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/blog/<?php echo escape($post['image']); ?>" alt="" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                                <?php else: ?>
                                    <div style="width: 50px; height: 50px; background: var(--bg-tertiary); border-radius: var(--radius-sm); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-image" style="color: var(--text-muted);"></i>
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>" style="font-weight: 500;">
                                    <?php echo escape(truncate($post['title'], 40)); ?>
                                </a>
                            </td>
                            <td><?php echo escape($post['display_author']); ?></td>
                            <td><?php echo escape($post['category_name'] ?? '-'); ?></td>
                            <td><?php echo $post['view_count']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <a href="edit_post.php?id=<?php echo $post['id']; ?>" class="btn btn-sm btn-icon btn-secondary" title="Edit">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="?delete=<?php echo $post['id']; ?>" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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
