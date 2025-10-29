<?php
include('../include/connect.php');
session_start();

// Keamanan: Pastikan user dan order_id valid
if (!isset($_SESSION['username']) || !isset($_GET['order_id'])) {
    header("Location: user_login.php");
    exit();
}

$order_id = intval($_GET['order_id']);
$username = $_SESSION['username'];
$user_id = null;
// Dapatkan user_id dari session untuk validasi
$stmt_user = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE username = ?");
mysqli_stmt_bind_param($stmt_user, "s", $username);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
if($row_user = mysqli_fetch_assoc($result_user)) {
    $user_id = $row_user['user_id'];
}
mysqli_stmt_close($stmt_user);

if(!$user_id) die("Invalid session.");

// --- LOGIKA HAPUS PRODUK DARI KATALOG ---

// 1. Ambil semua product_id & gambar dari pesanan yang BARU saja dibayar (status 'Pending')
$get_products_query = "SELECT p.product_id, p.product_image1, p.product_image2, p.product_image3 
                       FROM products p
                       JOIN orders_pending op ON p.product_id = op.product_id
                       JOIN user_orders uo ON op.order_id = uo.order_id
                       WHERE uo.order_id = ? AND uo.user_id = ? AND uo.order_status = 'Pending'";
                       
$stmt_get = mysqli_prepare($con, $get_products_query);
mysqli_stmt_bind_param($stmt_get, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt_get);
$result_products = mysqli_stmt_get_result($stmt_get);
$products_to_delete = [];
while ($row = mysqli_fetch_assoc($result_products)) {
    $products_to_delete[] = $row;
}
mysqli_stmt_close($stmt_get);

// 2. Hapus setiap produk dari tabel `products` dan hapus file gambarnya
if (!empty($products_to_delete)) {
    $delete_product_query = "DELETE FROM products WHERE product_id = ?";
    $stmt_delete = mysqli_prepare($con, $delete_product_query);

    foreach ($products_to_delete as $product) {
        $product_id_to_delete = $product['product_id'];
        // Hapus file gambar
        if (file_exists("../atmin/product_images/" . $product['product_image1'])) unlink("../atmin/product_images/" . $product['product_image1']);
        if (file_exists("../atmin/product_images/" . $product['product_image2'])) unlink("../atmin/product_images/" . $product['product_image2']);
        if (file_exists("../atmin/product_images/" . $product['product_image3'])) unlink("../atmin/product_images/" . $product['product_image3']);
        // Hapus record dari database
        mysqli_stmt_bind_param($stmt_delete, "i", $product_id_to_delete);
        mysqli_stmt_execute($stmt_delete);
    }
    mysqli_stmt_close($stmt_delete);
}

// 3. Update status pesanan menjadi 'Paid'
$update_order_status = "UPDATE user_orders SET order_status = 'Paid' WHERE order_id = ? AND user_id = ?";
$stmt_update = mysqli_prepare($con, $update_order_status);
mysqli_stmt_bind_param($stmt_update, "ii", $order_id, $user_id);
mysqli_stmt_execute($stmt_update);
mysqli_stmt_close($stmt_update);

$_SESSION['alert'] = ['type' => 'success', 'message' => 'Payment successful! Your order is being processed.'];
header("Location: profile.php?my_orders");
exit();
?>