<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/helpers.php';
require_once '../includes/auth.php';

requireAdmin();

$pageTitle = 'Dashboard';

$stats = [
    'projects' => db()->count('projects', "status = 'published'"),
    'blog_posts' => db()->count('blog_posts', "status = 'published'"),
    'messages' => db()->count('messages'),
    'unread_messages' => db()->count('messages', "status = 'unread'"),
    'skills' => db()->count('skills'),
    'testimonials' => db()->count('testimonials', "status = 'approved'")
];

$recentProjects = db()->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM projects p 
     LEFT JOIN categories c ON p.category_id = c.id 
     ORDER BY p.created_at DESC 
     LIMIT 5"
);

$recentMessages = db()->fetchAll(
    "SELECT * FROM messages ORDER BY created_at DESC LIMIT 5"
);

$recentPosts = db()->fetchAll(
    "SELECT bp.*, COALESCE(bp.author_name, u.full_name) as display_author 
     FROM blog_posts bp 
     LEFT JOIN users u ON bp.author_id = u.id 
     ORDER BY bp.created_at DESC 
     LIMIT 5"
);
?>
<?php require_once 'header.php'; ?>

<div class="stat-cards">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-briefcase"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['projects']; ?></h3>
            <p>Projects</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-blog"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['blog_posts']; ?></h3>
            <p>Blog Posts</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-envelope"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['unread_messages']; ?></h3>
            <p>Unread Messages</p>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon error">
            <i class="fas fa-users"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $stats['testimonials']; ?></h3>
            <p>Testimonials</p>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem;">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Recent Projects</h3>
            <a href="manage_projects.php" class="btn btn-sm btn-secondary">
                View All
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentProjects)): ?>
                <div class="empty-state" style="padding: 2rem;">
                    <p class="empty-state-text">No projects yet</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentProjects as $project): ?>
                            <tr>
                                <td>
                                    <a href="edit_project.php?id=<?php echo $project['id']; ?>">
                                        <?php echo escape(truncate($project['title'], 30)); ?>
                                    </a>
                                </td>
                                <td><?php echo escape($project['category_name'] ?? '-'); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $project['status']; ?>">
                                        <?php echo ucfirst($project['status']); ?>
                                    </span>
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
            <h3 class="card-title">Recent Messages</h3>
            <a href="manage_messages.php" class="btn btn-sm btn-secondary">
                View All
            </a>
        </div>
        <div class="card-body" style="padding: 0;">
            <?php if (empty($recentMessages)): ?>
                <div class="empty-state" style="padding: 2rem;">
                    <p class="empty-state-text">No messages yet</p>
                </div>
            <?php else: ?>
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Subject</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentMessages as $message): ?>
                            <tr>
                                <td>
                                    <a href="manage_messages.php?view=<?php echo $message['id']; ?>">
                                        <?php echo escape($message['name']); ?>
                                    </a>
                                </td>
                                <td><?php echo escape(truncate($message['subject'] ?? '-', 25)); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $message['status']; ?>">
                                        <?php echo ucfirst($message['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="card" style="margin-top: 2rem;">
    <div class="card-header">
        <h3 class="card-title">Recent Blog Posts</h3>
        <a href="manage_blog.php" class="btn btn-sm btn-secondary">
            View All
        </a>
    </div>
    <div class="card-body" style="padding: 0;">
        <?php if (empty($recentPosts)): ?>
            <div class="empty-state" style="padding: 2rem;">
                <p class="empty-state-text">No blog posts yet</p>
            </div>
        <?php else: ?>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Author</th>
                        <th>Views</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($recentPosts as $post): ?>
                        <tr>
                            <td>
                                <a href="edit_post.php?id=<?php echo $post['id']; ?>">
                                    <?php echo escape(truncate($post['title'], 40)); ?>
                                </a>
                            </td>
                            <td><?php echo escape($post['display_author']); ?></td>
                            <td><?php echo $post['view_count']; ?></td>
                            <td>
                                <span class="status-badge status-<?php echo $post['status']; ?>">
                                    <?php echo ucfirst($post['status']); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once 'footer.php'; ?>
