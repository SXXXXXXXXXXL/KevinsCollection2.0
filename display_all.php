<?php
session_start();
include('include/connect.php');
include('functions/common_function.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>All Products - Kevin's Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div id="app" class="relative">
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
                    <button onclick="closeAlert()" 
                            class="px-6 py-2 bg-gray-900 text-white rounded-lg hover:bg-gray-800 transition-colors duration-200">
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

        <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="index.php" class="flex items-center space-x-2">
                            <i class="fas fa-store text-2xl text-blue-500"></i>
                            <span class="text-white font-bold text-lg">Kevin's Collection</span>
                        </a>
                        
                        <div class="hidden sm:flex sm:ml-6 space-x-1">
                            <a href="index.php" 
                               class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                                      <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 
                                      'text-white bg-gray-700' : 
                                      'text-gray-300 hover:text-white hover:bg-gray-700' ?>">
                                Home
                            </a>
                            <a href="display_all.php" 
                               class="px-3 py-2 text-sm font-medium rounded-md transition-colors duration-200
                                      <?php echo basename($_SERVER['PHP_SELF']) == 'display_all.php' ? 
                                      'text-white bg-gray-700' : 
                                      'text-gray-300 hover:text-white hover:bg-gray-700' ?>">
                                Products
                            </a>
                            <div class="relative" @mouseenter="contactsOpen = true" @mouseleave="contactsOpen = false">
                                <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">
                                    Contacts
                                </button>
                                <div v-show="contactsOpen" 
                                     class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50 transform transition-all duration-200">
                                    <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-md">
                                        <i class="far fa-envelope mr-2"></i>E-mail
                                    </a>
                                    <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-md">
                                        <i class="fab fa-whatsapp mr-2"></i>Whatsapp
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right side navigation -->
                    <div class="flex items-center space-x-4">
                        <!-- Search -->
                        <form action="search_product.php" method="get" class="hidden md:flex items-center">
                            <div class="relative">
                                <input type="search" name="search_data" placeholder="Search products..." 
                                       class="w-64 px-4 py-1 rounded-full bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" name="search_data_product" class="absolute right-0 top-0 mt-1 mr-2">
                                    <i class="fas fa-search text-gray-400"></i>
                                </button>
                            </div>
                        </form>

                        <!-- Cart -->
                        <a href="cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative group">
                            <i class="fa-solid fa-cart-shopping text-xl group-hover:text-blue-500 transition-colors duration-200"></i>
                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                <?php cart_item() ?>
                            </span>
                            <span class="text-gray-300 text-sm hidden md:inline-block ml-2">
                                Rp.<?php total_cart_price(); ?>
                            </span>
                        </a>

                        <!-- Auth Buttons -->
                        <div class="hidden sm:flex items-center space-x-2">
                            <?php if (isset($_SESSION['username'])): ?>
                                <a href="user/profile.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium">
                                    <i class="far fa-user mr-1"></i>My Account
                                </a>
                                <a href="user/logout.php" class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-red-600 transition-colors duration-200">
                                    Logout
                                </a>
                            <?php else: ?>
                                <a href="user/user_login.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium">
                                    <i class="fas fa-sign-in-alt mr-1"></i>Login
                                </a>
                                <a href="user/user_registration.php" class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-blue-600 transition-colors duration-200">
                                    Register
                                </a>
                            <?php endif; ?>
                        </div>

                        <!-- Mobile menu button -->
                        <button @click="mobileMenu = !mobileMenu" class="sm:hidden text-gray-300 hover:text-white">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>

                <!-- Mobile menu -->
                <div v-show="mobileMenu" class="sm:hidden">
                    <div class="px-2 pt-2 pb-3 space-y-1">
                        <a href="index.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                            <i class="fas fa-home mr-2"></i>Home
                        </a>
                        <a href="display_all.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                            <i class="fas fa-shopping-bag mr-2"></i>Products
                        </a>
                        <?php if (isset($_SESSION['username'])): ?>
                            <a href="user/profile.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                                <i class="far fa-user mr-2"></i>My Account
                            </a>
                            <a href="user/logout.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                                <i class="fas fa-sign-out-alt mr-2"></i>Logout
                            </a>
                        <?php else: ?>
                            <a href="user/user_login.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                                <i class="fas fa-sign-in-alt mr-2"></i>Login
                            </a>
                            <a href="user/user_registration.php" class="text-gray-300 hover:text-white block px-3 py-2 text-base font-medium rounded-md hover:bg-gray-700">
                                <i class="fas fa-user-plus mr-2"></i>Register
                            </a>
                        <?php endif; ?>
                        
                        <!-- Mobile Search -->
                        <form action="search_product.php" method="get" class="px-3 py-2">
                            <div class="relative">
                                <input type="search" name="search_data" placeholder="Search products..." 
                                       class="w-full px-4 py-2 rounded-full bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <button type="submit" name="search_data_product" class="absolute right-0 top-0 mt-2 mr-3">
                                    <i class="fas fa-search text-gray-400"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </nav>

        <?php cart(); ?>

        <!-- Hero Section -->
        <div class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h1 class="text-4xl font-bold text-gray-900 mb-4">Kevin's Collection</h1>
                <p class="text-xl text-gray-600 mb-8">Welcome, have already figured what you are looking for?</p>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- Products Grid -->
                <div class="col-span-12 lg:col-span-9">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php
                        global $con;

                        // Display products based on condition
                        if(isset($_GET['brand'])) {
                            $brand_id = $_GET['brand'];
                            $select_query = "SELECT * FROM products WHERE brand_id = $brand_id";
                            $result_query = mysqli_query($con, $select_query);
                            if(mysqli_num_rows($result_query) == 0) {
                                echo "<p class='col-span-3 text-center text-gray-500 py-8'>No products found in this brand.</p>";
                            }
                            while($row = mysqli_fetch_assoc($result_query)) {
                                display_product(
                                    $row['product_id'],
                                    $row['product_title'],
                                    $row['product_desc'],
                                    $row['product_image1'],
                                    $row['product_price']
                                );
                            }
                        } elseif(isset($_GET['category'])) {
                            $category_id = $_GET['category'];
                            $select_query = "SELECT * FROM products WHERE category_id = $category_id";
                            $result_query = mysqli_query($con, $select_query);
                            if(mysqli_num_rows($result_query) == 0) {
                                echo "<p class='col-span-3 text-center text-gray-500 py-8'>No products found in this category.</p>";
                            }
                            while($row = mysqli_fetch_assoc($result_query)) {
                                display_product(
                                    $row['product_id'],
                                    $row['product_title'],
                                    $row['product_desc'],
                                    $row['product_image1'],
                                    $row['product_price']
                                );
                            }
                        } else {
                            $select_query = "SELECT * FROM products WHERE (available_until IS NULL OR available_until < NOW())";
                            $result_query = mysqli_query($con, $select_query);
                            while($row = mysqli_fetch_assoc($result_query)) {
                                display_product(
                                    $row['product_id'],
                                    $row['product_title'],
                                    $row['product_desc'],
                                    $row['product_image1'],
                                    $row['product_price']
                                );
                            }
                        }
                        ?>
                    </div>
                </div>

                <!-- Sidebar -->
                <div class="col-span-12 lg:col-span-3 space-y-6">
                    <!-- Brands -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="bg-gray-900 text-white py-3 px-4 text-lg font-semibold">Brands</h2>
                        <ul class="divide-y divide-gray-200">
                            <?php 
                            $select_brands = "SELECT * FROM brands";
                            $result_brands = mysqli_query($con, $select_brands);
                            while($row_data = mysqli_fetch_assoc($result_brands)) {
                                $brand_title = $row_data['brand_title'];
                                $brand_id = $row_data['brand_id'];
                                echo "
                                <li>
                                    <a href='index.php?brand=$brand_id' 
                                       class='block px-4 py-3 text-gray-700 hover:bg-gray-50 border-l-4 " . 
                                       (isset($_GET['brand']) && $_GET['brand'] == $brand_id ? "border-blue-500" : "border-transparent") . 
                                       " hover:border-blue-500 transition duration-200'>
                                        $brand_title
                                    </a>
                                </li>";
                            }
                            ?>
                        </ul>
                    </div>

                    <!-- Categories -->
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="bg-gray-900 text-white py-3 px-4 text-lg font-semibold">Categories</h2>
                        <ul class="divide-y divide-gray-200">
                            <?php 
                            $select_categories = "SELECT * FROM categories";
                            $result_categories = mysqli_query($con, $select_categories);
                            while($row_data = mysqli_fetch_assoc($result_categories)) {
                                $category_title = $row_data['category_title'];
                                $category_id = $row_data['category_id'];
                                echo "
                                <li>
                                    <a href='index.php?category=$category_id' 
                                       class='block px-4 py-3 text-gray-700 hover:bg-gray-50 border-l-4 " .
                                       (isset($_GET['category']) && $_GET['category'] == $category_id ? "border-blue-500" : "border-transparent") .
                                       " hover:border-blue-500 transition duration-200'>
                                        $category_title
                                    </a>
                                </li>";
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <?php include('./include/footer.php') ?>
    </div>

    <script>
        const { createApp } = Vue
        
        createApp({
            data() {
                return {
                    mobileMenu: false,
                    contactsOpen: false,
                    showAlert: false,
                    alertMessage: '',
                    alertType: 'success',
                    alertProgress: 100,
                    alertTimeout: null,
                    progressInterval: null
                }
            },
            methods: {
                showAlertMessage(message, type = 'success') {
                    this.alertMessage = message;
                    this.alertType = type;
                    this.showAlert = true;
                    this.alertProgress = 100;

                    // Clear any existing timeouts
                    if (this.alertTimeout) {
                        clearTimeout(this.alertTimeout);
                        clearInterval(this.progressInterval);
                    }

                    // Progress bar
                    const duration = 3000; 
                    const intervalTime = 10;
                    const steps = duration / intervalTime;
                    const progressStep = 100 / steps;

                    this.progressInterval = setInterval(() => {
                        this.alertProgress -= progressStep;
                    }, intervalTime);

                    // Hide alert after duration
                    this.alertTimeout = setTimeout(() => {
                        this.showAlert = false;
                        clearInterval(this.progressInterval);
                    }, duration);
                }
            }
        }).mount('#app')

        // Global function for PHP to call
        function showAlert(message, type = 'success') {
            const app = document.querySelector('#app').__vue_app__;
            app.config.globalProperties.showAlertMessage(message, type);
        }
    </script>
</body>
</html>
