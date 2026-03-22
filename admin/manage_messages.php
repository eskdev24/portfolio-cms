<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Manage Messages';

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    db()->delete('messages', 'id = ?', [$id]);
    setFlash('success', 'Message deleted successfully.');
    redirect(ADMIN_URL . '/manage_messages.php');
}

if (isset($_GET['mark_read']) && is_numeric($_GET['mark_read'])) {
    $id = (int)$_GET['mark_read'];
    db()->update('messages', ['status' => 'read'], 'id = :id', ['id' => $id]);
    setFlash('success', 'Message marked as read.');
    redirect(ADMIN_URL . '/manage_messages.php');
}

if (isset($_GET['view']) && is_numeric($_GET['view'])) {
    $id = (int)$_GET['view'];
    $message = db()->fetchOne("SELECT * FROM messages WHERE id = ?", [$id]);
    
    if ($message) {
        if ($message['status'] === 'unread') {
            db()->update('messages', ['status' => 'read'], 'id = :id', ['id' => $id]);
            $message['status'] = 'read';
        }
    }
}

$view = isset($_GET['view']) ? (int)$_GET['view'] : 0;
$message = $view ? db()->fetchOne("SELECT * FROM messages WHERE id = ?", [$view]) : null;

$status = isset($_GET['status']) ? sanitize($_GET['status']) : '';
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

$whereClause = '1=1';
$params = [];

if ($status) {
    $whereClause .= " AND status = ?";
    $params[] = $status;
}

if (!$view) {
    $totalMessages = db()->count('messages', $whereClause, $params);
    $pagination = paginate($totalMessages, ITEMS_PER_PAGE, $page);
    
    $messages = db()->fetchAll(
        "SELECT * FROM messages WHERE {$whereClause} ORDER BY created_at DESC LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
        $params
    );
}

$stats = [
    'total' => db()->count('messages'),
    'unread' => db()->count('messages', "status = 'unread'"),
    'read' => db()->count('messages', "status = 'read'"),
    'replied' => db()->count('messages', "status = 'replied'")
];
?>
<?php require_once 'header.php'; ?>

<?php if ($message): ?>
    <div class="card" style="margin-bottom: 2rem;">
        <div class="card-header">
            <h3 class="card-title">Message from <?php echo escape($message['name']); ?></h3>
            <div style="display: flex; gap: 0.5rem; flex-wrap: wrap;">
                <?php if ($message['status'] !== 'replied'): ?>
                    <a href="?view=<?php echo $message['id']; ?>&action=reply" class="btn btn-sm btn-success">
                        <i class="fas fa-reply"></i> Mark as Replied
                    </a>
                <?php endif; ?>
                <a href="?delete=<?php echo $message['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirmDelete();">
                    <i class="fas fa-trash"></i> Delete
                </a>
                <a href="manage_messages.php" class="btn btn-sm btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>
        <div class="card-body">
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1.5rem; margin-bottom: 2rem;">
                <div>
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">From</label>
                    <p style="font-weight: 500; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo escape($message['name']); ?>"><?php echo escape($message['name']); ?></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Email</label>
                    <p style="margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                        <a href="mailto:<?php echo escape($message['email']); ?>" title="<?php echo escape($message['email']); ?>" style="display: block; overflow: hidden; text-overflow: ellipsis;"><?php echo escape($message['email']); ?></a>
                    </p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Phone</label>
                    <p style="margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo escape($message['phone'] ?? '-'); ?>"><?php echo escape($message['phone'] ?? '-'); ?></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Date</label>
                    <p style="margin: 0;"><?php echo formatDate($message['created_at'], 'M d, Y H:i'); ?></p>
                </div>
                <div>
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Status</label>
                    <p style="margin: 0;">
                        <span class="status-badge status-<?php echo $message['status']; ?>"><?php echo ucfirst($message['status']); ?></span>
                    </p>
                </div>
            </div>
            
            <?php if (!empty($message['subject'])): ?>
                <div style="margin-bottom: 1.5rem;">
                    <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.25rem;">Subject</label>
                    <p style="font-weight: 500; margin: 0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;" title="<?php echo escape($message['subject']); ?>"><?php echo escape($message['subject']); ?></p>
                </div>
            <?php endif; ?>
            
            <div>
                <label style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; display: block; margin-bottom: 0.5rem;">Message</label>
                <div style="background: var(--bg-secondary); padding: 1.5rem; border-radius: var(--radius-md); word-wrap: break-word; word-break: break-word;">
                    <?php echo nl2br(escape($message['message'])); ?>
                </div>
            </div>
            
            <?php if (isset($_GET['action']) && $_GET['action'] === 'reply'): ?>
                <div style="margin-top: 2rem;">
                    <a href="mailto:<?php echo escape($message['email']); ?>?subject=Re: <?php echo urlencode($message['subject'] ?? 'Your Message'); ?>" class="btn btn-primary">
                        <i class="fas fa-reply"></i> Reply via Email
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
<?php else: ?>
    <div class="stat-cards" style="margin-bottom: 2rem;">
        <div class="stat-card" onclick="location.href='?status='" style="cursor: pointer;">
            <div class="stat-content">
                <h3><?php echo $stats['total']; ?></h3>
                <p>Total Messages</p>
            </div>
        </div>
        <div class="stat-card" onclick="location.href='?status=unread'" style="cursor: pointer;">
            <div class="stat-icon warning">
                <i class="fas fa-envelope"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['unread']; ?></h3>
                <p>Unread</p>
            </div>
        </div>
        <div class="stat-card" onclick="location.href='?status=read'" style="cursor: pointer;">
            <div class="stat-icon primary">
                <i class="fas fa-envelope-open"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['read']; ?></h3>
                <p>Read</p>
            </div>
        </div>
        <div class="stat-card" onclick="location.href='?status=replied'" style="cursor: pointer;">
            <div class="stat-icon success">
                <i class="fas fa-check"></i>
            </div>
            <div class="stat-content">
                <h3><?php echo $stats['replied']; ?></h3>
                <p>Replied</p>
            </div>
        </div>
    </div>
    
    <div class="filter-bar">
        <div class="filter-group">
            <select class="form-select" style="width: auto;" onchange="location.href = '?status=' + this.value;">
                <option value="">All Messages</option>
                <option value="unread" <?php echo $status === 'unread' ? 'selected' : ''; ?>>Unread</option>
                <option value="read" <?php echo $status === 'read' ? 'selected' : ''; ?>>Read</option>
                <option value="replied" <?php echo $status === 'replied' ? 'selected' : ''; ?>>Replied</option>
            </select>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body" style="padding: 0;">
            <?php if (empty($messages)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-envelope"></i>
                    </div>
                    <h3 class="empty-state-title">No messages found</h3>
                    <p class="empty-state-text">Messages from your contact form will appear here.</p>
                </div>
            <?php else: ?>
                <table class="data-table" style="table-layout: fixed;">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Name</th>
                            <th style="width: 200px;">Email</th>
                            <th style="width: 150px;">Subject</th>
                            <th style="width: 100px;">Date</th>
                            <th style="width: 80px;">Status</th>
                            <th style="width: 100px;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($messages as $msg): ?>
                            <tr style="<?php echo $msg['status'] === 'unread' ? 'font-weight: 600;' : ''; ?>">
                                <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="?view=<?php echo $msg['id']; ?>" title="<?php echo escape($msg['name']); ?>">
                                        <?php echo escape(truncate($msg['name'], 20)); ?>
                                    </a>
                                </td>
                                <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <a href="mailto:<?php echo escape($msg['email']); ?>" title="<?php echo escape($msg['email']); ?>">
                                        <?php echo escape($msg['email']); ?>
                                    </a>
                                </td>
                                <td style="overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    <?php echo escape(truncate($msg['subject'] ?? '-', 25)); ?>
                                </td>
                                <td><?php echo formatDate($msg['created_at']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $msg['status']; ?>">
                                        <?php echo ucfirst($msg['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="table-actions">
                                        <a href="?view=<?php echo $msg['id']; ?>" class="btn btn-sm btn-icon btn-secondary" title="View">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="?delete=<?php echo $msg['id']; ?>" class="btn btn-sm btn-icon btn-danger" title="Delete" onclick="return confirmDelete();">
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
<?php endif; ?>

<?php require_once 'footer.php'; ?>
