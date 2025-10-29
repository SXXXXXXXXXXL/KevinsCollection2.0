<?php
include('../include/connect.php'); // Sesuaikan path koneksi

if (isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);

    // Update status di user_orders menjadi 'Shipped' dan di orders_pending (jika perlu)
    $update_order_status = "UPDATE user_orders SET order_status = 'Shipped' WHERE order_id = ?";
    $stmt = mysqli_prepare($con, $update_order_status);
    mysqli_stmt_bind_param($stmt, "i", $order_id);
    
    if (mysqli_stmt_execute($stmt)) {
        // Anda juga bisa mengupdate tabel orders_pending jika masih digunakan
        $update_pending_status = "UPDATE orders_pending SET order_status = 'Shipped' WHERE order_id = ?";
        $stmt_pending = mysqli_prepare($con, $update_pending_status);
        mysqli_stmt_bind_param($stmt_pending, "i", $order_id);
        mysqli_stmt_execute($stmt_pending);

        // Set pesan sukses (opsional)
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Payment for order ID ' . $order_id . ' has been confirmed and status updated to Shipped.'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Failed to update order status.'
        ];
    }
    
    // Redirect kembali ke halaman daftar pembayaran
    header("Location: indexatmin.php?list_payments");
    exit();

} else {
    // Jika tidak ada order_id, kembalikan ke halaman sebelumnya
    header("Location: indexatmin.php?list_payments");
    exit();
}
?>