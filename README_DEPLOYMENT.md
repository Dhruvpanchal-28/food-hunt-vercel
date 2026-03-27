# Food Hunt - Deployment Guide

## Overview
This guide will help you deploy your Food Hunt application to a live server with mobile responsiveness.

## Prerequisites

### Server Requirements
- PHP 7.1 or higher
- MySQL 5.7 or higher
- Apache web server with mod_rewrite enabled
- SSL certificate (recommended)
- At least 100MB disk space
- 512MB RAM minimum

### Required PHP Extensions
- mysqli
- gd
- json
- session
- fileinfo

## Step 1: Server Setup

### 1.1 Install Required Software
```bash
# On Ubuntu/Debian
sudo apt update
sudo apt install apache2 php php-mysqli php-gd php-json php-session

# On CentOS/RHEL
sudo yum install httpd php php-mysqli php-gd php-json
```

### 1.2 Configure Apache
Enable required modules:
```bash
sudo a2enmod rewrite
sudo a2enmod headers
sudo a2enmod deflate
sudo a2enmod expires
```

### 1.3 Create Virtual Host
Create a new Apache configuration file:
```apache
<VirtualHost *:80>
    ServerName yourdomain.com
    DocumentRoot /var/www/html/dbfood
    
    <Directory /var/www/html/dbfood>
        AllowOverride All
        Require all granted
    </Directory>
    
    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
```

## Step 2: Database Setup

### 2.1 Create Database
```sql
CREATE DATABASE dbfood CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'dbfood_user'@'localhost' IDENTIFIED BY 'strong_password';
GRANT ALL PRIVILEGES ON dbfood.* TO 'dbfood_user'@'localhost';
FLUSH PRIVILEGES;
```

### 2.2 Import Database Schema
```bash
mysql -u dbfood_user -p dbfood < localhost.sql
```

## Step 3: Application Configuration

### 3.1 Update Configuration
Edit `production_config.php`:
```php
// Update these values
define('DB_HOST', 'localhost');
define('DB_USER', 'dbfood_user');
define('DB_PASS', 'strong_password');
define('DB_NAME', 'dbfood');
define('APP_URL', 'https://yourdomain.com');
define('ENCRYPTION_KEY', 'your_32_character_encryption_key');
```

### 3.2 Update Connection File
Replace `connection.php` with production settings:
```php
<?php
require_once 'production_config.php';
$con = getProductionConnection();
?>
```

### 3.3 Set File Permissions
```bash
# Set proper permissions
chmod 755 /var/www/html/dbfood
chmod -R 644 /var/www/html/dbfood/*.php
chmod -R 644 /var/www/html/dbfood/css/
chmod -R 644 /var/www/html/dbfood/js/
chmod -R 755 /var/www/html/dbfood/uploads/
chmod -R 755 /var/www/html/dbfood/logs/
```

## Step 4: Mobile Responsiveness

### 4.1 Add Mobile CSS
Add this line to the `<head>` section of all PHP files:
```html
<link rel="stylesheet" href="mobile.css">
```

### 4.2 Add Viewport Meta Tag
Add this to the `<head>` section:
```html
<meta name="viewport" content="width=device-width, initial-scale=1.0">
```

### 4.3 Test Mobile Responsiveness
- Test on various mobile devices
- Use browser developer tools to simulate mobile devices
- Check all functionality works on mobile

## Step 5: Security Setup

### 5.1 SSL Certificate
Install SSL certificate:
```bash
# Using Let's Encrypt
sudo apt install certbot python3-certbot-apache
sudo certbot --apache -d yourdomain.com
```

### 5.2 Update .htaccess
Uncomment HTTPS redirect in `.htaccess`:
```apache
# Force HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### 5.3 Security Headers
The `.htaccess` file already includes security headers. Verify they're working.

## Step 6: Performance Optimization

### 6.1 Enable PHP OPcache
Edit `/etc/php/7.x/apache2/php.ini`:
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
```

### 6.2 Configure Caching
The `.htaccess` file includes caching rules. Verify they're working.

### 6.3 Optimize Images
- Compress all images
- Use WebP format when possible
- Implement lazy loading

## Step 7: Testing

### 7.1 Functionality Testing
Test all features:
- User registration/login
- Food ordering
- Search functionality
- Admin panel
- Vendor management

### 7.2 Mobile Testing
Test on:
- iOS devices
- Android devices
- Various screen sizes
- Different browsers

### 7.3 Performance Testing
- Check page load times
- Test with multiple users
- Verify database queries are optimized

## Step 8: Monitoring and Maintenance

### 8.1 Error Logging
Monitor error logs:
```bash
tail -f /var/www/html/dbfood/logs/error.log
```

### 8.2 Backup Strategy
Set up automated backups:
```bash
# Database backup
mysqldump -u dbfood_user -p dbfood > backup_$(date +%Y%m%d).sql

# Files backup
tar -czf files_backup_$(date +%Y%m%d).tar.gz /var/www/html/dbfood
```

### 8.3 Updates
- Regularly update PHP and server software
- Monitor security advisories
- Update dependencies

## Troubleshooting

### Common Issues

#### 500 Internal Server Error
- Check file permissions
- Verify .htaccess syntax
- Check error logs

#### Database Connection Failed
- Verify database credentials
- Check database server status
- Test database connectivity

#### Mobile Issues
- Verify viewport meta tag
- Check mobile.css is included
- Test with browser developer tools

#### Performance Issues
- Enable OPcache
- Optimize database queries
- Implement caching

## Deployment Checklist

- [ ] Server requirements met
- [ ] Database created and imported
- [ ] Configuration files updated
- [ ] File permissions set
- [ ] SSL certificate installed
- [ ] Mobile CSS added
- [ ] Security headers configured
- [ ] All functionality tested
- [ ] Mobile responsiveness verified
- [ ] Performance optimized
- [ ] Backup system configured
- [ ] Monitoring set up
- [ ] deploy.php deleted from production

## Support

For issues during deployment:
1. Check error logs
2. Verify configuration
3. Test with minimal setup
4. Consult web server documentation

## Post-Deployment

After successful deployment:
1. Monitor performance
2. Regular security updates
3. Backup maintenance
4. User feedback collection
5. Continuous improvement
