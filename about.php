<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/helpers.php';

$pageTitle = 'About';

$skills = db()->fetchAll(
    "SELECT * FROM skills ORDER BY category, sort_order ASC"
);

$skillsByCategory = [];
foreach ($skills as $skill) {
    $category = $skill['category'] ?? 'development';
    if (!isset($skillsByCategory[$category])) {
        $skillsByCategory[$category] = [];
    }
    $skillsByCategory[$category][] = $skill;
}

$stats = [
    'projects' => db()->count('projects', "status = 'published'"),
    'clients' => 150,
    'experience' => 5,
    'awards' => 15
];

$experience = db()->fetchAll("SELECT * FROM experience WHERE status = 'active' ORDER BY sort_order ASC, id DESC");
$education = db()->fetchAll("SELECT * FROM education WHERE status = 'active' ORDER BY sort_order ASC, id DESC");
?>
<?php require_once 'includes/header.php'; ?>

<section class="hero" style="min-height: auto; padding-top: calc(var(--header-height) + 7rem); padding-bottom: 4rem;">
    <div class="container">
        <div class="about-grid">
            <div class="about-image">
                <div class="about-image-wrapper">
                    <?php $aboutImage = getSetting('about_image'); ?>
                    <?php if (!empty($aboutImage)): ?>
                        <img src="<?php echo SITE_URL; ?>/uploads/<?php echo escape($aboutImage); ?>" alt="Eugene Simpson" class="about-img" style="object-fit: cover;">
                    <?php else: ?>
                        <div class="about-img" style="background: linear-gradient(135deg, var(--bg-tertiary) 0%, var(--bg-secondary) 100%); display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-user" style="font-size: 8rem; color: var(--primary);"></i>
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
                <p>
                    Hello! I'm <?php echo escape(getSetting('name', 'Eugene Simpson')); ?>, a passionate web developer and graphic designer
                    based in <?php echo escape(getSetting('address', 'Accra, GH')); ?>. I specialize in creating
                    modern, responsive, and user-friendly digital experiences.
                </p>
                <p>
                    With over <?php echo $stats['experience']; ?> years of professional experience, I've had the privilege of working with
                    startups, agencies, and established businesses to bring their digital visions to life.
                    My approach combines technical expertise with creative problem-solving to deliver
                    solutions that not only meet but exceed expectations.
                </p>
                <p>
                    When I'm not coding or designing, you can find me exploring new technologies,
                    contributing to open-source projects, or sharing my knowledge through blog posts
                    and tutorials.
                </p>
                
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
                
                <div style="display: flex; gap: 1rem; margin-top: 2rem; flex-wrap: wrap;">
                    <a href="<?php echo SITE_URL; ?>/contact.php" class="btn btn-primary">
                        <i class="fas fa-paper-plane"></i> Hire Me
                    </a>
                    <?php $cvFile = getSetting('cv_file'); ?>
                    <?php if (!empty($cvFile)): ?>
                        <a href="<?php echo SITE_URL; ?>/uploads/<?php echo escape($cvFile); ?>" class="btn btn-secondary" download>
                            <i class="fas fa-download"></i> Download CV
                        </a>
                    <?php else: ?>
                        <a href="#" class="btn btn-secondary" onclick="alert('CV not uploaded yet. Please check back soon.'); return false;">
                            <i class="fas fa-download"></i> Download CV
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">My Skills</h2>
            <p class="section-subtitle">
                Technologies and tools I use to bring your ideas to life
            </p>
        </div>
        
        <?php if (!empty($skillsByCategory)): ?>
            <?php foreach ($skillsByCategory as $category => $categorySkills): ?>
                <div style="margin-bottom: 3rem;">
                    <h3 style="margin-bottom: 1.5rem; display: flex; align-items: center; gap: 0.75rem; justify-content: center;">
                        <i class="fas fa-<?php echo $category === 'design' ? 'palette' : 'code'; ?>" style="color: var(--primary);"></i>
                        <?php echo ucfirst($category); ?> Skills
                    </h3>
                    <div class="skills-grid">
                        <?php foreach ($categorySkills as $skill): ?>
                            <div class="skill-circle fade-in">
                                <svg viewBox="0 0 100 100">
                                    <circle class="skill-circle-bg" cx="50" cy="50" r="45"></circle>
                                    <circle class="skill-circle-progress" cx="50" cy="50" r="45" data-progress="<?php echo $skill['proficiency']; ?>"></circle>
                                </svg>
                                <div class="skill-circle-content">
                                    <?php if ($skill['icon']): ?>
                                        <div class="skill-circle-icon"><i class="<?php echo escape($skill['icon']); ?>"></i></div>
                                    <?php endif; ?>
                                    <span class="skill-circle-name"><?php echo escape($skill['name']); ?></span>
                                    <div class="skill-circle-percent"><?php echo $skill['proficiency']; ?>%</div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Experience & Education</h2>
            <p class="section-subtitle">
                My professional journey and educational background
            </p>
        </div>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 4rem;">
            <div>
                <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-briefcase" style="color: var(--primary);"></i>
                    Work Experience
                </h3>
                
                <?php foreach ($experience as $exp): ?>
                    <div class="fade-in" style="position: relative; padding-left: 2rem; padding-bottom: 2rem; border-left: 2px solid var(--border-color); margin-left: 0.5rem;">
                        <div style="position: absolute; left: -0.5rem; top: 0; width: 1rem; height: 1rem; background: var(--primary); border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: var(--primary);"><?php echo escape($exp['year']); ?></span>
                        <h4 style="margin: 0.5rem 0;"><?php echo escape($exp['title']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem; margin-bottom: 0.5rem;">
                            <i class="fas fa-building"></i> <?php echo escape($exp['company']); ?>
                        </p>
                        <p style="color: var(--text-secondary); font-size: 0.9375rem;">
                            <?php echo escape($exp['description']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div>
                <h3 style="margin-bottom: 2rem; display: flex; align-items: center; gap: 0.75rem;">
                    <i class="fas fa-graduation-cap" style="color: var(--primary);"></i>
                    Education
                </h3>
                
                <?php foreach ($education as $edu): ?>
                    <div class="fade-in" style="position: relative; padding-left: 2rem; padding-bottom: 2rem; border-left: 2px solid var(--border-color); margin-left: 0.5rem;">
                        <div style="position: absolute; left: -0.5rem; top: 0; width: 1rem; height: 1rem; background: var(--secondary); border-radius: 50%;"></div>
                        <span style="font-size: 0.875rem; color: var(--secondary);"><?php echo escape($edu['year']); ?></span>
                        <h4 style="margin: 0.5rem 0;"><?php echo escape($edu['degree']); ?></h4>
                        <p style="color: var(--text-muted); font-size: 0.875rem;">
                            <i class="fas fa-university"></i> <?php echo escape($edu['school']); ?>
                        </p>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<section class="section" style="background: var(--bg-secondary);">
    <div class="container">
        <div class="section-header">
            <h2 class="section-title">Statistics</h2>
            <p class="section-subtitle">
                Numbers that tell my story
            </p>
        </div>
        
        <div class="hero-stats" style="justify-content: center;">
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="<?php echo $stats['projects']; ?>">0</div>
                <div class="stat-label">Projects Completed</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="<?php echo $stats['clients']; ?>">0</div>
                <div class="stat-label">Happy Clients</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="<?php echo $stats['experience']; ?>">0</div>
                <div class="stat-label">Years Experience</div>
            </div>
            <div class="stat-item fade-in">
                <div class="stat-number" data-target="<?php echo $stats['awards']; ?>">0</div>
                <div class="stat-label">Awards Won</div>
            </div>
        </div>
    </div>
</section>

<section class="cta-section">
    <div class="container">
        <div class="cta-content">
            <h2>Let's Work Together</h2>
            <p>
                I'm always excited to connect with new people and discuss potential collaborations.
                Whether you have a project in mind or just want to say hello, I'd love to hear from you!
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
    const skillProgress = document.querySelectorAll('.skill-progress[data-progress]');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const progress = entry.target.dataset.progress;
                entry.target.style.width = progress + '%';
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });
    
    skillProgress.forEach(el => observer.observe(el));
});
</script>

<?php require_once 'includes/footer.php'; ?>
