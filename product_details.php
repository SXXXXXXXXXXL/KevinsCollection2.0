<?php
include('include/connect.php');
include('functions/common_function.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Details - Kevin's Collection</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-50">
    <div id="app">
        <!-- Navbar -->
        <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <!-- Left side -->
                    <div class="flex items-center">
                        <!-- Logo/Brand -->
                        <a href="index.php" class="flex items-center space-x-2">
                            <i class="fas fa-store text-2xl text-blue-500"></i>
                            <span class="text-white font-bold text-lg">Kevin's Collection</span>
                        </a>
                        
                        <!-- Desktop Navigation -->
                        <div class="hidden sm:flex sm:ml-6 space-x-1">
                            <a href="index.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Home</a>
                            <a href="display_all.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Products</a>
                            <?php if (isset($_SESSION['username'])): ?>
                                <a href="user/profile.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">My Account</a>
                            <?php else: ?>
                                <a href="user/user_registration.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Register</a>
                            <?php endif; ?>
                            
                            <!-- Contacts Dropdown -->
                            <div class="relative" @mouseenter="contactsOpen = true" @mouseleave="contactsOpen = false">
                                <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">
                                    Contacts
                                </button>
                                <div v-show="contactsOpen" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                        <i class="far fa-envelope mr-2"></i>E-mail
                                    </a>
                                    <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
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
                                    <i class="far fa-user mr-1"></i><?php echo $_SESSION['username']; ?>
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
        <div class="bg-white py-8">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h1 class="text-3xl font-bold text-gray-900">Product Details</h1>
            </div>
        </div>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-6">
                <!-- Product Details -->
                <div class="col-span-12 lg:col-span-9">
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <?php
                        if(isset($_GET['product_id'])){
                            $product_id = $_GET['product_id'];
                            $select_query = "SELECT * FROM products WHERE product_id=$product_id";
                            $result_query = mysqli_query($con, $select_query);
                            while($row = mysqli_fetch_assoc($result_query)){
                                $product_title = $row['product_title'];
                                $product_desc = $row['product_desc'];
                                $product_image1 = $row['product_image1'];
                                $product_image2 = $row['product_image2'];
                                $product_image3 = $row['product_image3'];
                                $product_price = $row['product_price'];
                                
                                echo "
                                <div class='grid grid-cols-1 md:grid-cols-2 gap-8 p-6'>
                                    <!-- Main Image -->
                                    <div class='space-y-4'>
                                        <div class='aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-gray-100'>
                                            <img src='./atmin/product_images/$product_image1' class='w-full h-96 object-contain rounded-lg' alt='$product_title'>
                                        </div>
                                        <div class='grid grid-cols-2 gap-4'>
                                            <div class='aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-gray-100'>
                                                <img src='./atmin/product_images/$product_image2' class='w-full h-48 object-cover rounded-lg' alt='$product_title'>
                                            </div>
                                            <div class='aspect-w-1 aspect-h-1 rounded-lg overflow-hidden bg-gray-100'>
                                                <img src='./atmin/product_images/$product_image3' class='w-full h-48 object-cover rounded-lg' alt='$product_title'>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Product Info -->
                                    <div class='space-y-6'>
                                        <h1 class='text-3xl font-bold text-gray-900'>$product_title</h1>
                                        <p class='text-2xl font-bold text-blue-600'>Rp.$product_price</p>
                                        <div class='prose max-w-none'>
                                            <p class='text-gray-600'>$product_desc</p>
                                        </div>
                                        <div class='flex space-x-4'>
                                            <a href='index.php?add_to_cart=$product_id' 
                                               class='flex-1 bg-gray-900 text-white py-3 px-8 rounded-lg hover:bg-gray-800 transition duration-200 text-center font-medium'>
                                                <i class='fas fa-shopping-cart mr-2'></i>Add to Cart
                                            </a>
                                            <a href='index.php' 
                                               class='flex-1 border border-gray-300 text-gray-700 py-3 px-8 rounded-lg hover:bg-gray-50 transition duration-200 text-center font-medium'>
                                                <i class='fas fa-home mr-2'></i>Continue Shopping
                                            </a>
                                        </div>
                                    </div>
                                </div>";
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
                    contactsOpen: false
                }
            }
        }).mount('#app')
    </script>
</body>
</html>
