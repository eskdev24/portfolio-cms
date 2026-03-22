# Portfolio CMS - Eugene Simpson

A complete, production-ready Portfolio Content Management System built with Core PHP and MySQL.

## Features

- **Public Portfolio Website**
  - Responsive design with dark/light mode
  - Smooth animations and transitions
  - Blog system with search and pagination
  - Contact form with validation
  - Project filtering by category

- **Admin Dashboard**
  - Secure authentication with password hashing
  - Dashboard with statistics
  - CRUD operations for all content types
  - Image upload functionality
  - Message management

## Tech Stack

- **Frontend**: HTML5, CSS3 (Flexbox + Grid), Vanilla JavaScript
- **Backend**: Core PHP (OOP)
- **Database**: MySQL
- **Server**: Apache (XAMPP compatible)

## Folder Structure

```
/portfolio-cms
├── /admin                 # Admin dashboard
│   ├── dashboard.php
│   ├── login.php
│   ├── logout.php
│   ├── manage_projects.php
│   ├── manage_blog.php
│   ├── manage_skills.php
│   ├── manage_messages.php
│   ├── add_project.php
│   ├── edit_project.php
│   ├── add_post.php
│   └── edit_post.php
├── /assets
│   ├── /css
│   │   ├── main.css
│   │   ├── admin.css
│   │   └── animations.css
│   ├── /js
│   │   └── main.js
│   └── /images
├── /includes
│   ├── config.php
│   ├── db.php
│   ├── helpers.php
│   ├── auth.php
│   ├── header.php
│   └── footer.php
├── /uploads
│   ├── /projects
│   └── /blog
├── index.php
├── about.php
├── portfolio.php
├── blog.php
├── contact.php
├── database.sql
├── .htaccess
└── README.md
```

## Installation Instructions

### Prerequisites

- XAMPP (or similar PHP + MySQL stack)
- Web browser
- Code editor

### Step 1: Setup Database

1. Open XAMPP Control Panel
2. Start Apache and MySQL services
3. Click "Admin" next to MySQL to open phpMyAdmin

**Option A: Using phpMyAdmin**
1. Click "Import" tab
2. Choose the `database.sql` file from this project
3. Click "Go" to import

**Option B: Using MySQL Command Line**
```bash
mysql -u root -p
CREATE DATABASE portfolio_cms;
USE portfolio_cms;
SOURCE path/to/database.sql;
```

### Step 2: Configure Database Connection

Edit `includes/config.php` if needed:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_cms');
define('DB_USER', 'root');
define('DB_PASS', '');
```

### Step 3: Update Admin Password

The default admin password is `admin123`. You should change this:

1. Go to phpMyAdmin
2. Navigate to `portfolio_cms` > `users` table
3. Edit the admin user and replace the password with a bcrypt hash

Or update via SQL:
```sql
UPDATE users SET password = '$2y$10$YourNewHashedPasswordHere' WHERE username = 'admin';
```

Generate a new password hash using PHP:
```php
echo password_hash('your_new_password', PASSWORD_BCRYPT);
```

### Step 4: Set Folder Permissions

Ensure the uploads folder is writable:
```bash
chmod 755 uploads/
chmod 755 uploads/projects/
chmod 755 uploads/blog/
```

### Step 5: Access the Website

- **Website**: http://localhost:8080/portfolio-cms/
- **Admin Dashboard**: http://localhost:8080/portfolio-cms/admin/
- **Admin Login**: http://localhost:8080/portfolio-cms/admin/login.php

## Default Admin Credentials

| Field | Value |
|-------|-------|
| Username | admin |
| Password | admin123 |

## Configuration

### Site Settings

Edit `includes/config.php`:

```php
define('SITE_NAME', 'Eugene Simpson');
define('SITE_TAGLINE', 'Web Developer & Designer');
define('SITE_URL', 'http://localhost:8080/portfolio-cms');

// Pagination
define('ITEMS_PER_PAGE', 6);
define('BLOG_PER_PAGE', 6);

// Upload settings
define('MAX_FILE_SIZE', 5242880); // 5MB
```

### Database Settings

| Setting | Default | Description |
|---------|---------|-------------|
| DB_HOST | localhost | MySQL server host |
| DB_NAME | portfolio_cms | Database name |
| DB_USER | root | MySQL username |
| DB_PASS | (empty) | MySQL password |

## Usage Guide

### Adding Projects

1. Log in to admin dashboard
2. Go to "Projects" > "Add Project"
3. Fill in the details:
   - Title (required)
   - Short Description (required)
   - Content (full details)
   - Client name
   - Project URL
   - GitHub URL
   - Technologies (comma-separated)
   - Upload image
   - Set status (Draft/Published)
   - Mark as Featured if needed
4. Click "Save Project"

### Creating Blog Posts

1. Go to "Blog Posts" > "Add Post"
2. Fill in:
   - Title (required)
   - Excerpt (required)
   - Full Content (HTML allowed)
   - Category
   - Tags
   - Featured Image
   - Status and Publish Date
3. Save the post

### Managing Skills

1. Go to "Skills"
2. Add new skills with:
   - Name
   - Category (Development/Design/Other)
   - Proficiency percentage
   - FontAwesome icon class

### Viewing Messages

1. Go to "Messages" in the admin dashboard
2. Click on a message to view details
3. Mark as replied or delete

## Security Features

- Password hashing with bcrypt
- Prepared statements (PDO) for all queries
- Input sanitization and validation
- SQL injection prevention
- XSS protection via output escaping
- Protected admin routes
- Session management

## Customization

### Changing Colors

Edit `assets/css/main.css` - :root variables:

```css
:root {
    --primary: #38bdf8;
    --bg-primary: #0f172a;
    /* ... other variables */
}
```

### Changing Fonts

Update Google Fonts import and font-family in CSS.

### Adding Social Links

Update settings in the database (`settings` table) or create an admin settings page.

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)
- Mobile browsers

## Troubleshooting

### Database Connection Error
- Verify MySQL is running
- Check database credentials in config.php
- Ensure database exists

### Images Not Uploading
- Check folder permissions (uploads/ should be 755)
- Verify PHP upload settings in php.ini
- Check error logs

### CSS/JS Not Loading
- Verify .htaccess is working
- Check file paths in header.php
- Clear browser cache

## License

This project is for demonstration purposes.

## Credits

- Font Awesome for icons
- Google Fonts for typography
- Unsplash for placeholder images (if used)

---

Built with care for professional portfolios.
