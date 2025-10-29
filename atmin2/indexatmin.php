<?php
include('../include/connect.php');
include('../functions/common_function.php');
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Admin Dashboard - Kevin's Collection</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="bg-gray-100 min-h-screen flex flex-col">
  <div id="app" class="flex-grow">
    <nav class="bg-gray-900 shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16 items-center">
          <div class="flex items-center space-x-4">
            <?php if(!isset($_SESSION['username'])): ?>
              <span class="text-gray-300">Welcome Guest</span>
              <a href="../user/user_login.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Login</a>
            <?php else: ?>
              <span class="text-gray-300">Welcome <?php echo htmlspecialchars($_SESSION['username']); ?></span>
              <a href="logout_admin.php" class="text-gray-300 hover:text-white px-3 py-2 rounded-md text-sm font-medium">Logout</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </nav>

    <header class="bg-white shadow">
      <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-3xl font-bold text-gray-900">Manage Details</h1>
      </div>
    </header>

    <main class="max-w-7xl mx-auto p-4 w-full">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4">
          
          <a href="indexatmin.php?insert_products" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-plus mr-2"></i> Insert Products
          </a>
          <a href="indexatmin.php?view_products" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-eye mr-2"></i> View Products
          </a>
          <a href="indexatmin.php?insert_category" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-plus mr-2"></i> Insert Category
          </a>
          <a href="indexatmin.php?view_category" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-eye mr-2"></i> View Category
          </a>
          <a href="indexatmin.php?insert_brand" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-plus mr-2"></i> Insert Brands
          </a>
          <a href="indexatmin.php?view_brand" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-eye mr-2"></i> View Brands
          </a>
          <a href="indexatmin.php?list_orders" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-list mr-2"></i> All Orders
          </a>
          <a href="indexatmin.php?list_payments" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-credit-card mr-2"></i> All Payments
          </a>
          <a href="indexatmin.php?list_users" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-users mr-2"></i> List Users
          </a>
          <a href="indexatmin.php?payment_report" class="bg-gray-800 text-white hover:bg-gray-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center">
            <i class="fas fa-file-alt mr-2"></i> Payment Report 
          </a>
          <a href="logout_admin.php" class="bg-red-600 text-white hover:bg-red-700 font-medium px-4 py-3 rounded-lg shadow-sm transition duration-200 inline-flex items-center justify-center text-center col-span-2 sm:col-span-1 md:col-span-1 lg:col-span-1">
            <i class="fas fa-sign-out-alt mr-2"></i> Logout
          </a>
        </div>
      </div>

      <div class="mt-6 bg-white p-6 rounded-lg shadow">
        <?php
        // ... (Blok include PHP tidak berubah) ...
        if(isset($_GET['insert_products'])){ include ('insert_products.php'); }
        if(isset($_GET['insert_category'])){ include ('insert_cat.php'); }
        if(isset($_GET['insert_brand'])){ include ('insert_brands.php'); }
        if(isset($_GET['view_products'])){ include ('view_products.php'); }
        if(isset($_GET['edit_products'])){ include ('edit_products.php'); }
        if(isset($_GET['delete_products'])){ include ('delete_products.php'); }
        if(isset($_GET['view_category'])){ include ('view_category.php'); }
        if(isset($_GET['view_brand'])){ include ('view_brand.php'); }
        if(isset($_GET['edit_category'])){ include ('edit_category.php'); }
        if(isset($_GET['delete_category'])){ include ('delete_category.php'); }
        if(isset($_GET['edit_brand'])){ include ('edit_brand.php'); }
        if(isset($_GET['delete_brand'])){ include ('delete_brand.php'); }
        if(isset($_GET['list_orders'])){ include ('list_orders.php'); }
        if(isset($_GET['delete_order'])){ include ('delete_order.php'); }
        if(isset($_GET['list_payments'])){ include ('list_payments.php'); }
        if(isset($_GET['list_users'])){ include ('list_users.php'); }
        if(isset($_GET['delete_user'])){ include ('delete_user.php'); }
        if(isset($_GET['delete_payment'])){ include ('delete_payment.php'); }
        if(isset($_GET['payment_report'])){ include ('payment_report.php'); }
        ?>
      </div>
    </main>
  </div>
  
  </body>
</html>