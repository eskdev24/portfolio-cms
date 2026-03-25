# Portfolio CMS - esk.dev

A complete, production-ready Portfolio Content Management System built with Core PHP and MySQL for Eugene Simpson (Web Developer & Graphic Designer).

## Features

### Public Website
- **Home Page**: Hero section, skills (circular animated progress), featured projects, testimonials slider, blog preview, CTA
- **About Page**: About section, skills by category (development/design), experience & education, statistics
- **Portfolio**: Project filtering by category, responsive grid layout
- **Blog**: Search functionality, pagination, comments system, author support
- **Contact**: Contact form with validation, WhatsApp integration, Formspree notifications

### Admin Dashboard
- Secure authentication with password hashing + remember me
- Dashboard with statistics
- **Project Management**: Add/edit/delete with image upload
- **Blog Management**: Add/edit/delete with Quill rich text editor, custom author names
- **Skills Management**: Circular progress display
- **Testimonials Management**: With profile images
- **Experience & Education**: Dynamic management
- **Messages Management**: Contact form submissions
- **Comments Management**: Blog post comments
- **Settings**: Site name, favicon, hero/about images, CV, social links, Formspree, WhatsApp

### UI/UX Features
- Dark/light mode toggle
- Responsive design (mobile-first)
- Animated statistics counters
- Testimonials slider (20s auto-advance)
- Circular skill progress with animations
- Toast notifications for forms
- Custom logo design (esk.dev)
- Favicon support

## Tech Stack

- **Frontend**: HTML5, CSS3 (Flexbox + Grid), Vanilla JavaScript
- **Backend**: Core PHP (OOP)
- **Database**: MySQL
- **Server**: Apache (XAMPP compatible)

## Folder Structure

```
/portfolio-cms
├── /admin                     # Admin dashboard
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── settings.php
│   ├── manage_projects.php
│   ├── manage_blog.php
│   ├── manage_skills.php
│   ├── manage_testimonials.php
│   ├── manage_experience.php
│   ├── manage_messages.php
│   ├── manage_comments.php
│   ├── add_project.php
│   ├── edit_project.php
│   ├── add_post.php
│   └── edit_post.php
├── /assets
│   ├── /css
│   │   ├── main.css
│   │   ├── admin.css
│   │   └── animations.css
│   └── /js
│       └── main.js
├── /db
|   └── database.sql
├── /includes
│   ├── config.php
│   ├── db.php
│   ├── helpers.php
│   ├── auth.php
│   ├── header.php
│   ├── footer.php
│   ├── process_contact.php
│   └── process_comment.php
├── /uploads
│   ├── /projects
│   ├── /blog
│   ├── /testimonials
│   └── favicon.ico
├── index.php
├── about.php
├── portfolio.php
├── blog.php
├── contact.php
└── README.md
```

## Installation

### Prerequisites
- XAMPP (PHP 8.0+ + MySQL)
- Web browser
- Code editor

### Step 1: Setup Database
1. Start Apache and MySQL in XAMPP
2. Open phpMyAdmin (http://localhost/phpmyadmin)
3. Create database `portfolio_cms`
4. Import `database.sql`

### Step 2: Run Additional SQL
Execute these in phpMyAdmin for new features:

```sql
-- Blog author name
ALTER TABLE blog_posts ADD COLUMN author_name VARCHAR(100) DEFAULT NULL AFTER author_id;

-- Remember me token
ALTER TABLE users ADD COLUMN remember_token VARCHAR(255) DEFAULT NULL;
ALTER TABLE users ADD COLUMN remember_token_expires DATETIME DEFAULT NULL;

-- Testimonial images
ALTER TABLE testimonials ADD COLUMN image VARCHAR(255) DEFAULT NULL;

-- Experience & Education tables
CREATE TABLE IF NOT EXISTS experience (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(50) NOT NULL,
    title VARCHAR(150) NOT NULL,
    company VARCHAR(150) NOT NULL,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS education (
    id INT AUTO_INCREMENT PRIMARY KEY,
    year VARCHAR(50) NOT NULL,
    degree VARCHAR(150) NOT NULL,
    school VARCHAR(150) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS activity_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT DEFAULT NULL,
    action VARCHAR(100) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

### Step 3: Access
- **Website**: http://localhost/apps/portfolio-cms/
- **Admin**: http://localhost/apps/portfolio-cms/admin/
- **Login**: http://localhost/apps/portfolio-cms/admin/login.php

### Default Credentials
| Username | Password |
|----------|----------|
| admin | admin123 |

## Configuration

### Site Settings (Admin Panel)
Go to **Settings** in admin dashboard to configure:
- Your Name (Eugene Simpson)
- Site Name (esk.dev)
- Site Tagline
- Favicon
- Hero/About images
- CV download
- Social media links
- WhatsApp number
- Formspree endpoint

### Config File (includes/config.php)
```php
define('NAME', 'Eugene Simpson');
define('SITE_NAME', 'esk.dev');
define('SITE_URL', 'http://localhost/apps/portfolio-cms');
define('ADMIN_URL', SITE_URL . '/admin');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_cms');
define('DB_USER', 'root');
define('DB_PASS', '');
```

## Usage Guide

### Managing Experience & Education
1. Go to Admin → Experience & Education
2. Add work experience with year, title, company, description
3. Add education with year, degree, school
4. Set sort order to control display order
5. Toggle active/inactive status

### Blog Posts with Custom Authors
1. Go to Blog Posts → Add Post
2. Fill in title, excerpt, content (use Quill editor)
3. Optionally enter Author Name for guest posts
4. Upload featured image
5. Set status and publish date

### Testimonials with Images
1. Go to Testimonials
2. Add testimonial with profile image
3. Enter name, position, company, rating, content

### Skills (Circular Progress)
1. Go to Skills
2. Add skills with category (development/design)
3. Set proficiency percentage
4. Add FontAwesome icon class

## Integrations

### WhatsApp
1. Add WhatsApp number in Settings (with country code, no +)
2. Contact page shows "WhatsApp Me" button

### Formspree (Email Notifications)
1. Create account at formspree.io
2. Create new form
3. Copy endpoint URL
4. Paste in Settings → Formspree Endpoint

## Security

- Password hashing with bcrypt
- Prepared statements (PDO)
- Input sanitization & validation
- SQL injection prevention
- XSS protection
- Remember me with secure tokens
- Activity logging

## Customization

### Colors (assets/css/main.css)
```css
:root {
    --primary: #38bdf8;
    --secondary: #818cf8;
    /* ... */
}
```

## Browser Support
- Chrome, Firefox, Safari, Edge (latest)
- Mobile browsers

## License
This project was developed by Eugene Simpson a final year IT student of the University of Energy and Natural Resources. This is for demonstration and educational purposes but can be used at production level.
Contact me on +233 599 04 8888 or Email: eskdev34@gmail.com if interested in projects like these.

## Credits
- Font Awesome 6.4 for icons
- Google Fonts (Poppins, Inter) for typography
- Quill.js (Rich Text Editor) for blog editing
- Unsplash for placeholder images
- Xampp for testing locally
- All php developers in the world

---
Built with love for personal professional portfolio management.
