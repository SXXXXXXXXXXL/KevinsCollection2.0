<?php
include('include/connect.php');
include('functions/common_function.php');
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Search Results - Kevin's Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <div id="app" class="relative">
        <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
            <div class="max-w-7xl mx-auto px-4">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <a href="index.php" class="flex items-center space-x-2">
                            <i class="fas fa-store text-2xl text-blue-500"></i>
                            <span class="text-white font-bold text-lg">Kevin's Collection</span>
                        </a>
                        <div class="hidden sm:flex sm:ml-6 space-x-1">
                            <a href="index.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700">Home</a>
                            <a href="display_all.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700">Products</a>
                            <div class="relative" @mouseenter="contactsOpen = true" @mouseleave="contactsOpen = false">
                                <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700">Contacts</button>
                                <div v-show="contactsOpen" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                                    <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="far fa-envelope mr-2"></i>E-mail</a>
                                    <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100"><i class="fab fa-whatsapp mr-2"></i>Whatsapp</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center space-x-4">
                        <form action="search_product.php" method="get" class="hidden md:flex items-center">
                            <div class="relative">
                                <input type="search" name="search_data" placeholder="Search products..." class="w-64 px-4 py-1 rounded-full bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500" value="<?php echo isset($_GET['search_data']) ? htmlspecialchars($_GET['search_data']) : '' ?>" />
                                <button type="submit" name="search_data_product" class="absolute right-0 top-0 mt-1 mr-2">
                                    <i class="fas fa-search text-gray-400"></i>
                                </button>
                            </div>
                        </form>
                        <a href="cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative group">
                            <i class="fa-solid fa-cart-shopping text-xl group-hover:text-blue-500"></i>
                            <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"><?php cart_item() ?></span>
                        </a>
                        <div class="hidden sm:flex items-center space-x-2">
                            <?php if (isset($_SESSION['username'])): ?>
                                <a href="user/profile.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="far fa-user mr-1"></i>My Account</a>
                                <a href="user/logout.php" class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-red-600">Logout</a>
                            <?php else: ?>
                                <a href="user/user_login.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
                                <a href="user/user_registration.php" class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-blue-600">Register</a>
                            <?php endif; ?>
                        </div>
                        <button @click="mobileMenu = !mobileMenu" class="sm:hidden text-gray-300 hover:text-white">
                            <i class="fas fa-bars text-xl"></i>
                        </button>
                    </div>
                </div>
                <div v-show="mobileMenu" class="sm:hidden">
                    </div>
            </div>
        </nav>

        <?php cart(); ?>

        <div class="bg-white py-12">
            <div class="max-w-7xl mx-auto px-4 text-center">
                <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-2">Search Results</h1>
                <?php
                if(isset($_GET['search_data'])){
                    echo "<p class='text-lg text-gray-600'>Showing results for: <span class='font-semibold'>" . htmlspecialchars($_GET['search_data']) . "</span></p>";
                }
                ?>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 py-12">
            <div class="grid grid-cols-12 gap-6">
                
                <div class="col-span-12 lg:col-span-9">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <?php
                        search_product();
                        ?>
                    </div>
                </div>

                <div class="col-span-12 lg:col-span-3 space-y-6">
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="bg-gray-900 text-white py-3 px-4 text-lg font-semibold">Brands</h2>
                        <ul class="divide-y divide-gray-200">
                            <?php getbrands(); ?>
                        </ul>
                    </div>
                    <div class="bg-white rounded-lg shadow-md overflow-hidden">
                        <h2 class="bg-gray-900 text-white py-3 px-4 text-lg font-semibold">Categories</h2>
                        <ul class="divide-y divide-gray-200">
                            <?php getcategory(); ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <?php include('./include/footer.php') ?>
    </div>
    
    <script>
        const { createApp } = Vue;
        createApp({ 
            data() {
                return {
                    mobileMenu: false,
                    contactsOpen: false
                }
            }
        }).mount('#app');
    </script>
</body>
</html>