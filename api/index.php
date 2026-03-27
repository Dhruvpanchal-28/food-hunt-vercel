<?php
/*
 * Vercel API Handler
 * This file handles all API requests for Vercel deployment
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

// Database connection for Vercel
function getVercelConnection() {
    $host = $_ENV['DB_HOST'] ?? 'localhost';
    $user = $_ENV['DB_USER'] ?? 'root';
    $pass = $_ENV['DB_PASS'] ?? '';
    $db = $_ENV['DB_NAME'] ?? 'dbfood';
    
    $con = mysqli_connect($host, $user, $pass, $db);
    
    if (!$con) {
        http_response_code(500);
        echo json_encode(['error' => 'Database connection failed']);
        exit;
    }
    
    return $con;
}

// Route handling
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$method = $_SERVER['REQUEST_METHOD'];

// Remove /api prefix from path
$path = str_replace('/api', '', $path);

switch ($path) {
    case '/foods':
        handleFoods($method);
        break;
    case '/vendors':
        handleVendors($method);
        break;
    case '/orders':
        handleOrders($method);
        break;
    case '/login':
        handleLogin($method);
        break;
    case '/register':
        handleRegister($method);
        break;
    case '/search':
        handleSearch($method);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

function handleFoods($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'GET':
            $query = "SELECT * FROM tbfood ORDER BY foodname";
            $result = mysqli_query($con, $query);
            $foods = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $foods[] = $row;
            }
            
            echo json_encode($foods);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}

function handleVendors($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'GET':
            $query = "SELECT * FROM tblvendor ORDER BY fld_name";
            $result = mysqli_query($con, $query);
            $vendors = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $vendors[] = $row;
            }
            
            echo json_encode($vendors);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}

function handleOrders($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            // Validate required fields
            if (!isset($data['customer_id']) || !isset($data['food_id'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Missing required fields']);
                break;
            }
            
            $query = "INSERT INTO tblorders (customer_id, food_id, quantity, total_price, status) 
                     VALUES (?, ?, ?, ?, 'pending')";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "iiid", 
                $data['customer_id'], 
                $data['food_id'], 
                $data['quantity'] ?? 1, 
                $data['total_price'] ?? 0
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'order_id' => mysqli_insert_id($con)]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Failed to create order']);
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}

function handleLogin($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (!isset($data['email']) || !isset($data['password'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Email and password required']);
                break;
            }
            
            // Check customer login
            $query = "SELECT * FROM tblcustomer WHERE fld_email = ? AND fld_password = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "ss", $data['email'], $data['password']);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            
            if ($row = mysqli_fetch_assoc($result)) {
                echo json_encode(['success' => true, 'user' => $row, 'type' => 'customer']);
            } else {
                // Check vendor login
                $query = "SELECT * FROM tblvendor WHERE fld_email = ? AND fld_password = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ss", $data['email'], $data['password']);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if ($row = mysqli_fetch_assoc($result)) {
                    echo json_encode(['success' => true, 'user' => $row, 'type' => 'vendor']);
                } else {
                    http_response_code(401);
                    echo json_encode(['error' => 'Invalid credentials']);
                }
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}

function handleRegister($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            
            $required_fields = ['name', 'email', 'password', 'phone', 'address'];
            foreach ($required_fields as $field) {
                if (!isset($data[$field])) {
                    http_response_code(400);
                    echo json_encode(['error' => "Missing field: $field"]);
                    break 2;
                }
            }
            
            $query = "INSERT INTO tblcustomer (fld_name, fld_email, fld_password, fld_mob, fld_address) 
                     VALUES (?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "sssss", 
                $data['name'], 
                $data['email'], 
                $data['password'], 
                $data['phone'], 
                $data['address']
            );
            
            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true, 'customer_id' => mysqli_insert_id($con)]);
            } else {
                http_response_code(500);
                echo json_encode(['error' => 'Registration failed']);
            }
            
            mysqli_stmt_close($stmt);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}

function handleSearch($method) {
    $con = getVercelConnection();
    
    switch ($method) {
        case 'GET':
            $search_term = $_GET['q'] ?? '';
            $search_type = $_GET['type'] ?? 'food';
            
            if (empty($search_term)) {
                http_response_code(400);
                echo json_encode(['error' => 'Search term required']);
                break;
            }
            
            if ($search_type === 'food') {
                $query = "SELECT f.*, v.fld_name as vendor_name 
                         FROM tbfood f 
                         JOIN tblvendor v ON f.fldvendor_id = v.fldvendor_id 
                         WHERE f.foodname LIKE ? OR f.cuisines LIKE ?";
                $term = "%$search_term%";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ss", $term, $term);
            } else {
                $query = "SELECT * FROM tblvendor WHERE fld_name LIKE ? OR fld_address LIKE ?";
                $term = "%$search_term%";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ss", $term, $term);
            }
            
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $results = [];
            
            while ($row = mysqli_fetch_assoc($result)) {
                $results[] = $row;
            }
            
            echo json_encode($results);
            mysqli_stmt_close($stmt);
            break;
            
        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }
    
    mysqli_close($con);
}
?>
