<?php
// checkout.php - Process guest checkout orders
session_start();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No data provided']);
    exit;
}

// Validate required fields
$required_fields = ['name', 'phone', 'email', 'address', 'state', 'city', 'cart'];
foreach ($required_fields as $field) {
    if (!isset($data[$field]) || trim($data[$field]) === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => "Missing required field: $field"]);
        exit;
    }
}

// Validate phone number (10 digits)
if (!preg_match('/^\d{10}$/', trim($data['phone']))) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Phone number must be 10 digits']);
    exit;
}

// Validate email
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Validate cart is not empty
if (empty($data['cart']) || !is_array($data['cart'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Cart is empty']);
    exit;
}

// Calculate order total
$total_amount = 0;
$item_count = 0;
foreach ($data['cart'] as $item_id => $item) {
    if (isset($item['price']) && isset($item['quantity'])) {
        $price = intval($item['price']);
        $quantity = intval($item['quantity']);
        if ($price > 0 && $quantity > 0) {
            $total_amount += ($price * $quantity);
            $item_count += $quantity;
        }
    }
}

if ($total_amount <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid cart total']);
    exit;
}

// Connect to database
$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    // Create orders table if it doesn't exist
    $create_table = "
    CREATE TABLE IF NOT EXISTS orders (
        id INT AUTO_INCREMENT PRIMARY KEY,
        order_number VARCHAR(20) NOT NULL UNIQUE,
        guest_name VARCHAR(100) NOT NULL,
        guest_email VARCHAR(100) NOT NULL,
        guest_phone VARCHAR(20) NOT NULL,
        guest_address VARCHAR(255) NOT NULL,
        guest_state VARCHAR(100) NOT NULL,
        guest_city VARCHAR(100) NOT NULL,
        total_amount INT NOT NULL,
        item_count INT NOT NULL,
        order_items JSON NOT NULL,
        order_status ENUM('pending', 'confirmed', 'preparing', 'ready', 'delivered', 'cancelled') DEFAULT 'pending',
        special_instructions TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        INDEX idx_order_number (order_number),
        INDEX idx_guest_email (guest_email),
        INDEX idx_guest_phone (guest_phone),
        INDEX idx_created_at (created_at)
    )
    ";
    $conn->query($create_table);

    // Generate unique order number
    $order_number = 'ORD-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid()), 0, 6));
    
    // Get special instructions if provided
    $special_instructions = isset($data['special_instructions']) ? trim($data['special_instructions']) : '';

    // Prepare order data
    $name = trim($data['name']);
    $phone = trim($data['phone']);
    $email = trim($data['email']);
    $address = trim($data['address']);
    $state = trim($data['state']);
    $city = trim($data['city']);
    $order_items_json = json_encode($data['cart']);

    // Insert order into database
    $insert_stmt = $conn->prepare(
        'INSERT INTO orders (order_number, guest_name, guest_email, guest_phone, guest_address, guest_state, guest_city, total_amount, item_count, order_items, special_instructions, order_status) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
    );
    
    if (!$insert_stmt) {
        throw new Exception('Prepare failed: ' . $conn->error);
    }

    $status = 'pending';
    $insert_stmt->bind_param(
        'sssssssiiiss',
        $order_number,
        $name,
        $email,
        $phone,
        $address,
        $state,
        $city,
        $total_amount,
        $item_count,
        $order_items_json,
        $special_instructions,
        $status
    );

    if (!$insert_stmt->execute()) {
        throw new Exception('Insert failed: ' . $insert_stmt->error);
    }

    $order_id = $conn->insert_id;
    $insert_stmt->close();
    $conn->close();

    // Return success response
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'message' => 'Order placed successfully!',
        'order_number' => $order_number,
        'order_id' => $order_id,
        'total_amount' => $total_amount,
        'item_count' => $item_count
    ]);
    exit;

} catch (Exception $e) {
    $conn->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error processing order: ' . $e->getMessage()]);
    exit;
}
?>
