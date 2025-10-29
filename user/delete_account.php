<?php
// Logika PHP diletakkan di atas sebelum output HTML apapun
// Diasumsikan session_start() dan include('../include/connect.php') sudah ada di file utama yang memuat file ini.

// Ambil username dari sesi
$username_session = $_SESSION['username'];

// Proses jika tombol "Delete" ditekan
if(isset($_POST['delete'])){
    // Gunakan prepared statement untuk menghapus data dengan aman
    $delete_query = "DELETE FROM user_table WHERE username = ?";
    $stmt = mysqli_prepare($con, $delete_query);

    if ($stmt) {
        mysqli_stmt_bind_param($stmt, "s", $username_session);
        if(mysqli_stmt_execute($stmt)){
            session_destroy();
            echo "<script>alert('Account has been deleted successfully.')</script>";
            echo "<script>window.open('../index.php','_self')</script>";
            exit(); // Hentikan eksekusi setelah redirect
        }
        mysqli_stmt_close($stmt);
    }
}

// Tombol "Don't Delete" tidak memerlukan proses PHP, karena akan ditangani oleh link biasa.

?>

<div class="max-w-lg mx-auto bg-white p-8 rounded-lg shadow-md border border-gray-200">
    <div class="text-center">
        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-red-100">
            <i class="fas fa-exclamation-triangle text-2xl text-red-600"></i>
        </div>
        
        <h3 class="mt-4 text-2xl font-bold text-gray-900">Delete Account</h3>
        
        <p class="mt-2 text-sm text-gray-600">
            Are you sure you want to delete your account? All of your data will be permanently removed. This action cannot be undone.
        </p>
    </div>

    <div class="mt-8 flex justify-center gap-x-4">
        <a href="profile.php" class="inline-flex justify-center rounded-md border border-gray-300 bg-white px-4 py-2 text-base font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 w-full sm:w-auto">
            No, Don't Delete Account
        </a>
        
        <form action="" method="post">
             <button type="submit" name="delete" class="inline-flex w-full justify-center rounded-md border border-transparent bg-red-600 px-4 py-2 text-base font-medium text-white shadow-sm hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto" onclick="return confirm('This action is permanent. Are you absolutely sure?')">
                Yes, Delete Account
            </button>
        </form>
    </div>
</div>