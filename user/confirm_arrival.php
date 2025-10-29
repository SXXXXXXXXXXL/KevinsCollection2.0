<?php
include('../include/connect.php');
session_start();

// Keamanan: Pastikan user sudah login
if (!isset($_SESSION['username'])) {
    die("Akses ditolak. Silakan login terlebih dahulu.");
}

// Keamanan: Pastikan order_id ada di URL
if (!isset($_GET['order_id'])) {
    header("Location: profile.php?my_orders");
    exit();
}

// 1. Ambil data dengan aman
$order_id = intval($_GET['order_id']);
$username = $_SESSION['username'];

// 2. Dapatkan user_id dari username untuk memastikan user hanya bisa mengubah order miliknya sendiri
$stmt_user = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE username = ?");
mysqli_stmt_bind_param($stmt_user, "s", $username);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);

if ($row_user = mysqli_fetch_assoc($result_user)) {
    $user_id = $row_user['user_id'];

    // 3. Update status pesanan menjadi 'Success - Arrived'
    // Query ini hanya akan berhasil jika order_id dan user_id cocok
    $update_query = "UPDATE user_orders SET order_status = 'Arrived' WHERE order_id = ? AND user_id = ?";
    $stmt_update = mysqli_prepare($con, $update_query);
    mysqli_stmt_bind_param($stmt_update, "ii", $order_id, $user_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        // Set pesan sukses untuk ditampilkan di halaman profil
        $_SESSION['alert'] = [
            'type' => 'success',
            'message' => 'Terima kasih telah mengonfirmasi pesanan Anda!'
        ];
    } else {
        $_SESSION['alert'] = [
            'type' => 'error',
            'message' => 'Gagal mengonfirmasi pesanan.'
        ];
    }
    mysqli_stmt_close($stmt_update);

} else {
    $_SESSION['alert'] = [
        'type' => 'error',
        'message' => 'User tidak ditemukan.'
    ];
}

mysqli_stmt_close($stmt_user);

// 4. Redirect kembali ke halaman pesanan
header("Location: profile.php?my_orders");
exit();

?>