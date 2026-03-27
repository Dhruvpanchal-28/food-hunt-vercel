<?php
// Test script to verify database connection and setup
include("connection.php");

echo "<h2>Database Connection Test</h2>";

if($con) {
    echo "<p style='color: green;'>✓ Database connection successful!</p>";
} else {
    echo "<p style='color: red;'>✗ Database connection failed!</p>";
    die();
}

echo "<h3>Database Tables:</h3>";
$tables = mysqli_query($con, "SHOW TABLES FROM dbfood");
while($table = mysqli_fetch_array($tables)) {
    echo "- " . $table[0] . "<br>";
}

echo "<h3>Admin Login Test:</h3>";
$admin_test = mysqli_query($con, "SELECT * FROM tbadmin WHERE fld_username='admin' AND fld_password='admin@123'");
if(mysqli_num_rows($admin_test) > 0) {
    echo "<p style='color: green;'>✓ Admin credentials found (admin/admin@123)</p>";
} else {
    echo "<p style='color: red;'>✗ Admin credentials not found</p>";
}

echo "<h3>Vendor Accounts:</h3>";
$vendors = mysqli_query($con, "SELECT fld_email, fld_password FROM tblvendor LIMIT 3");
while($vendor = mysqli_fetch_array($vendors)) {
    echo "- Vendor: " . $vendor['fld_email'] . " / Password: " . $vendor['fld_password'] . "<br>";
}

echo "<h3>Customer Accounts:</h3>";
$customers = mysqli_query($con, "SELECT fld_email, password FROM tblcustomer LIMIT 3");
while($customer = mysqli_fetch_array($customers)) {
    echo "- Customer: " . $customer['fld_email'] . " / Password: " . $customer['password'] . "<br>";
}

echo "<h3>Food Items:</h3>";
$foods = mysqli_query($con, "SELECT COUNT(*) as count FROM tbfood");
$food_count = mysqli_fetch_array($foods);
echo "- Total food items: " . $food_count['count'] . "<br>";

echo "<h2>Access Links:</h2>";
echo "<p><a href='index.php'>Main Website</a></p>";
echo "<p><a href='admin.php'>Admin Login</a></p>";
echo "<p><a href='vendor_login.php'>Vendor Login</a></p>";
echo "<p><a href='form/index.php'>Customer Login</a></p>";
?>
