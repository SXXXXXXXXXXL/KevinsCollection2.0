<?php
include('../include/connect.php');
include('../functions/common_function.php');
// session_start() harus dipanggil sebelum ada output HTML
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proses form registrasi
if(isset($_POST['user_register'])){
    // Gunakan trim untuk membersihkan spasi di awal/akhir
    $user_username = trim($_POST['user_username']);
    $user_email = trim($_POST['user_email']);
    $user_pw = $_POST['user_pw'];
    $user_con_pw = $_POST['user_con_pw'];
    $user_add = trim($_POST['user_add']);
    $user_numb = trim($_POST['user_numb']);
    $user_ip = getIPAddress();
    $secret_key = trim($_POST['secret_key']);
    $defined_secret_key = "admit_admin123";
    $role = ($secret_key === $defined_secret_key) ? 'admin' : 'user';

    // --- PERBAIKAN KEAMANAN: Menggunakan Prepared Statements ---
    $select_query = "SELECT * FROM user_table WHERE username = ? OR user_email = ? OR user_mobile = ?";
    $stmt_select = mysqli_prepare($con, $select_query);
    mysqli_stmt_bind_param($stmt_select, "sss", $user_username, $user_email, $user_numb);
    mysqli_stmt_execute($stmt_select);
    $result = mysqli_stmt_get_result($stmt_select);
    $num_rows = mysqli_num_rows($result);

    if($num_rows > 0){
        $row = mysqli_fetch_assoc($result);
        if($row['username'] == $user_username){
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Username already exists'];
        } elseif($row['user_email'] == $user_email){
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Email already exists'];
        } elseif($row['user_mobile'] == $user_numb){
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Mobile number already exists'];
        }
        header("Location: user_registration.php"); 
        exit();
    } else if($user_pw != $user_con_pw){
        $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Passwords do not match'];
        header("Location: user_registration.php");
        exit();
    } else {
        $user_image = 'default_profile.png'; 
        if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
            $user_image_temp = $_FILES['user_image']['tmp_name'];
            $original_filename = basename($_FILES['user_image']['name']);
            $user_image = uniqid() . '-' . $original_filename;
            move_uploaded_file($user_image_temp, "./user_images/$user_image");
        }
        
        $pw_hash = password_hash($user_pw, PASSWORD_DEFAULT);

        $insert_query = "INSERT INTO user_table (username, user_email, user_pw, user_image, user_ip, user_address, user_mobile, role) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = mysqli_prepare($con, $insert_query);
        mysqli_stmt_bind_param($stmt_insert, "ssssssss", $user_username, $user_email, $pw_hash, $user_image, $user_ip, $user_add, $user_numb, $role);
        
        if(mysqli_stmt_execute($stmt_insert)){
            $_SESSION['alert'] = ['type' => 'success', 'message' => "Registration successful as $role! Please login."];
            header("Location: user_login.php"); 
            exit();
        } else {
            $_SESSION['alert'] = ['type' => 'warning', 'message' => 'Failed to register. Please try again.'];
            header("Location: user_registration.php");
            exit();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Kevin's Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 py-12 px-4 sm:px-6 lg:px-8">
    <div id="app">
        
        <?php if(isset($_SESSION['alert'])): ?>
        <div id="alert-container" class="fixed top-5 right-5 z-50">
            <div class="max-w-sm w-full bg-white shadow-lg rounded-lg pointer-events-auto ring-1 ring-black ring-opacity-5 overflow-hidden">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <?php if ($_SESSION['alert']['type'] === 'success'): ?>
                                <i class="fas fa-check-circle text-green-400 h-6 w-6"></i>
                            <?php else: ?>
                                <i class="fas fa-exclamation-circle text-yellow-400 h-6 w-6"></i>
                            <?php endif; ?>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">
                                <?php echo $_SESSION['alert']['type'] === 'success' ? 'Success!' : 'Warning!'; ?>
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                <?php echo $_SESSION['alert']['message']; ?>
                            </p>
                        </div>
                        <div class="ml-4 flex-shrink-0 flex">
                            <button type="button" onclick="document.getElementById('alert-container').remove()" class="bg-white rounded-md inline-flex text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times h-5 w-5"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script>
            // Hapus alert secara otomatis setelah 5 detik
            setTimeout(() => {
                const alertContainer = document.getElementById('alert-container');
                if (alertContainer) {
                    alertContainer.remove();
                }
            }, 5000);
        </script>
        <?php unset($_SESSION['alert']); endif; ?>


        <div class="max-w-2xl mx-auto bg-white p-8 rounded-xl shadow-2xl">
            <div class="text-center mb-8">
                <a href="../index.php" class="flex items-center justify-center mb-6">
                    <i class="fas fa-store text-3xl text-blue-500 mr-2"></i>
                    <h2 class="text-3xl font-bold text-gray-900">Kevin's Collection</h2>
                </a>
                <h2 class="text-2xl font-extrabold text-gray-900 mb-2">Create your account</h2>
                <p class="text-sm text-gray-600">
                    Already have an account?
                    <a href="user_login.php" class="font-medium text-blue-600 hover:text-blue-500">
                        Sign in here
                    </a>
                </p>
            </div>

            <form action="" method="post" enctype="multipart/form-data" class="space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="user_username" class="block text-sm font-medium text-gray-700">Username</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-user text-gray-400"></i></div><input type="text" id="user_username" name="user_username" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Choose a username"></div>
                    </div>
                    <div>
                        <label for="user_email" class="block text-sm font-medium text-gray-700">Email</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-envelope text-gray-400"></i></div><input type="email" id="user_email" name="user_email" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your email"></div>
                    </div>
                    <div>
                        <label for="user_pw" class="block text-sm font-medium text-gray-700">Password</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-lock text-gray-400"></i></div><input type="password" id="user_pw" name="user_pw" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Create a password"></div>
                    </div>
                    <div>
                        <label for="user_con_pw" class="block text-sm font-medium text-gray-700">Confirm Password</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-lock text-gray-400"></i></div><input type="password" id="user_con_pw" name="user_con_pw" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Confirm your password"></div>
                    </div>
                    <div class="md:col-span-2">
                        <label for="user_add" class="block text-sm font-medium text-gray-700">Address</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-map-marker-alt text-gray-400"></i></div><input type="text" id="user_add" name="user_add" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your address"></div>
                    </div>
                    <div>
                        <label for="user_numb" class="block text-sm font-medium text-gray-700">Mobile Number</label>
                        <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-phone text-gray-400"></i></div><input type="text" id="user_numb" name="user_numb" required class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter your mobile number"></div>
                    </div>
                    <div>
                        <label for="user_image" class="block text-sm font-medium text-gray-700">Profile Photo</label>
                        <input type="file" id="user_image" name="user_image" class="mt-1 block w-full text-sm text-gray-900 border border-gray-300 rounded-md cursor-pointer bg-gray-50 focus:outline-none file:bg-gray-200 file:text-gray-700 file:text-sm file:font-medium file:border-0 file:px-4 file:py-2">
                    </div>
                    <div class="md:col-span-2">
                         <label for="secret_key" class="block text-sm font-medium text-gray-700">Admin Token (Optional)</label>
                         <div class="mt-1 relative"><div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none"><i class="fas fa-key text-gray-400"></i></div><input type="text" id="secret_key" name="secret_key" class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500" placeholder="Enter token if you are an admin"></div>
                    </div>
                </div>
                <div class="flex flex-col items-center space-y-4 pt-4">
                    <button type="submit" name="user_register" class="w-full md:w-1/2 px-8 py-3 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 transition duration-200">Create Account</button>
                    <a href="../index.php" class="text-sm font-medium text-gray-600 hover:text-gray-500"><i class="fas fa-arrow-left mr-1"></i>Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>