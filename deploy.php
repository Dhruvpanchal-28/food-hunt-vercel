<?php
/*
 * Deployment Script for Food Hunt Application
 * This script helps prepare the application for production deployment
 */

// Prevent direct access in production
if (defined('APP_ENV') && APP_ENV === 'production') {
    die('This script should only be run during deployment setup.');
}

echo "<h1>Food Hunt - Deployment Setup</h1>";
echo "<p>This script will help you prepare your application for production deployment.</p>";

// Check requirements
echo "<h2>Checking Requirements...</h2>";

$requirements = [
    'PHP Version >= 7.1' => version_compare(PHP_VERSION, '7.1.0', '>='),
    'MySQLi Extension' => extension_loaded('mysqli'),
    'GD Extension' => extension_loaded('gd'),
    'JSON Extension' => extension_loaded('json'),
    'Session Support' => extension_loaded('session'),
    'File Upload Support' => ini_get('file_uploads'),
];

foreach ($requirements as $requirement => $met) {
    $status = $met ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
    echo "<p>$status $requirement</p>";
    
    if (!$met) {
        echo "<p style='color: red; margin-left: 20px;'>Please install/enable: $requirement</p>";
    }
}

// Check directory permissions
echo "<h2>Checking Directory Permissions...</h2>";

$directories = [
    'uploads' => '0755',
    'logs' => '0755',
    'image' => '0755',
    'img' => '0755'
];

foreach ($directories as $dir => $expected_perms) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p><span style='color: green;'>✓</span> Created directory: $dir</p>";
        } else {
            echo "<p><span style='color: red;'>✗</span> Could not create directory: $dir</p>";
        }
    }
    
    if (is_dir($dir)) {
        $current_perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir);
        $status = $writable ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
        echo "<p>$status Directory: $dir (Permissions: $current_perms, Writable: " . ($writable ? 'Yes' : 'No') . ")</p>";
    }
}

// Database setup
echo "<h2>Database Setup</h2>";
echo "<p>Please ensure you have:</p>";
echo "<ul>";
echo "<li>Created a production database</li>";
echo "<li>Imported the localhost.sql file</li>";
echo "<li>Created a database user with appropriate permissions</li>";
echo "</ul>";

// Configuration file setup
echo "<h2>Configuration Setup</h2>";

if (!file_exists('production_config.php')) {
    echo "<p><span style='color: orange;'>⚠</span> production_config.php already exists. Please backup and update it manually.</p>";
} else {
    echo "<p><span style='color: green;'>✓</span> production_config.php is ready for configuration</p>";
}

echo "<h3>Steps to configure production_config.php:</h3>";
echo "<ol>";
echo "<li>Update database credentials (DB_HOST, DB_USER, DB_PASS, DB_NAME)</li>";
echo "<li>Set APP_URL to your production domain</li>";
echo "<li>Generate a secure ENCRYPTION_KEY (32 characters)</li>";
echo "<li>Configure email settings (SMTP_*)</li>";
echo "<li>Update any other settings as needed</li>";
echo "</ol>";

// Mobile responsiveness check
echo "<h2>Mobile Responsiveness</h2>";
echo "<p>Mobile CSS has been created. To enable it:</p>";
echo "<ol>";
echo "<li>Add the following line to your HTML head section in all PHP files:</li>";
echo "<code>&lt;link rel='stylesheet' href='mobile.css'&gt;</code>";
echo "<li>Test on various mobile devices</li>";
echo "<li>Adjust styles as needed</li>";
echo "</ol>";

// Security recommendations
echo "<h2>Security Recommendations</h2>";
echo "<ul>";
echo "<li>Change default database passwords</li>";
echo "<li>Use strong passwords for all admin accounts</li>";
echo "<li>Enable SSL/HTTPS on your server</li>";
echo "<li>Regularly update PHP and server software</li>";
echo "<li>Implement regular backups</li>";
echo "<li>Monitor error logs</li>";
echo "<li>Use a Web Application Firewall (WAF)</li>";
echo "</ul>";

// Performance optimizations
echo "<h2>Performance Optimizations</h2>";
echo "<ul>";
echo "<li>Enable PHP OPcache</li>";
echo "<li>Use a CDN for static assets</li>";
echo "<li>Optimize images for web</li>";
echo "<li>Implement database indexing</li>";
echo "<li>Consider using Redis/Memcached for caching</li>";
echo "</ul>";

// File structure check
echo "<h2>File Structure Check</h2>";

$required_files = [
    'index.php',
    'connection.php',
    'mobile.css',
    'production_config.php',
    '.htaccess',
    'localhost.sql'
];

foreach ($required_files as $file) {
    $exists = file_exists($file);
    $status = $exists ? '<span style="color: green;">✓</span>' : '<span style="color: red;">✗</span>';
    echo "<p>$status $file</p>";
}

// Next steps
echo "<h2>Next Steps</h2>";
echo "<ol>";
echo "<li>Configure production_config.php with your production settings</li>";
echo "<li>Upload files to your production server</li>";
echo "<li>Import the database schema</li>";
echo "<li>Test all functionality</li>";
echo "<li>Test mobile responsiveness</li>";
echo "<li>Set up SSL certificate</li>";
echo "<li>Configure domain and DNS</li>";
echo "<li>Set up monitoring and backups</li>";
echo "</ol>";

// Deployment checklist
echo "<h2>Pre-Deployment Checklist</h2>";
echo "<form>";
echo "<input type='checkbox' id='check1'> <label for='check1'>Database configured and imported</label><br>";
echo "<input type='checkbox' id='check2'> <label for='check2'>production_config.php updated with correct settings</label><br>";
echo "<input type='checkbox' id='check3'> <label for='check3'>All file permissions set correctly</label><br>";
echo "<input type='checkbox' id='check4'> <label for='check4'>Mobile CSS added to all pages</label><br>";
echo "<input type='checkbox' id='check5'> <label for='check5'>SSL certificate installed</label><br>";
echo "<input type='checkbox' id='check6'> <label for='check6'>Error logging configured</label><br>";
echo "<input type='checkbox' id='check7'> <label for='check7'>Backup system in place</label><br>";
echo "<input type='checkbox' id='check8'> <label for='check8'>All functionality tested</label><br>";
echo "</form>";

echo "<h2>Deployment Complete!</h2>";
echo "<p>Your Food Hunt application is now ready for production deployment.</p>";
echo "<p><strong>Important:</strong> Delete this deploy.php file from your production server after deployment.</p>";
?>
