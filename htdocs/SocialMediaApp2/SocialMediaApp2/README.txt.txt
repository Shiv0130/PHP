================================================================================
                         DARK SOCIAL - README
================================================================================

Project: Dark Social Networking Application
Authors: Saishna
Year: 2025
Assignment: PHP Official Assignment 2025

================================================================================
                         TABLE OF CONTENTS
================================================================================

1. Project Overview
2. System Requirements
3. Installation Instructions
4. Database Setup
5. File Structure
6. Features
7. Security Implementation
8. Usage Guide
9. Troubleshooting
10. References

================================================================================
                         1. PROJECT OVERVIEW
================================================================================

Dark Social is a modern social networking web application built with PHP, 
featuring a dark-themed user interface. The application allows users to:
- Create and manage accounts
- Post updates with images
- Search and connect with other users
- Send direct messages
- Customize their profiles

The application emphasizes security with features like password hashing, 
CSRF protection, input validation, and secure file uploads.

================================================================================
                         2. SYSTEM REQUIREMENTS
================================================================================

Server Requirements:
- PHP 7.4 or higher (8.0+ recommended)
- MySQL 5.7 or higher / MariaDB 10.2 or higher
- Apache 2.4+ or Nginx 1.18+
- mod_rewrite enabled (for Apache)

PHP Extensions Required:
- PDO
- PDO_MySQL
- mbstring
- GD or Imagick (for image processing)
- OpenSSL
- session support

Browser Requirements:
- Modern browsers (Chrome 90+, Firefox 88+, Safari 14+, Edge 90+)
- JavaScript enabled

================================================================================
                         3. INSTALLATION INSTRUCTIONS
================================================================================

Step 1: Extract Files
---------------------------------------
Extract all files to your web server's document root:
- XAMPP: C:\xampp\htdocs\dark_social\
- WAMP: C:\wamp64\www\dark_social\
- Linux: /var/www/html/dark_social/

Step 2: Set Permissions
---------------------------------------
Ensure the uploads directory is writable:

Windows:
- Right-click 'uploads' folder > Properties > Security
- Grant 'Write' permissions to IUSR or appropriate user

Linux/Mac:
chmod 755 uploads/
chown www-data:www-data uploads/

Step 3: Configure Database Connection
---------------------------------------
Edit includes/db_connection.php:

$host = 'localhost';        // Database host
$db = 'social_network';     // Database name
$user = 'root';             // Database username
$pass = '';                 // Database password (set your password)
$charset = 'utf8mb4';

================================================================================
                         4. DATABASE SETUP
================================================================================

Step 1: Create Database
---------------------------------------
1. Open phpMyAdmin (http://localhost/phpmyadmin)
2. Click "New" to create a database
3. Name: social_network
4. Collation: utf8mb4_general_ci
5. Click "Create"

Step 2: Import Tables
---------------------------------------
Execute the following SQL to create required tables:

-- Users Table
CREATE TABLE `users` (
  `user_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(255) NOT NULL,
  `email` VARCHAR(255) NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `profile_picture` VARCHAR(255) DEFAULT NULL,
  `password_reset_token` VARCHAR(255) DEFAULT NULL,
  `password_reset_expires` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Posts Table
CREATE TABLE `posts` (
  `post_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `user_id` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `image_path` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Messages Table
CREATE TABLE `messages` (
  `message_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `sender_id` INT(11) NOT NULL,
  `receiver_id` INT(11) NOT NULL,
  `content` TEXT NOT NULL,
  `sent_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`receiver_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Notifications Table
CREATE TABLE `notifications` (
  `notification_id` INT(11) AUTO_INCREMENT PRIMARY KEY,
  `recipient_id` INT(11) NOT NULL,
  `sender_id` INT(11) DEFAULT NULL,
  `type` VARCHAR(50) NOT NULL,
  `content` TEXT NOT NULL,
  `read_status` BOOLEAN DEFAULT FALSE,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`recipient_id`) REFERENCES `users`(`user_id`) ON DELETE CASCADE,
  FOREIGN KEY (`sender_id`) REFERENCES `users`(`user_id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

Step 3: Create Default Profile Image
---------------------------------------
Place a default profile image at:
images/default_profile.png

================================================================================
                         5. FILE STRUCTURE
================================================================================

dark_social/
│
├── pages/                          # Main application pages
│   ├── login.php                   # User login
│   ├── register.php                # User registration
│   ├── dashboard.php               # Main dashboard
│   ├── profile.php                 # User profile view
│   ├── edit_profile.php            # Profile editing
│   ├── create_post.php             # Create new posts
│   ├── messages.php                # Messaging system
│   ├── search_users.php            # User search
│   ├── account_settings.php        # Account settings
│   ├── forgot_password.php         # Password recovery
│   ├── set_new_password.php        # Password reset
│   ├── logout.php                  # Logout handler
│   ├── ajax_notifications.php      # Notifications API
│   └── ajax_search_users.php       # Search API
│
├── includes/                       # Backend includes
│   └── db_connection.php           # Database connection
│
├── css/                            # Stylesheets
│   ├── style.css                   # Main stylesheet
│   └── search_suggestions.css      # Search styling
│
├── js/                             # JavaScript files
│   ├── password_toggle.js          # Password visibility
│   ├── image_preview.js            # Image preview
│   ├── live_search.js              # Live search
│   ├── chat_scroll.js              # Chat auto-scroll
│   └── app_interactivity.js        # Notifications
│
├── uploads/                        # User uploads (writable)
│   ├── (profile images)
│   └── (post images)
│
├── images/                         # Static images
│   └── default_profile.png         # Default avatar
│
└── README.txt                      # This file

================================================================================
                         6. FEATURES
================================================================================

User Authentication:
- Secure registration with password validation
- Login with email and password
- Password reset functionality
- Session management
- Multi-factor authentication ready

User Profiles:
- Customizable profile information
- Profile picture upload (JPG, PNG, GIF)
- View other users' profiles
- Edit personal information

Social Features:
- Create text posts with optional images
- View news feed with recent posts
- Search users by name or email
- Live search suggestions
- Direct messaging between users
- Real-time message display

Security Features:
- Password hashing (bcrypt)
- CSRF token protection
- SQL injection prevention (prepared statements)
- XSS protection (input sanitization)
- Secure file upload validation
- Session security (regeneration, timeout)
- HTTPS ready

================================================================================
                         7. SECURITY IMPLEMENTATION
================================================================================

Password Security:
- Passwords hashed using PASSWORD_DEFAULT (bcrypt)
- Minimum password strength validation
- Secure password reset with tokens

Input Validation:
- All user inputs validated and sanitized
- htmlspecialchars() used for output
- filter_var() for email validation
- File upload type and size restrictions

Database Security:
- Prepared statements (PDO) prevent SQL injection
- No direct concatenation of user input in queries
- Proper error handling without exposing details

Session Security:
- Session regeneration on login
- HTTPOnly and Secure cookie flags recommended
- Session timeout implementation
- CSRF tokens for state-changing operations

File Upload Security:
- File type validation (MIME and extension)
- File size limits (5MB)
- Unique filename generation
- Storage outside accessible paths recommended

================================================================================
                         8. USAGE GUIDE
================================================================================

Getting Started:
1. Navigate to http://localhost/dark_social/pages/register.php
2. Create a new account with valid email and password
3. Log in at http://localhost/dark_social/pages/login.php
4. Complete your profile by uploading a profile picture

Creating Posts:
1. Go to Dashboard
2. Click "Create Post"
3. Enter your content (required)
4. Optionally upload an image
5. Click "Post"

Messaging Users:
1. Search for users via "Search Users"
2. Click on a user's profile
3. Click "Message" button
4. Type your message and send

Managing Account:
1. Click "My Profile" to view your profile
2. Click "Edit Profile" to update information
3. Go to "Account Settings" to change password

================================================================================
                         9. TROUBLESHOOTING
================================================================================

Common Issues:

Issue: "Database connection failed"
Solution: Check db_connection.php credentials and ensure MySQL is running

Issue: "Upload failed" or "Permission denied"
Solution: Ensure uploads/ directory has write permissions (chmod 755)

Issue: Images not displaying
Solution: Check file paths are relative to page location (use ../ properly)

Issue: Session errors
Solution: Ensure session_start() is at the top of each page, check PHP session configuration

Issue: CSRF token errors
Solution: Ensure forms include the CSRF token hidden field and session is active

Issue: Styles not loading
Solution: Clear browser cache, check CSS file paths are correct

Issue: Database tables missing
Solution: Run the CREATE TABLE statements from Section 4

Issue: File upload size too large
Solution: Adjust php.ini settings:
  upload_max_filesize = 10M
  post_max_size = 10M

================================================================================
                         10. REFERENCES
================================================================================

Assignment References:
- Apeh, M., 2023. "Creating a Security Policy for SMEs"
  https://safeonlineafrica.com/the-step-by-step-guide-to-creating-a-security-policy-for-smes-2/

- Chawdhary, G., 2025. "Web Application Security Issues and Solutions"
  https://www.securitycompass.com/blog/web-application-security-issues-solutions/

- Dolson, J., 2010. "Processing Forms with PHP"
  https://www.joedolson.com/2007/02/processing-forms-with-php/

- Irwin, L., 2023. "What is CEO Fraud?"
  https://www.itgovernanceusa.com/blog/what-is-ceo-fraud-definition-examples-and-prevention

- Novikova, D., 2024. "How to Make a Social Media App"
  https://solveit.dev/blog/how-to-make-a-social-media-app

- Sanchhaya, 2025. "PHP Form Processing"
  https://www.geeksforgeeks.org/php/php-form-processing/

Documentation:
- PHP Manual: https://www.php.net/manual/
- PDO Documentation: https://www.php.net/manual/en/book.pdo.php
- MySQL Documentation: https://dev.mysql.com/doc/

================================================================================
                         SUPPORT & CONTACT
================================================================================

For technical support or questions about this project:
- Review the documentation in the PDF assignment file
- Check the inline code comments for detailed explanations
- Refer to the IT Security Policy (Question 2.2) for security guidelines

Project Repository: 
https://drive.google.com/file/d/1tf-65XRkPhV68WZgwOi94svBgX0dBQ-j/view?usp=sharing

================================================================================
                         LICENSE
================================================================================

This project is developed for educational purposes as part of an official
PHP assignment for 2025.

© 2025 Rishabh and Saishna. All rights reserved.

================================================================================
                         END OF README
================================================================================