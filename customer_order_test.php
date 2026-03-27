<?php
// Test page to verify customer order status system
session_start();
include("connection.php");

// Check if customer is logged in
if(!isset($_SESSION['cust_id'])) {
    echo "<h2>Please login as customer first: <a href='form/index.php'>Customer Login</a></h2>";
    echo "<h3>Test Accounts:</h3>";
    echo "<ul>";
    echo "<li>customer1@gmail.com / customer1</li>";
    echo "<li>customer2@gmail.com / customer2</li>";
    echo "<li>customer3@gmail.com / customer3</li>";
    echo "</ul>";
    exit();
}

$cust_id = $_SESSION['cust_id'];
echo "<h2>Customer Order Status Test</h2>";
echo "<h3>Logged in as: " . $cust_id . "</h3>";

// Show customer's orders with status
echo "<h3>Your Orders:</h3>";
$orders = mysqli_query($con,"SELECT o.*, f.foodname FROM tblorder o LEFT JOIN tbfood f ON o.fld_food_id = f.food_id WHERE o.fld_email_id='$cust_id' ORDER BY o.fld_order_id DESC");

if(mysqli_num_rows($orders) > 0) {
    echo "<table border='1' cellpadding='10'>";
    echo "<tr><th>Order ID</th><th>Food Item</th><th>Amount</th><th>Status</th><th>Test Action</th></tr>";
    
    while($order = mysqli_fetch_array($orders)) {
        echo "<tr>";
        echo "<td>" . $order['fld_order_id'] . "</td>";
        echo "<td>" . $order['foodname'] . "</td>";
        echo "<td>Rs." . $order['fld_payment'] . "</td>";
        
        // Status with color coding
        if($order['fldstatus'] == "In Process") {
            echo "<td style='color: orange;'>🕐 " . $order['fldstatus'] . " (Pending Approval)</td>";
            echo "<td><a href='form/cart.php'>Go to Orders Tab</a></td>";
        } elseif($order['fldstatus'] == "cancelled") {
            echo "<td style='color: red;'>❌ " . $order['fldstatus'] . "</td>";
            echo "<td>No Action</td>";
        } elseif($order['fldstatus'] == "Out Of Stock") {
            echo "<td style='color: red;'>⚠️ " . $order['fldstatus'] . "</td>";
            echo "<td>No Action</td>";
        } else {
            echo "<td style='color: green;'>✅ " . $order['fldstatus'] . "</td>";
            echo "<td>No Action</td>";
        }
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "<p>No orders found. <a href='index.php'>Go shopping!</a></p>";
}

// Show pending count
$pending_count = mysqli_query($con,"SELECT COUNT(*) as count FROM tblorder WHERE fld_email_id='$cust_id' AND fldstatus='In Process'");
$pending_result = mysqli_fetch_array($pending_count);
echo "<h3>Pending Orders: " . $pending_result['count'] . "</h3>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to <a href='form/cart.php'>Customer Cart</a></li>";
echo "<li>Click on 'Orders' tab (you should see a badge with " . $pending_result['count'] . " if pending)</li>";
echo "<li>View your order status with visual indicators</li>";
echo "<li>For pending orders, you can cancel them</li>";
echo "</ol>";

echo "<p><a href='form/cart.php'>← Go to Customer Cart/Orders</a></p>";
?>
