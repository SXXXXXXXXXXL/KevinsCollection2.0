<?php
// Selalu letakkan logika PHP di atas HTML
include('../include/connect.php');

$feedback_message = ''; // Variabel untuk menyimpan pesan feedback

if(isset($_POST['insert_brand'])){
    // 1. Ambil dan bersihkan input
    $brand_title = trim($_POST['brand_title']);

    // 2. Validasi input tidak boleh kosong
    if(empty($brand_title)){
        $feedback_message = "<div class='bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700 p-4 mb-4' role='alert'>Brand title cannot be empty.</div>";
    } else {
        // 3. Gunakan prepared statement untuk keamanan
        // Cek apakah brand sudah ada
        $select_query = "SELECT * FROM brands WHERE brand_title = ?";
        $stmt_select = mysqli_prepare($con, $select_query);
        mysqli_stmt_bind_param($stmt_select, "s", $brand_title);
        mysqli_stmt_execute($stmt_select);
        $result_select = mysqli_stmt_get_result($stmt_select);
        $number = mysqli_num_rows($result_select);

        if($number > 0){
            $feedback_message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-4' role='alert'>This brand is already available.</div>";
        } else {
            // Jika belum ada, masukkan brand baru
            $insert_query = "INSERT INTO brands (brand_title) VALUES (?)";
            $stmt_insert = mysqli_prepare($con, $insert_query);
            mysqli_stmt_bind_param($stmt_insert, "s", $brand_title);
            $result = mysqli_stmt_execute($stmt_insert);

            if($result){
                $feedback_message = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-4' role='alert'>Brand has been added successfully.</div>";
            }
        }
    }
}
?>

<div class="max-w-md mx-auto">
    <h2 class="text-2xl font-bold text-center mb-4 text-gray-800">Insert New Brand</h2>

    <?php echo $feedback_message; ?>

    <form action="" method="post" class="space-y-4">
        <div>
            <label for="brand_title" class="block text-sm font-medium text-gray-700">Brand Title</label>
            <div class="mt-1 relative rounded-md shadow-sm">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa-solid fa-receipt text-gray-400"></i>
                </div>
                <input type="text" name="brand_title" id="brand_title" 
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md leading-5 bg-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-gray-800 focus:border-gray-800 sm:text-sm" required>
            </div>
        </div>

        <div>
            <button type="submit" name="insert_brand" 
                    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800 transition duration-150">
                Insert Brand
            </button>
        </div>
    </form>
</div>