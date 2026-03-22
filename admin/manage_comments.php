<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Comments';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db()->delete('blog_comments', 'id = ?', [$id]);
    setFlash('success', 'Comment deleted successfully.');
    redirect(ADMIN_URL . '/manage_comments.php');
}

if (isset($_GET['approve']) && is_numeric($_GET['approve'])) {
    $id = (int)$_GET['approve'];
    db()->update('blog_comments', ['status' => 'approved'], 'id = :id', ['id' => $id]);
    setFlash('success', 'Comment approved.');
    redirect(ADMIN_URL . '/manage_comments.php');
}

if (isset($_GET['spam']) && is_numeric($_GET['spam'])) {
    $id = (int)$_GET['spam'];
    db()->update('blog_comments', ['status' => 'spam'], 'id = :id', ['id' => $id]);
    setFlash('success', 'Comment marked as spam.');
    redirect(ADMIN_URL . '/manage_comments.php');
}

$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$whereClause = '1=1';
$params = [];

if ($status) {
    $whereClause .= " AND c.status = ?";
    $params[] = $status;
}

$totalComments = db()->count('blog_comments c', $whereClause, $params);
$pagination = paginate($totalComments, ITEMS_PER_PAGE, $page);

$comments = db()->fetchAll(
    "SELECT c.*, bp.title as post_title 
     FROM blog_comments c 
     LEFT JOIN blog_posts bp ON c.post_id = bp.id 
     WHERE {$whereClause}
     ORDER BY c.created_at DESC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);

$stats = [
    'total' => db()->count('blog_comments'),
    'pending' => db()->count('blog_comments', "status = 'pending'"),
    'approved' => db()->count('blog_comments', "status = 'approved'"),
    'spam' => db()->count('blog_comments', "status = 'spam'")
];
?>
<?php require_once 'header.php'; ?>

<div class="stat-cards" style="margin-bottom: 2rem;">
    <div class="stat-card" onclick="location.href='?status='" style="cursor: pointer;">
        <div class="stat-content">
            <h3><?php echo $stats['total']; ?></h3>
            <p>Total Comments</p>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='?status=pending'" style="cursor: pointer;">
        <div class="stat-icon warning">
            <i class="fas fa-clock"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['pending']; ?></h3>
            <p>Pending</p>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='?status=approved'" style="cursor: pointer;">
        <div class="stat-icon success">
            <i class="fas fa-check"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['approved']; ?></h3>
            <p>Approved</p>
        </div>
    </div>
    <div class="stat-card" onclick="location.href='?status=spam'" style="cursor: pointer;">
        <div class="stat-icon error">
            <i class="fas fa-ban"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['spam']; ?></h3>
            <p>Spam</p>
        </div>
    </div>
</div>

<div class="filter-bar">
    <div class="filter-group">
        <select class="form-select" style="width: auto;" onchange="location.href = '?status=' + this.value;">
            <option value="">All Comments</option>
            <option value="pending" <?php echo $status === 'pending' ? 'selected' : ''; ?>>Pending</option>
            <option value="approved" <?php echo $status === 'approved' ? 'selected' : ''; ?>>Approved</option>
            <option value="spam" <?php echo $status === 'spam' ? 'selected' : ''; ?>>Spam</option>
        </select>
    </div>
</div>

<div class="card">
    <div class="card-body" style="padding: 0;">
        <?php if (empty($comments)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-comments"></i>
                </div>
                <h3 class="empty-state-title">No comments found</h3>
                <p class="empty-state-text">Comments from blog posts will appear here.</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Author</th>
                        <th>Comment</th>
                        <th>Post</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th style="width: 180px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($comments as $comment): ?>
                        <tr>
                            <td>
                                <strong><?php echo escape($comment['name']); ?></strong><br>
                                <small><a href="mailto:<?php echo escape($comment['email']); ?>"><?php echo escape($comment['email']); ?></a></small>
                            </td>
                            <td style="max-width: 300px;">
                                <?php echo escape(truncate($comment['comment'], 100)); ?>
                            </td>
                            <td>
                                <?php if ($comment['post_title']): ?>
                                    <a href="../blog.php?slug=<?php echo escape($comment['post_id']); ?>" target="_blank">
                                        <?php echo escape(truncate($comment['post_title'], 30)); ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($comment['created_at']); ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $comment['status']; ?>">
                                    <?php echo ucfirst($comment['status']); ?>
                                </span>
                            </td>
                            <td>
                                <div class="table-actions">
                                    <?php if ($comment['status'] === 'pending'): ?>
                                        <a href="?approve=<?php echo $comment['id']; ?>" class="btn btn-sm btn-icon btn-success" title="Approve">
                                            <i class="fas fa-check"></i>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($comment['status'] !== 'spam'): ?>
                                        <a href="?spam=<?php echo $comment['id']; ?>" class="btn btn-sm btn-icon btn-warning" title="Mark as Spam">
                                            <i class="fas fa-ban"></i>
                                        </a>
                                    <?php endif; ?>
                                    <a href="?delete=<?php echo $comment['id']; ?>" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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
                    <a href="?page=<?php echo $pagination['prev_page']; ?><?php echo $status ? '&status=' . escape($status) : ''; ?>">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                <?php endif; ?>
                
                <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                    <?php if ($i == $pagination['current_page']): ?>
                        <span class="active"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="?page=<?php echo $i; ?><?php echo $status ? '&status=' . escape($status) : ''; ?>">
                            <?php echo $i; ?>
                        </a>
                    <?php endif; ?>
                <?php endfor; ?>
                
                <?php if ($pagination['has_next']): ?>
                    <a href="?page=<?php echo $pagination['next_page']; ?><?php echo $status ? '&status=' . escape($status) : ''; ?>">
                        <i class="fas fa-chevron-right"></i>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once 'footer.php'; ?>
