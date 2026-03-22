<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Blog';

$slug = isset($_GET['slug']) ? sanitize($_GET['slug']) : null;

if ($slug) {
    $post = db()->fetchOne(
        "SELECT bp.*, u.full_name as author_name, c.name as category_name 
         FROM blog_posts bp 
         LEFT JOIN users u ON bp.author_id = u.id 
         LEFT JOIN categories c ON bp.category_id = c.id 
         WHERE bp.slug = ? AND bp.status = 'published'",
        [$slug]
    );
    
    if ($post) {
        db()->query(
            "UPDATE blog_posts SET view_count = view_count + 1 WHERE id = ?",
            [$post['id']]
        );
        
        $comments = db()->fetchAll(
            "SELECT * FROM blog_comments WHERE post_id = ? AND status = 'approved' ORDER BY created_at DESC",
            [$post['id']]
        );
        
        $pageTitle = $post['title'];
    }
}

if (!$slug) {
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    
    $whereClause = "bp.status = 'published'";
    $params = [];
    
    if ($search) {
        $searchTerm = '%' . $search . '%';
        $whereClause .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ? OR bp.content LIKE ? OR bp.tags LIKE ?)";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }
    
    $totalPosts = db()->count('blog_posts bp', $whereClause, $params);
    $pagination = paginate($totalPosts, BLOG_PER_PAGE, $page);
    
    $posts = db()->fetchAll(
        "SELECT bp.*, u.full_name as author_name, c.name as category_name 
         FROM blog_posts bp 
         LEFT JOIN users u ON bp.author_id = u.id 
         LEFT JOIN categories c ON bp.category_id = c.id 
         WHERE {$whereClause}
         ORDER BY bp.published_at DESC 
         LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
        $params
    );
}
?>
<?php require_once 'includes/header.php'; ?>

<?php if ($slug && $post): ?>
    <article class="single-post">
        <div class="container">
            <a href="<?php echo SITE_URL; ?>/blog.php" class="btn btn-secondary" style="margin-bottom: 1.5rem; display: inline-flex; align-items: center; gap: 0.5rem;">
                <i class="fas fa-arrow-left"></i> Back to Blog
            </a>
            
            <header class="single-post-header">
                <div class="single-post-meta">
                    <span><i class="far fa-calendar"></i> <?php echo formatDate($post['published_at']); ?></span>
                    <span><i class="far fa-user"></i> <?php echo escape($post['author_name']); ?></span>
                    <?php if ($post['category_name']): ?>
                        <span><i class="far fa-folder"></i> <?php echo escape($post['category_name']); ?></span>
                    <?php endif; ?>
                    <span><i class="far fa-eye"></i> <?php echo $post['view_count']; ?> views</span>
                </div>
                <h1 class="single-post-title"><?php echo escape($post['title']); ?></h1>
            </header>
            
            <div class="single-post-image">
                <?php if (!empty($post['image']) && file_exists(ROOT_PATH . 'uploads/blog/' . $post['image'])): ?>
                    <img src="<?php echo SITE_URL; ?>/uploads/blog/<?php echo escape($post['image']); ?>" alt="<?php echo escape($post['title']); ?>" style="width: 100%; height: 400px; object-fit: cover; border-radius: var(--radius-xl);">
                <?php else: ?>
                    <div style="width: 100%; height: 400px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center; border-radius: var(--radius-xl);">
                        <i class="fas fa-blog" style="font-size: 6rem; color: white;"></i>
                    </div>
                <?php endif; ?>
            </div>
            
            <div class="single-post-content">
                <?php echo $post['content']; ?>
            </div>
            
            <?php if ($post['tags']): ?>
                <div style="margin-top: 3rem; padding-top: 2rem; border-top: 1px solid var(--border-color);">
                    <h4 style="margin-bottom: 1rem;">Tags:</h4>
                    <div class="project-tags">
                        <?php foreach (explode(',', $post['tags']) as $tag): ?>
                            <span class="project-tag"><?php echo trim(escape($tag)); ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endif; ?>
            
            <div style="margin-top: 3rem;">
                <a href="<?php echo SITE_URL; ?>/blog.php" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Blog
                </a>
            </div>
        </div>
    </article>
    
    <section class="section" style="background: var(--bg-secondary);">
        <div class="container">
            <div class="comments-section">
                <div class="comments-tabs" style="display: flex; gap: 1rem; margin-bottom: 2rem; border-bottom: 2px solid var(--border-color); padding-bottom: 0;">
                    <button class="comment-tab active" data-tab="comments" style="background: none; border: none; padding: 1rem 1.5rem; font-size: 1rem; font-weight: 500; color: var(--text-secondary); cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="far fa-comments"></i> Comments (<?php echo count($comments); ?>)
                    </button>
                    <button class="comment-tab" data-tab="add-comment" style="background: none; border: none; padding: 1rem 1.5rem; font-size: 1rem; font-weight: 500; color: var(--text-secondary); cursor: pointer; border-bottom: 2px solid transparent; margin-bottom: -2px; transition: all 0.3s ease; display: flex; align-items: center; gap: 0.5rem;">
                        <i class="fas fa-plus"></i> Add Comment
                    </button>
                </div>
                
                <div id="comments-content" class="tab-content">
                    <?php if (!empty($comments)): ?>
                        <div class="comments-list">
                            <?php foreach ($comments as $comment): ?>
                                <div class="comment">
                                    <div class="comment-header">
                                        <span class="comment-author"><?php echo escape($comment['name']); ?></span>
                                        <span class="comment-date"><?php echo timeAgo($comment['created_at']); ?></span>
                                    </div>
                                    <p class="comment-content"><?php echo escape($comment['comment']); ?></p>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div style="text-align: center; padding: 3rem; color: var(--text-muted);">
                            <i class="far fa-comment-dots" style="font-size: 3rem; margin-bottom: 1rem;"></i>
                            <p>No comments yet. Be the first to comment!</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div id="add-comment-content" class="tab-content" style="display: none;">
                    <div class="comment-form" style="background: var(--bg-card); padding: 2rem; border-radius: var(--radius-xl);">
                        <form action="<?php echo SITE_URL; ?>/includes/process_comment.php" method="POST" id="commentForm">
                            <input type="hidden" name="post_id" value="<?php echo $post['id']; ?>">
                            <div class="form-row">
                                <div class="form-group">
                                    <label class="form-label">Name *</label>
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">Email *</label>
                                    <input type="email" name="email" class="form-control" required>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Comment *</label>
                                <textarea name="comment" class="form-control" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Submit Comment
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <style>
    .comment-tab.active {
        color: var(--primary);
        border-bottom-color: var(--primary);
    }
    .comment-tab:hover {
        color: var(--text-primary);
    }
    </style>
    
    <script>
    document.querySelectorAll('.comment-tab').forEach(tab => {
        tab.addEventListener('click', function() {
            document.querySelectorAll('.comment-tab').forEach(t => t.classList.remove('active'));
            this.classList.add('active');
            
            const tabName = this.dataset.tab;
            
            if (tabName === 'comments') {
                document.getElementById('comments-content').style.display = 'block';
                document.getElementById('add-comment-content').style.display = 'none';
            } else {
                document.getElementById('comments-content').style.display = 'none';
                document.getElementById('add-comment-content').style.display = 'block';
            }
        });
    });
    </script>

<?php else: ?>
    <section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
        <div class="container">
            <div class="section-header">
                <h1 class="section-title">Blog</h1>
                <p class="section-subtitle">
                    Insights, tutorials, and thoughts on web development
                </p>
            </div>
            
            <div class="blog-search">
                <form action="" method="GET" class="blog-search-form">
                    <i class="fas fa-search blog-search-icon"></i>
                    <input type="text" name="search" class="blog-search-input" placeholder="Search blog posts..." value="<?php echo escape($search); ?>">
                    <button type="submit" class="blog-search-btn">
                        <span>Search</span>
                    </button>
                </form>
            </div>
            
            <?php if (empty($posts)): ?>
                <div class="empty-state">
                    <div class="empty-state-icon">
                        <i class="fas fa-blog"></i>
                    </div>
                    <h3 class="empty-state-title">No posts found</h3>
                    <p class="empty-state-text">
                        <?php if ($search): ?>
                            Try adjusting your search terms.
                        <?php else: ?>
                            Blog posts will appear here once they are published.
                        <?php endif; ?>
                    </p>
                </div>
            <?php else: ?>
                <div class="blog-grid">
                    <?php foreach ($posts as $post): ?>
                        <article class="blog-card fade-in">
                            <div class="blog-image">
                                <?php if (!empty($post['image']) && file_exists(ROOT_PATH . 'uploads/blog/' . $post['image'])): ?>
                                    <img src="<?php echo SITE_URL; ?>/uploads/blog/<?php echo escape($post['image']); ?>" alt="<?php echo escape($post['title']); ?>">
                                <?php else: ?>
                                    <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-blog" style="font-size: 3rem; color: white;"></i>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="blog-content">
                                <div class="blog-meta">
                                    <?php if ($post['category_name']): ?>
                                        <span class="blog-category"><?php echo escape($post['category_name']); ?></span>
                                    <?php endif; ?>
                                    <span><i class="far fa-calendar"></i> <?php echo formatDate($post['published_at']); ?></span>
                                </div>
                                <h3 class="blog-title">
                                    <a href="?slug=<?php echo escape($post['slug']); ?>">
                                        <?php echo escape($post['title']); ?>
                                    </a>
                                </h3>
                                <p class="blog-excerpt"><?php echo escape(truncate($post['excerpt'], 150)); ?></p>
                                <div class="blog-card-footer">
                                    <span class="blog-views"><i class="far fa-eye"></i> <?php echo $post['view_count']; ?> views</span>
                                    <a href="?slug=<?php echo escape($post['slug']); ?>" class="btn btn-sm btn-primary">
                                        Read More
                                    </a>
                                </div>
                            </div>
                        </article>
                    <?php endforeach; ?>
                </div>
                
                <?php if ($pagination['total_pages'] > 1): ?>
                    <div class="pagination">
                        <?php if ($pagination['has_prev']): ?>
                            <a href="?page=<?php echo $pagination['prev_page']; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                                <i class="fas fa-chevron-left"></i>
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                            <?php if ($i == $pagination['current_page']): ?>
                                <span class="active"><?php echo $i; ?></span>
                            <?php elseif ($i <= 3 || $i > $pagination['total_pages'] - 2 || abs($i - $pagination['current_page']) <= 1): ?>
                                <a href="?page=<?php echo $i; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php elseif ($i == 4 || $i == $pagination['total_pages'] - 3): ?>
                                <span>...</span>
                            <?php endif; ?>
                        <?php endfor; ?>
                        
                        <?php if ($pagination['has_next']): ?>
                            <a href="?page=<?php echo $pagination['next_page']; ?><?php echo $search ? '&search=' . escape($search) : ''; ?>">
                                <i class="fas fa-chevron-right"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </section>
<?php endif; ?>

<style>
.single-post-content {
    font-size: 1.125rem;
    line-height: 1.8;
    color: var(--text-primary);
}

.single-post-content p {
    margin-bottom: 1.5rem;
}

.single-post-content h1,
.single-post-content h2,
.single-post-content h3,
.single-post-content h4,
.single-post-content h5,
.single-post-content h6 {
    margin-top: 2rem;
    margin-bottom: 1rem;
    line-height: 1.3;
    color: var(--text-primary);
}

.single-post-content h2 { font-size: 1.75rem; }
.single-post-content h3 { font-size: 1.5rem; }
.single-post-content h4 { font-size: 1.25rem; }

.single-post-content ul,
.single-post-content ol {
    margin-bottom: 1.5rem;
    padding-left: 1.5rem;
}

.single-post-content li {
    margin-bottom: 0.5rem;
}

.single-post-content blockquote {
    border-left: 4px solid var(--primary);
    padding: 1rem 1.5rem;
    margin: 1.5rem 0;
    background: var(--bg-secondary);
    border-radius: 0 var(--radius-md) var(--radius-md) 0;
    font-style: italic;
}

.single-post-content img {
    max-width: 100%;
    height: auto;
    border-radius: var(--radius-lg);
    margin: 1.5rem 0;
}

.single-post-content a {
    color: var(--primary);
    text-decoration: underline;
    text-underline-offset: 2px;
}

.single-post-content a:hover {
    color: var(--secondary);
}

.single-post-content code {
    background: var(--bg-tertiary);
    padding: 0.2rem 0.5rem;
    border-radius: var(--radius-sm);
    font-family: 'Fira Code', monospace;
    font-size: 0.9em;
}

.single-post-content pre {
    background: var(--bg-tertiary);
    padding: 1.5rem;
    border-radius: var(--radius-lg);
    overflow-x: auto;
    margin: 1.5rem 0;
}

.single-post-content pre code {
    background: none;
    padding: 0;
}

.single-post-content table {
    width: 100%;
    border-collapse: collapse;
    margin: 1.5rem 0;
}

.single-post-content table th,
.single-post-content table td {
    border: 1px solid var(--border-color);
    padding: 0.75rem;
    text-align: left;
}

.single-post-content table th {
    background: var(--bg-secondary);
    font-weight: 600;
}

.comment-toast {
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--radius-lg);
    color: white;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    box-shadow: var(--shadow-lg);
    transform: translateX(120%);
    transition: transform 0.4s ease;
    z-index: 9999;
    max-width: 350px;
}

.comment-toast.show {
    transform: translateX(0);
}

.comment-toast.success {
    background: linear-gradient(135deg, #22c55e 0%, #16a34a 100%);
}

.comment-toast.error {
    background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
}

.comment-toast i {
    font-size: 1.25rem;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    20%, 60% { transform: translateX(-5px); }
    40%, 80% { transform: translateX(5px); }
}

.comment-form.shake {
    animation: shake 0.4s ease;
}
</style>

<div id="commentToast" class="comment-toast">
    <i class="fas fa-check-circle" id="toastIcon"></i>
    <span id="toastMessage"></span>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const commentForm = document.getElementById('commentForm');
    const toast = document.getElementById('commentToast');
    const toastIcon = document.getElementById('toastIcon');
    const toastMessage = document.getElementById('toastMessage');
    
    function showToast(message, isSuccess) {
        toastMessage.textContent = message;
        toastIcon.className = isSuccess ? 'fas fa-check-circle' : 'fas fa-exclamation-circle';
        toast.className = 'comment-toast ' + (isSuccess ? 'success' : 'error');
        toast.classList.add('show');
        
        setTimeout(() => {
            toast.classList.remove('show');
        }, 4000);
    }
    
    if (commentForm) {
        commentForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            
            fetch(this.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    showToast(data.message, true);
                    commentForm.reset();
                } else {
                    showToast(data.message || 'An error occurred', false);
                    commentForm.classList.add('shake');
                    setTimeout(() => commentForm.classList.remove('shake'), 400);
                }
            })
            .catch(error => {
                showToast('Failed to submit comment. Please try again.', false);
                commentForm.classList.add('shake');
                setTimeout(() => commentForm.classList.remove('shake'), 400);
            })
            .finally(() => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }
});
</script>

<?php require_once 'includes/footer.php'; ?>
