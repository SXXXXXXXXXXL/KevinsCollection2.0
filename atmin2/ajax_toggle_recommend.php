<?php
include('../include/connect.php');
header('Content-Type: application/json');

$response = ['success' => false, 'message' => 'Invalid request.'];

if (isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);

    // Gunakan query `1 - is_recommended` untuk membalik nilai (0 menjadi 1, 1 menjadi 0)
    $query = "UPDATE products SET is_recommended = 1 - is_recommended WHERE product_id = ?";
    $stmt = mysqli_prepare($con, $query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "i", $product_id);
        if (mysqli_stmt_execute($stmt)) {
            // Ambil status baru untuk dikirim kembali
            $select_stmt = mysqli_prepare($con, "SELECT is_recommended FROM products WHERE product_id = ?");
            mysqli_stmt_bind_param($select_stmt, "i", $product_id);
            mysqli_stmt_execute($select_stmt);
            $result = mysqli_stmt_get_result($select_stmt);
            $new_status = mysqli_fetch_assoc($result)['is_recommended'];

            $response = ['success' => true, 'new_status' => $new_status];
        } else {
            $response['message'] = 'Failed to update status.';
        }
        mysqli_stmt_close($stmt);
    }
}

echo json_encode($response);
?>