<?php
// track_order.php - API endpoint to track orders
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['order_id']) && !isset($data['order_number']) && !isset($data['email'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Please provide order ID, order number, or email']);
    exit;
}

$conn = new mysqli('localhost', 'root', '', 'tealogy_login');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $query = "SELECT id, order_number, guest_name, guest_email, guest_phone, 
              total_amount, item_count, order_status, order_items, created_at, updated_at 
              FROM orders WHERE ";
    
    if (isset($data['order_id'])) {
        $query .= "id = " . intval($data['order_id']);
    } elseif (isset($data['order_number'])) {
        $query .= "order_number = '" . $conn->real_escape_string($data['order_number']) . "'";
    } elseif (isset($data['email'])) {
        $query .= "guest_email = '" . $conn->real_escape_string($data['email']) . "'";
    }
    
    $query .= " LIMIT 1";
    
    $result = $conn->query($query);
    
    if (!$result) {
        throw new Exception('Query failed: ' . $conn->error);
    }
    
    if ($result->num_rows === 0) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Order not found']);
        $conn->close();
        exit;
    }
    
    $order = $result->fetch_assoc();
    $order['order_items'] = json_decode($order['order_items'], true);
    
    // Determine status badge
    $statusBadges = [
        'pending' => ['color' => '#ffc107', 'label' => 'Pending Confirmation'],
        'confirmed' => ['color' => '#17a2b8', 'label' => 'Confirmed'],
        'preparing' => ['color' => '#007bff', 'label' => 'Preparing'],
        'ready' => ['color' => '#28a745', 'label' => 'Ready for Pickup'],
        'delivered' => ['color' => '#28a745', 'label' => 'Delivered'],
        'cancelled' => ['color' => '#dc3545', 'label' => 'Cancelled']
    ];
    
    $order['status_badge'] = $statusBadges[$order['order_status']] ?? ['color' => '#999', 'label' => $order['order_status']];
    
    $conn->close();
    
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'order' => $order
    ]);
    exit;
    
} catch (Exception $e) {
    $conn->close();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    exit;
}
?>
