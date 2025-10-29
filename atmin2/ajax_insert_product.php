<?php
// Set header untuk respons JSON
header('Content-Type: application/json');

include('../include/connect.php'); // Sesuaikan path koneksi

// Inisialisasi respons default
$response = ['success' => false, 'message' => 'An unknown error occurred.'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data teks dari form
    $product_title = $_POST['product_title'] ?? '';
    $product_desc = $_POST['product_desc'] ?? '';
    $product_keyword = $_POST['product_keyword'] ?? '';
    $product_category = $_POST['product_category'] ?? '';
    $product_brand = $_POST['product_brand'] ?? '';
    $product_price = $_POST['product_price'] ?? '';
    $is_recommended = $_POST['is_recommended'] ?? 0;
    $product_status = 'true';

    // Validasi dasar
    if (empty($product_title) || empty($product_category) || empty($product_brand) || empty($product_price)) {
        $response['message'] = 'Please fill all required fields.';
        echo json_encode($response);
        exit();
    }

    // Siapkan nama file gambar (maksimal 3)
    $image_names = [];
    for ($i = 1; $i <= 3; $i++) {
        $file_key = 'product_image' . $i;
        if (isset($_FILES[$file_key]) && $_FILES[$file_key]['error'] === UPLOAD_ERR_OK) {
            $tmp_name = $_FILES[$file_key]['tmp_name'];
            $original_name = basename($_FILES[$file_key]['name']);
            // Buat nama file unik untuk keamanan
            $new_filename = uniqid() . '-' . $original_name;
            
            if (move_uploaded_file($tmp_name, "./product_images/$new_filename")) {
                $image_names[] = $new_filename;
            } else {
                 $image_names[] = ''; // Gagal upload
            }
        } else {
            $image_names[] = ''; // Tidak ada file
        }
    }
    
    // Pastikan setidaknya ada satu gambar
    if (empty($image_names[0])) {
         $response['message'] = 'Please upload at least one image.';
         echo json_encode($response);
         exit();
    }

    // Gunakan prepared statement untuk insert ke database
    $insert_query = "INSERT INTO products (product_title, product_desc, product_keywords, category_id, brand_id, product_image1, product_image2, product_image3, product_price, is_recommended, date, status) VALUES (?,?,?,?,?,?,?,?,?,?,NOW(),?)";
    
    $stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($stmt, "sssiissssis", 
        $product_title, 
        $product_desc, 
        $product_keyword, 
        $product_category, 
        $product_brand, 
        $image_names[0], // Gambar 1
        $image_names[1], // Gambar 2
        $image_names[2], // Gambar 3
        $product_price, 
        $is_recommended, 
        $product_status
    );

    if(mysqli_stmt_execute($stmt)){
        $response = ['success' => true, 'message' => 'Product has been inserted successfully!'];
    } else {
        $response['message'] = 'Database error: Failed to insert product.';
    }
    mysqli_stmt_close($stmt);

} else {
    $response['message'] = 'Invalid request method.';
}

echo json_encode($response);
?>