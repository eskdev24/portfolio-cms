-- Portfolio CMS Database Schema
-- Database: portfolio_cms

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `portfolio_cms` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `portfolio_cms`;

-- --------------------------------------------------------
-- Table: users (Admin)
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `role` ENUM('admin', 'editor') DEFAULT 'admin',
  `avatar` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: categories
-- --------------------------------------------------------
CREATE TABLE `categories` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `description` TEXT,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: projects
-- --------------------------------------------------------
CREATE TABLE `projects` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(200) NOT NULL,
  `slug` VARCHAR(200) NOT NULL,
  `description` TEXT NOT NULL,
  `content` TEXT,
  `image` VARCHAR(255) DEFAULT NULL,
  `category_id` INT(11) DEFAULT NULL,
  `client` VARCHAR(100) DEFAULT NULL,
  `project_url` VARCHAR(255) DEFAULT NULL,
  `github_url` VARCHAR(255) DEFAULT NULL,
  `technologies` VARCHAR(255) DEFAULT NULL,
  `featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `projects_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: skills
-- --------------------------------------------------------
CREATE TABLE `skills` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL,
  `category` VARCHAR(50) DEFAULT 'development',
  `proficiency` INT(3) DEFAULT 50,
  `icon` VARCHAR(50) DEFAULT NULL,
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: blog_posts
-- --------------------------------------------------------
CREATE TABLE `blog_posts` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL,
  `excerpt` TEXT,
  `content` TEXT NOT NULL,
  `image` VARCHAR(255) DEFAULT NULL,
  `author_id` INT(11) DEFAULT 1,
  `category_id` INT(11) DEFAULT NULL,
  `tags` VARCHAR(255) DEFAULT NULL,
  `view_count` INT(11) DEFAULT 0,
  `featured` TINYINT(1) DEFAULT 0,
  `status` ENUM('draft', 'published') DEFAULT 'draft',
  `published_at` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `author_id` (`author_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `blog_posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `blog_posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: blog_comments
-- --------------------------------------------------------
CREATE TABLE `blog_comments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `post_id` INT(11) NOT NULL,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `comment` TEXT NOT NULL,
  `status` ENUM('pending', 'approved', 'spam') DEFAULT 'pending',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `blog_comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `blog_posts` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: messages (Contact Form)
-- --------------------------------------------------------
CREATE TABLE `messages` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) NOT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: testimonials
-- --------------------------------------------------------
CREATE TABLE `testimonials` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `position` VARCHAR(100) DEFAULT NULL,
  `company` VARCHAR(100) DEFAULT NULL,
  `avatar` VARCHAR(255) DEFAULT NULL,
  `content` TEXT NOT NULL,
  `rating` INT(1) DEFAULT 5,
  `status` ENUM('pending', 'approved') DEFAULT 'approved',
  `sort_order` INT(11) DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table: settings
-- --------------------------------------------------------
CREATE TABLE `settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL,
  `setting_value` TEXT,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- INSERT SAMPLE DATA
-- --------------------------------------------------------

-- Admin User (password: admin123)
INSERT INTO `users` (`username`, `email`, `password`, `full_name`, `role`) VALUES
('admin', 'eugene@portfolio.com', '$2y$10$zO14O/hZvd0fnf5Fm.4I1.EV9qG2s6CYXmgQv6MvjS7DdV.uFUfSu', 'Eugene Simpson', 'admin');

-- Categories
INSERT INTO `categories` (`name`, `slug`, `description`) VALUES
('Web Development', 'web-development', 'Full-stack web development projects'),
('Graphic Design', 'graphic-design', 'Creative design and branding projects'),
('UI/UX Design', 'ui-ux-design', 'User interface and experience design'),
('Mobile Apps', 'mobile-apps', 'Responsive and mobile-first applications');

-- Skills
INSERT INTO `skills` (`name`, `slug`, `category`, `proficiency`, `icon`, `sort_order`) VALUES
('HTML5/CSS3', 'html5-css3', 'development', 95, 'fab fa-html5', 1),
('JavaScript', 'javascript', 'development', 90, 'fab fa-js', 2),
('PHP', 'php', 'development', 85, 'fab fa-php', 3),
('MySQL', 'mysql', 'development', 88, 'fas fa-database', 4),
('React', 'react', 'development', 80, 'fab fa-react', 5),
('Node.js', 'nodejs', 'development', 75, 'fab fa-node-js', 6),
('WordPress', 'wordpress', 'development', 90, 'fab fa-wordpress', 7),
('Adobe Photoshop', 'adobe-photoshop', 'design', 92, 'fab fa-adobe', 8),
('Figma', 'figma', 'design', 88, 'fab fa-figma', 9),
('Adobe Illustrator', 'adobe-illustrator', 'design', 85, 'fab fa-adobe', 10);

-- Projects
INSERT INTO `projects` (`title`, `slug`, `description`, `content`, `image`, `category_id`, `client`, `project_url`, `github_url`, `technologies`, `featured`, `status`, `sort_order`) VALUES
('E-Commerce Platform', 'ecommerce-platform', 'A full-featured online store with payment integration and inventory management.', 'Detailed project description...', 'project-1.jpg', 1, 'RetailMax Inc.', 'https://example.com', 'https://github.com', 'PHP, MySQL, JavaScript, Bootstrap', 1, 'published', 1),
('Brand Identity System', 'brand-identity-system', 'Complete brand identity including logo, color palette, and guidelines.', 'Detailed project description...', 'project-2.jpg', 2, 'TechStart', NULL, NULL, 'Adobe CC, Figma', 1, 'published', 2),
('Mobile Banking App', 'mobile-banking-app', 'User-friendly mobile banking interface with secure authentication.', 'Detailed project description...', 'project-3.jpg', 4, 'FinanceFirst', 'https://example.com', NULL, 'React Native, Node.js', 1, 'published', 3),
('Dashboard Analytics', 'dashboard-analytics', 'Real-time analytics dashboard with data visualization.', 'Detailed project description...', 'project-4.jpg', 3, 'DataCorp', 'https://example.com', 'https://github.com', 'Vue.js, D3.js, Firebase', 0, 'published', 4),
('Restaurant Website', 'restaurant-website', 'Elegant website with online ordering and reservation system.', 'Detailed project description...', 'project-5.jpg', 1, 'Bistro Milano', 'https://example.com', NULL, 'WordPress, WooCommerce', 0, 'published', 5);

-- Blog Posts
INSERT INTO `blog_posts` (`title`, `slug`, `excerpt`, `content`, `image`, `author_id`, `category_id`, `tags`, `featured`, `status`, `published_at`) VALUES
('Building Modern Web Applications', 'building-modern-web-applications', 'Learn the best practices for building modern, scalable web applications from scratch.', '<p>Full article content here...</p>', 'blog-1.jpg', 1, 1, 'web development,tutorial,javascript', 1, 'published', '2026-03-01 10:00:00'),
('The Art of Web Design', 'the-art-of-web-design', 'Exploring the principles of effective web design and user experience.', '<p>Full article content here...</p>', 'blog-2.jpg', 1, 3, 'design,ui,ux', 1, 'published', '2026-02-15 14:30:00'),
('PHP 8.0 Features You Should Know', 'php-8-features', 'A comprehensive guide to the new features in PHP 8.0 and how to use them.', '<p>Full article content here...</p>', 'blog-3.jpg', 1, 1, 'php,programming', 0, 'published', '2026-01-20 09:00:00');

-- Testimonials
INSERT INTO `testimonials` (`name`, `position`, `company`, `content`, `rating`, `status`, `sort_order`) VALUES
('Sarah Johnson', 'CEO', 'TechStart', 'Eugene delivered an exceptional brand identity that perfectly captured our vision. Highly recommended!', 5, 'approved', 1),
('Michael Chen', 'Project Manager', 'RetailMax Inc.', 'Professional, creative, and detail-oriented. The e-commerce platform exceeded all expectations.', 5, 'approved', 2),
('Emily Davis', 'Marketing Director', 'FinanceFirst', 'Working with Eugene was a pleasure. He understood our needs and delivered on time.', 5, 'approved', 3);

-- Messages
INSERT INTO `messages` (`name`, `email`, `subject`, `phone`, `message`, `status`) VALUES
('John Smith', 'john@example.com', 'Project Inquiry', '+1 234 567 890', 'Hi Eugene, I saw your portfolio and would like to discuss a potential project...', 'read'),
('Lisa Brown', 'lisa@example.com', 'Collaboration Offer', NULL, 'Hello! I am interested in collaborating on a startup project...', 'unread');

-- Settings
INSERT INTO `settings` (`setting_key`, `setting_value`) VALUES
('site_name', 'esk.dev - Web Developer & Designer'),
('name', 'Eugene Simpson'),
('site_tagline', 'Creating Digital Experiences'),
('site_description', 'Professional portfolio of Eugene Simpson, a skilled web developer and graphic designer.'),
('email', 'eugene@portfolio.com'),
('phone', '+1 (555) 123-4567'),
('address', 'San Francisco, CA'),
('facebook_url', 'https://facebook.com'),
('twitter_url', 'https://twitter.com'),
('linkedin_url', 'https://linkedin.com'),
('github_url', 'https://github.com'),
('instagram_url', 'https://instagram.com');
