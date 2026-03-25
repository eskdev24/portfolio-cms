<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Portfolio';

$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$category = isset($_GET['category']) ? sanitize($_GET['category']) : 'all';

$categories = db()->fetchAll("SELECT * FROM categories ORDER BY name ASC");

$whereClause = "status = 'published'";
$params = [];

if ($category !== 'all' && $category) {
    $whereClause .= " AND c.slug = ?";
    $params[] = $category;
}

$totalProjects = db()->count('projects p LEFT JOIN categories c ON p.category_id = c.id', $whereClause, $params);
$pagination = paginate($totalProjects, ITEMS_PER_PAGE, $page);

$projects = db()->fetchAll(
    "SELECT p.*, c.name as category_name, c.slug as category_slug 
     FROM projects p 
     LEFT JOIN categories c ON p.category_id = c.id 
     WHERE {$whereClause}
     ORDER BY p.sort_order ASC 
     LIMIT {$pagination['per_page']} OFFSET {$pagination['offset']}",
    $params
);
?>
<?php require_once 'includes/header.php'; ?>

<section class="section" style="padding-top: calc(var(--header-height) + 3rem);">
    <div class="container">
        <div class="section-header">
            <h1 class="section-title">My Portfolio</h1>
            <p class="section-subtitle">
                A showcase of my best work and personal projects
            </p>
        </div>
        
        <div class="projects-filters">
            <button class="filter-btn <?php echo $category === 'all' ? 'active' : ''; ?>" data-filter="all">
                All Projects
            </button>
            <?php foreach ($categories as $cat): ?>
                <button class="filter-btn <?php echo $category === $cat['slug'] ? 'active' : ''; ?>" 
                        data-filter="<?php echo escape($cat['slug']); ?>">
                    <?php echo escape($cat['name']); ?>
                </button>
            <?php endforeach; ?>
        </div>
        
        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-folder-open"></i>
                </div>
                <h3 class="empty-state-title">No projects found</h3>
                <p class="empty-state-text">Projects will appear here once they are added.</p>
            </div>
        <?php else: ?>
            <div class="projects-grid" id="projectsGrid">
                <?php foreach ($projects as $project): ?>
                    <article class="project-card fade-in" data-category="<?php echo escape($project['category_slug'] ?? ''); ?>">
                        <div class="project-image">
                            <?php if (!empty($project['image']) && file_exists(ROOT_PATH . 'uploads/projects/' . $project['image'])): ?>
                                <img src="<?php echo SITE_URL; ?>/uploads/projects/<?php echo escape($project['image']); ?>" alt="<?php echo escape($project['title']); ?>">
                            <?php else: ?>
                                <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image" style="font-size: 4rem; color: var(--text-muted);"></i>
                                </div>
                            <?php endif; ?>
                            <div class="project-overlay">
                                <?php if ($project['project_url']): ?>
                                    <a href="<?php echo escape($project['project_url']); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-primary">
                                        <i class="fas fa-external-link-alt"></i> Live Demo
                                    </a>
                                <?php endif; ?>
                                <?php if ($project['github_url']): ?>
                                    <a href="<?php echo escape($project['github_url']); ?>" target="_blank" rel="noopener" class="btn btn-sm btn-secondary">
                                        <i class="fab fa-github"></i> Source
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="project-content">
                            <span class="project-category"><?php echo escape($project['category_name'] ?? 'General'); ?></span>
                            <h3 class="project-title"><?php echo escape($project['title']); ?></h3>
                            <p class="project-description"><?php echo escape(truncate($project['description'], 120)); ?></p>
                            <?php if ($project['technologies']): ?>
                                <div class="project-tags">
                                    <?php foreach (explode(',', $project['technologies']) as $tech): ?>
                                        <span class="project-tag"><?php echo trim(escape($tech)); ?></span>
                                    <?php endforeach; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <?php if ($pagination['total_pages'] > 1): ?>
                <div class="pagination">
                    <?php if ($pagination['has_prev']): ?>
                        <a href="?page=<?php echo $pagination['prev_page']; ?><?php echo $category !== 'all' ? '&category=' . escape($category) : ''; ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    <?php endif; ?>
                    
                    <?php for ($i = 1; $i <= $pagination['total_pages']; $i++): ?>
                        <?php if ($i == $pagination['current_page']): ?>
                            <span class="active"><?php echo $i; ?></span>
                        <?php elseif ($i <= 3 || $i > $pagination['total_pages'] - 2 || abs($i - $pagination['current_page']) <= 1): ?>
                            <a href="?page=<?php echo $i; ?><?php echo $category !== 'all' ? '&category=' . escape($category) : ''; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php elseif ($i == 4 || $i == $pagination['total_pages'] - 3): ?>
                            <span>...</span>
                        <?php endif; ?>
                    <?php endfor; ?>
                    
                    <?php if ($pagination['has_next']): ?>
                        <a href="?page=<?php echo $pagination['next_page']; ?><?php echo $category !== 'all' ? '&category=' . escape($category) : ''; ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Have a Project in Mind?</h2>
            <p>
                I'm always looking for new challenges and exciting projects to work on.
                Let's create something amazing together!
            </p>
            <div class="cta-buttons">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-lg">
                    <i class="fas fa-paper-plane"></i> Get In Touch
                </a>
            </div>
        </div>
    </div>
</section>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const filterBtns = document.querySelectorAll('.filter-btn');
    const projectsGrid = document.getElementById('projectsGrid');
    const projectCards = document.querySelectorAll('.project-card');
    
    filterBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const filter = this.dataset.filter;
            
            filterBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            projectCards.forEach(card => {
                const category = card.dataset.category;
                
                if (filter === 'all' || category === filter) {
                    card.style.display = 'block';
                    setTimeout(() => card.classList.add('visible'), 50);
                } else {
                    card.classList.remove('visible');
                    setTimeout(() => card.style.display = 'none', 300);
                }
            });
        });
    });
});
</script>

<?php require_once 'includes/footer.php'; ?>
