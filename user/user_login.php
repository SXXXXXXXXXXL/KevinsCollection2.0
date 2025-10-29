<?php
session_start();
    
include('../include/connect.php');
include('../functions/common_function.php');

if(isset($_POST['user_login'])){
    $user_username = mysqli_real_escape_string($con, $_POST['user_username']);
    $user_pw = mysqli_real_escape_string($con, $_POST['user_pw']);

    $select_query = "SELECT * FROM user_table WHERE username='$user_username'";
    $result = mysqli_query($con, $select_query);
    $user_ip = getIPAddress();
    
    $select_query_cart = "SELECT * FROM cart_details WHERE ip_address='$user_ip'";
    $select_cart = mysqli_query($con, $select_query_cart);
    $row_count_cart = mysqli_num_rows($select_cart);

    if($result && mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $hashed_pw = $row['user_pw'];
        
        if(password_verify($user_pw, $hashed_pw)){
            $_SESSION['username'] = $user_username;
            $role = $row['role'];

            if($row_count_cart > 0){
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Logged in successfully'
                ];
                header("Location:checkout.php");
            } else {
                $_SESSION['alert'] = [
                    'type' => 'success',
                    'message' => 'Logged in successfully'
                ];
                if($role == 'admin'){
                    header("Location:../atmin/indexatmin.php");
                } else {
                    header("Location:profile.php");
                }
            }
        } else {
            $_SESSION['alert'] = [
                'type' => 'warning',
                'message' => 'Invalid credentials'
            ];
        }
    } else {
        $_SESSION['alert'] = [
            'type' => 'warning',
            'message' => 'Invalid credentials'
        ];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Kevin's Collection</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
    <div id="app">
        <?php if(isset($_SESSION['alert'])): ?>
        <div id="alert" 
             class="fixed inset-0 flex items-center justify-center z-50">
            <div class="fixed inset-0 bg-black bg-opacity-30 backdrop-blur-sm"></div>
            <div class="bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 max-w-sm w-full mx-4 relative z-50 border-t-4 <?php echo $_SESSION['alert']['type'] === 'success' ? 'border-green-500' : 'border-yellow-500' ?>">
                <div class="p-4 flex flex-col items-center">
                    <div class="w-16 h-16 rounded-full flex items-center justify-center mb-4 <?php echo $_SESSION['alert']['type'] === 'success' ? 'bg-green-100' : 'bg-yellow-100' ?> border-2 <?php echo $_SESSION['alert']['type'] === 'success' ? 'border-green-500' : 'border-yellow-500' ?>">
                        <i class="fas <?php echo $_SESSION['alert']['type'] === 'success' ? 'fa-check' : 'fa-exclamation' ?> text-3xl <?php echo $_SESSION['alert']['type'] === 'success' ? 'text-green-500' : 'text-yellow-500' ?>"></i>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 mb-2">
                        <?php echo $_SESSION['alert']['type'] === 'success' ? 'Success!' : 'Warning!' ?>
                    </h3>
                    <p class="text-gray-600 text-center mb-6">
                        <?php echo $_SESSION['alert']['message'] ?>
                    </p>
                    <button onclick="closeAlert()" class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors duration-200">
                        OK
                    </button>
                </div>
                <div class="w-full bg-gray-100 h-1">
                    <div id="progress-bar" class="bg-gray-900 h-1 transition-all duration-300 w-full"></div>
                </div>
            </div>
        </div>
        <script>
            let progressBar = document.getElementById('progress-bar');
            let width = 100;
            let interval = setInterval(() => {
                width -= 1;
                progressBar.style.width = width + '%';
                if (width <= 0) {
                    clearInterval(interval);
                    closeAlert();
                }
            }, 30);

            function closeAlert() {
                let alert = document.getElementById('alert');
                alert.classList.add('translate-x-full', 'opacity-0');
                setTimeout(() => alert.remove(), 300);
            }
        </script>
        <?php unset($_SESSION['alert']); endif; ?>

        <!-- Login Form -->
        <div class="max-w-md w-full space-y-8 bg-white p-8 rounded-xl shadow-2xl">
            <div>
                <a href="../index.php" class="flex items-center justify-center mb-8">
                    <i class="fas fa-store text-3xl text-blue-500 mr-2"></i>
                    <h2 class="text-3xl font-bold text-gray-900">Kevin's Collection</h2>
                </a>
                <h2 class="text-center text-2xl font-extrabold text-gray-900 mb-2">Sign in to your account</h2>
                <p class="text-center text-sm text-gray-600">
                    Or
                    <a href="user_registration.php" class="font-medium text-blue-600 hover:text-blue-500">
                        create a new account
                    </a>
                </p>
            </div>
            <form class="mt-8 space-y-6" action="" method="post">
                <div class="rounded-md shadow-sm space-y-4">
                    <div>
                        <label for="user_username" class="block text-sm font-medium text-gray-700">
                            Username
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-user text-gray-400"></i>
                            </div>
                            <input id="user_username" name="user_username" type="text" required 
                                   class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your username">
                        </div>
                    </div>
                    <div>
                        <label for="user_pw" class="block text-sm font-medium text-gray-700">
                            Password
                        </label>
                        <div class="mt-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <i class="fas fa-lock text-gray-400"></i>
                            </div>
                            <input id="user_pw" name="user_pw" type="password" required 
                                   class="appearance-none block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Enter your password">
                        </div>
                    </div>
                </div>

                <div>
                    <a href="forgot_password.php"><span class="mb-2">forgot password?</span></a>
                    <button type="submit" name="user_login"
                            class="group relative w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition duration-200">
                        <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                            <i class="fas fa-sign-in-alt text-gray-300 group-hover:text-gray-200"></i>
                        </span>
                        Sign in
                    </button>
                </div>
            </form>

            <div class="text-center">
                <a href="../index.php" class="text-sm font-medium text-gray-600 hover:text-gray-500">
                    <i class="fas fa-arrow-left mr-1"></i>
                    Back to Home
                </a>
            </div>
        </div>
    </div>

    <script>
        const { createApp } = Vue
        createApp({
            data() {
                return {
                    showPassword: false
                }
            }
        }).mount('#app')
    </script>
</body>
</html>
