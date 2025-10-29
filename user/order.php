<?php
include('../include/connect.php');
include('../functions/common_function.php');

if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']); // Sanitize user_id
}

$get_user_ip = getIPAddress();
$total_price = 0;
$cart_query_price = "SELECT product_id FROM cart_details WHERE ip_address=?";
$stmt = $con->prepare($cart_query_price);

if (!$stmt) {
    die('Error preparing cart query: ' . $con->error);
}

$stmt->bind_param('s', $get_user_ip);
$stmt->execute();
$result_cart_price = $stmt->get_result();
$invoice_number = mt_rand();
$status = 'pending';
$count_products = $result_cart_price->num_rows;

// Fetch product prices and calculate total price
while ($row_price = $result_cart_price->fetch_assoc()) {
    $product_id = $row_price['product_id'];
    $select_product = "SELECT product_price FROM products WHERE product_id=?";
    $stmt_price = $con->prepare($select_product);

    if (!$stmt_price) {
        die('Error preparing product price query: ' . $con->error);
    }

    $stmt_price->bind_param('i', $product_id);
    $stmt_price->execute();
    $result_product = $stmt_price->get_result();
    if ($row_product_price = $result_product->fetch_assoc()) {
        $product_price = $row_product_price['product_price'];
        $total_price += $product_price;
    }
}

// Get quantity from cart
$get_cart = "SELECT quantity FROM cart_details WHERE ip_address=?";
$stmt_cart = $con->prepare($get_cart);

if (!$stmt_cart) {
    die('Error preparing cart quantity query: ' . $con->error);
}

$stmt_cart->bind_param('s', $get_user_ip);
$stmt_cart->execute();
$result_cart = $stmt_cart->get_result();
if ($get_item_quantity = $result_cart->fetch_assoc()) {
    $quantity = intval($get_item_quantity['quantity']);
} else {
    $quantity = 1; // Default value if no quantity is found
}

$subtotal = $total_price * $quantity;

// Insert into user_orders
$insert_orders = "INSERT INTO user_orders (user_id, amount_due, invoice_number, total_products, order_date, order_status) VALUES (?, ?, ?, ?, NOW(), ?)";
$stmt_orders = $con->prepare($insert_orders);

if (!$stmt_orders) {
    die('Error preparing user orders insert query: ' . $con->error);
}

$stmt_orders->bind_param('idiss', $user_id, $subtotal, $invoice_number, $count_products, $status);
$result_query = $stmt_orders->execute();

if ($result_query) {
    // Insert into orders_pending
    $insert_pending_orders = "INSERT INTO orders_pending (user_id, invoice_number, product_id, quantity, order_status) VALUES (?, ?, ?, ?, ?)";
    $stmt_pending = $con->prepare($insert_pending_orders);

    if (!$stmt_pending) {
        die('Error preparing orders pending insert query: ' . $con->error);
    }

    $stmt_pending->bind_param('iisii', $user_id, $invoice_number, $product_id, $quantity, $status);
    $stmt_pending->execute();
    
    // Update product availability
$update_product_status = "UPDATE products SET available_until = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE product_id=?";
$stmt_update = $con->prepare($update_product_status);

if (!$stmt_update) {
    die('Error preparing product update query: ' . $con->error);
}

$stmt_update->bind_param('i', $product_id);
$stmt_update->execute();



    // Empty cart
    $empty_cart = "DELETE FROM cart_details WHERE ip_address=?";
    $stmt_empty = $con->prepare($empty_cart);

    if (!$stmt_empty) {
        die('Error preparing cart empty query: ' . $con->error);
    }

    $stmt_empty->bind_param('s', $get_user_ip);
    $stmt_empty->execute();

    echo "<script>alert('Orders are submitted successfully'); window.open('profile.php', '_self');</script>";
} else {
    echo "<script>alert('Error submitting orders. Please try again.'); window.open('profile.php', '_self');</script>";
}
?>