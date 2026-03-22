<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'Home';

$featuredProjects = db()->fetchAll(
    "SELECT p.*, c.name as category_name 
     FROM projects p 
     LEFT JOIN categories c ON p.category_id = c.id 
     WHERE p.featured = 1 AND p.status = 'published' 
     ORDER BY p.sort_order ASC 
     LIMIT 6"
);

$latestPosts = db()->fetchAll(
    "SELECT bp.*, u.full_name as author_name, c.name as category_name 
     FROM blog_posts bp 
     LEFT JOIN users u ON bp.author_id = u.id 
     LEFT JOIN categories c ON bp.category_id = c.id 
     WHERE bp.featured = 1 AND bp.status = 'published' 
     ORDER BY bp.published_at DESC 
     LIMIT 3"
);

$skills = db()->fetchAll(
    "SELECT * FROM skills ORDER BY sort_order ASC LIMIT 6"
);

$stats = [
    'projects' => db()->count('projects', "status = 'published'"),
    'clients' => 50,
    'experience' => 5,
    'awards' => 15
];

$testimonials = db()->fetchAll(
    "SELECT * FROM testimonials WHERE status = 'approved' ORDER BY sort_order ASC LIMIT 3"
);
?>
<?php require_once 'includes/header.php'; ?>

<section class="hero">
    <div class="container">
        <div class="hero-content">
            <div class="hero-text">
                <div class="hero-badge">
                    <i class="fas fa-check-circle"></i>
                    <span>Available for Freelance Work</span>
                </div>
                
                <h1 class="hero-title">
                    Hi, I'm <?php echo escape(getSetting('site_name', 'Eugene Simpson')); ?>
                    <br>
                    <span class="highlight">
                        <span class="typed-text"><?php echo escape(getSetting('hero_title', 'Web Developer')); ?></span>
                    </span>
                </h1>
                
                <?php $heroSubtitle = getSetting('hero_subtitle'); ?>
                <?php if ($heroSubtitle): ?>
                    <p class="hero-description"><?php echo nl2br(escape($heroSubtitle)); ?></p>
                <?php else: ?>
                    <p class="hero-description">
                        I create beautiful, functional, and user-centered digital experiences.
                        With <?php echo $stats['experience']; ?> years of experience in web development and design,
                        I bring ideas to life through clean code and thoughtful design.
                    </p>
                <?php endif; ?>
                
                <div class="hero-buttons">
                    <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-primary btn-lg">
                        <i class="fas fa-briefcase"></i> View My Work
                    </a>
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-outline btn-lg">
                        <i class="fas fa-paper-plane"></i> Get In Touch
                    </a>
                </div>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['projects']; ?>+</div>
                        <div class="stat-label">Projects Completed</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['clients']; ?>+</div>
                        <div class="stat-label">Happy Clients</div>
                    </div>
                    <div class="stat-item">
                        <div class="stat-number"><?php echo $stats['experience']; ?>+</div>
                        <div class="stat-label">Years Experience</div>
                    </div>
                </div>
            </div>
            
            <div class="hero-image">
                <div class="hero-image-wrapper">
                    <?php $heroImage = getSetting('hero_image'); ?>
                    <?php if (!empty($heroImage)): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($heroImage); ?>" alt="Eugene Simpson" class="hero-img" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="hero-img" style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 8rem; color: var(--text-muted);"></i>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" id="about-preview">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <div class="about-image-wrapper">
                    <?php $aboutImage = getSetting('about_image'); ?>
                    <?php if (!empty($aboutImage)): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($aboutImage); ?>" alt="About" class="about-img" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="about-img" style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-code" style="font-size: 6rem; color: var(--primary);"></i>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="experience-badge">
                    <div class="number"><?php echo $stats['experience']; ?>+</div>
                    <div class="text">Years<br>Experience</div>
                </div>
            </div>
            
            <div class="about-content">
                <h2>About Me</h2>
                <?php $aboutDesc = getSetting('about_description'); ?>
                <?php if ($aboutDesc): ?>
                    <?php echo nl2br(escape($aboutDesc)); ?>
                <?php else: ?>
                    <p>
                        I'm a passionate web developer and graphic designer based in <?php echo escape(getSetting('address', 'San Francisco')); ?>.
                        I specialize in creating modern, responsive websites and applications that provide
                        excellent user experiences.
                    </p>
                    <p>
                        My approach combines technical expertise with creative thinking to deliver
                        solutions that not only look great but also perform exceptionally well.
                    </p>
                <?php endif; ?>
                
                <div class="about-features">
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Clean & Modern Code</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Responsive Design</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>SEO Optimized</span>
                    </div>
                    <div class="feature-item">
                        <i class="fas fa-check-circle"></i>
                        <span>Fast Performance</span>
                    </div>
                </div>
                
                <a href="<?php echo SITE_URL; ?>/about.php" class="btn btn-primary">
                    <i class="fas fa-user"></i> Learn More
                </a>
            </div>
        </div>
    </div>
</section>

<section class="section" id="skills-preview" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Skills</h2>
            <p class="section-subtitle">
                Technologies and tools I use to bring your ideas to life
            </p>
        </div>
        
        <div class="skills-grid">
            <?php foreach ($skills as $skill): ?>
                <div class="skill-category">
                    <div class="skill-item">
                        <div class="skill-header">
                            <span class="skill-name"><?php echo escape($skill['name']); ?></span>
                            <span class="skill-percent"><?php echo $skill['proficiency']; ?>%</span>
                        </div>
                        <div class="skill-bar">
                            <div class="skill-progress" style="width: <?php echo $skill['proficiency']; ?>%;"></div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section" id="portfolio-preview">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Featured Projects</h2>
            <p class="section-subtitle">
                A selection of my recent work and personal projects
            </p>
        </div>
        
        <div class="projects-grid">
            <?php foreach ($featuredProjects as $project): ?>
                <article class="project-card fade-in">
                    <div class="project-image">
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-image" style="font-size: 4rem; color: var(--text-muted);"></i>
                        </div>
                        <div class="project-overlay">
                            <?php if ($project['project_url']): ?>
                                <a href="<?php echo escape($project['project_url']); ?>" target="_blank" class="btn btn-sm btn-primary">
                                    <i class="fas fa-external-link-alt"></i> View
                                </a>
                            <?php endif; ?>
                            <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-sm btn-secondary">
                                <i class="fas fa-info-circle"></i> Details
                            </a>
                        </div>
                    </div>
                    <div class="project-content">
                        <span class="project-category"><?php echo escape($project['category_name'] ?? 'General'); ?></span>
                        <h3 class="project-title"><?php echo escape($project['title']); ?></h3>
                        <p class="project-description"><?php echo escape(truncate($project['description'], 100)); ?></p>
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
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-th-large"></i> View All Projects
            </a>
        </div>
    </div>
</section>

<?php if (!empty($testimonials)): ?>
<section class="section testimonials-section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Client Testimonials</h2>
            <p class="section-subtitle">
                What my clients say about working with me
            </p>
        </div>
        
        <div class="testimonials-slider">
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card fade-in">
                    <p class="testimonial-content">"<?php echo escape($testimonial['content']); ?>"</p>
                    <div class="testimonial-author">
                        <div class="testimonial-avatar">
                            <?php echo strtoupper(substr($testimonial['name'], 0, 1)); ?>
                        </div>
                        <div class="testimonial-info">
                            <h4><?php echo escape($testimonial['name']); ?></h4>
                            <p><?php echo escape($testimonial['position']); ?><?php echo $testimonial['company'] ? ' at ' . escape($testimonial['company']) : ''; ?></p>
                            <div class="testimonial-rating">
                                <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                                    <i class="fas fa-star"></i>
                                <?php endfor; ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php if (!empty($latestPosts)): ?>
<section class="section" id="blog-preview">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Latest Blog Posts</h2>
            <p class="section-subtitle">
                Insights and tutorials from my development journey
            </p>
        </div>
        
        <div class="blog-grid">
            <?php foreach ($latestPosts as $post): ?>
                <article class="blog-card fade-in">
                    <div class="blog-image">
                        <div style="width: 100%; height: 100%; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-blog" style="font-size: 3rem; color: white;"></i>
                        </div>
                    </div>
                    <div class="blog-content">
                        <div class="blog-meta">
                            <span class="blog-category"><?php echo escape($post['category_name'] ?? 'General'); ?></span>
                            <span><i class="far fa-calendar"></i> <?php echo formatDate($post['published_at']); ?></span>
                        </div>
                        <h3 class="blog-title">
                            <a href="<?php echo SITE_URL; ?>/blog.php?slug=<?php echo escape($post['slug']); ?>">
                                <?php echo escape($post['title']); ?>
                            </a>
                        </h3>
                        <p class="blog-excerpt"><?php echo escape(truncate($post['excerpt'], 120)); ?></p>
                        <div class="blog-card-footer">
                            <span class="blog-views"><i class="far fa-eye"></i> <?php echo $post['view_count']; ?> views</span>
                            <a href="<?php echo SITE_URL; ?>/blog.php?slug=<?php echo escape($post['slug']); ?>" class="btn btn-sm btn-primary">
                                Read More
                            </a>
                        </div>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
        
        <div style="text-align: center; margin-top: 3rem;">
            <a href="<?php echo SITE_URL; ?>/blog.php" class="btn btn-secondary btn-lg">
                <i class="fas fa-blog"></i> View All Posts
            </a>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Ready to Start Your Project?</h2>
            <p>
                I'm always excited to take on new challenges and bring your ideas to life.
                Let's discuss how I can help you achieve your goals.
            </p>
            <div class="cta-buttons">
                <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-lg">
                    <i class="fas fa-paper-plane"></i> Get In Touch
                </a>
                <a href="<?php echo SITE_URL; ?>/portfolio.php" class="btn btn-outline btn-lg">
                    <i class="fas fa-briefcase"></i> View Portfolio
                </a>
            </div>
        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
