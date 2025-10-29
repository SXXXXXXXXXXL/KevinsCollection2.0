<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../include/connect.php');
require_once '../vendor/autoload.php'; // Use Composer autoload

// Midtrans configuration
\Midtrans\Config::$serverKey = 'SB-Mid-server-85I5ReG-tchz8CUfP-jjxYeC';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if (!isset($_GET['order_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Order ID is required']);
    exit();
}

$order_id = intval($_GET['order_id']);

// Fetch order details
$select_data = "SELECT * FROM user_orders WHERE order_id = $order_id";
$result = mysqli_query($con, $select_data);
if (!$result) {
    http_response_code(500);
    echo json_encode(['error' => 'Error fetching order data']);
    exit();
}
$order = mysqli_fetch_assoc($result);
if (!$order) {
    http_response_code(404);
    echo json_encode(['error' => 'Order not found']);
    exit();
}

$invoice_number = $order['invoice_number'];
$amount_due = $order['amount_due'];

// Prepare transaction details
$transaction_details = [
    'order_id' => $invoice_number,
    'gross_amount' => $amount_due,
];

// Optional customer details
$customer_details = [
    'first_name' => '', // Add if available
    'email' => '',      // Add if available
    'phone' => '',      // Add if available
];

// Items details (optional)
$items = [];
$select_items = "SELECT p.product_title, p.product_price, op.product_id FROM orders_pending op JOIN products p ON op.product_id = p.product_id WHERE op.order_id = $order_id";
$result_items = mysqli_query($con, $select_items);
while ($item = mysqli_fetch_assoc($result_items)) {
    $items[] = [
        'id' => $item['product_id'],
        'price' => $item['product_price'],
        'quantity' => 1,
        'name' => $item['product_title'],
    ];
}

$params = [
    'transaction_details' => $transaction_details,
    'customer_details' => $customer_details,
    'item_details' => $items,
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
    echo json_encode(['snap_token' => $snapToken]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Failed to get snap token: ' . $e->getMessage()]);
}
?>
