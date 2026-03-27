<?php
// Simple test page to verify admin order approval system
include("connection.php");
session_start();

// Check if admin is logged in
if(!isset($_SESSION['admin'])) {
    echo "<h2>Please login as admin first: <a href='admin.php'>Admin Login</a></h2>";
    exit();
}

echo "<h2>Admin Order Approval System Test</h2>";

// Show pending orders count
$pending_count = mysqli_query($con,"SELECT COUNT(*) as count FROM tblorder WHERE fldstatus='In Process'");
$pending_result = mysqli_fetch_array($pending_count);
echo "<h3>Pending Orders: " . $pending_result['count'] . "</h3>";

// Show all orders with status
echo "<h3>All Orders:</h3>";
$orders = mysqli_query($con,"SELECT o.*, f.foodname FROM tblorder o LEFT JOIN tbfood f ON o.fld_food_id = f.food_id ORDER BY o.fld_order_id");
echo "<table border='1' cellpadding='10'>";
echo "<tr><th>Order ID</th><th>Customer</th><th>Food</th><th>Amount</th><th>Status</th><th>Test Action</th></tr>";

while($order = mysqli_fetch_array($orders)) {
    echo "<tr>";
    echo "<td>" . $order['fld_order_id'] . "</td>";
    echo "<td>" . $order['fld_email_id'] . "</td>";
    echo "<td>" . $order['foodname'] . "</td>";
    echo "<td>Rs." . $order['fld_payment'] . "</td>";
    
    // Status with color coding
    if($order['fldstatus'] == "In Process") {
        echo "<td style='color: orange;'>" . $order['fldstatus'] . " (Pending)</td>";
        echo "<td>";
        echo "<a href='dashboard.php' style='margin-right:10px;'>Go to Dashboard</a>";
        echo "</td>";
    } elseif($order['fldstatus'] == "cancelled") {
        echo "<td style='color: red;'>" . $order['fldstatus'] . "</td>";
        echo "<td>No Action</td>";
    } else {
        echo "<td style='color: green;'>" . $order['fldstatus'] . "</td>";
        echo "<td>No Action</td>";
    }
    echo "</tr>";
}

echo "</table>";

echo "<h3>Next Steps:</h3>";
echo "<ol>";
echo "<li>Go to <a href='dashboard.php'>Admin Dashboard</a></li>";
echo "<li>Click on 'Order status' tab (you should see a red badge with " . $pending_result['count'] . ")</li>";
echo "<li>For orders with 'In Process' status, click 'Approve' or 'Reject'</li>";
echo "<li>You should see success messages and the order status should update</li>";
echo "</ol>";

echo "<p><a href='dashboard.php'>← Back to Admin Dashboard</a></p>";
?>
