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
  <title>Cart - Kevin's Collection</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
  <style>
    .cart_img {
      width: 100px;
      height: 100px;
      object-fit: cover;
      border-radius: 0.375rem; /* rounded-md */
    }
  </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
  <div id="app" class="relative flex-grow">
    <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <div class="flex items-center">
            <a href="index.php" class="flex items-center space-x-2">
              <i class="fas fa-store text-2xl text-blue-500"></i>
              <span class="text-white font-bold text-lg">Kevin's Collection</span>
            </a>
            <div class="hidden sm:flex sm:ml-6 space-x-1">
              <a href="index.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Home</a>
              <a href="display_all.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Products</a>
              <div class="relative" @mouseenter="contactsOpen = true" @mouseleave="contactsOpen = false">
                <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Contacts</button>
                <div v-show="contactsOpen" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                  <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-md"><i class="far fa-envelope mr-2"></i>E-mail</a>
                  <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-md"><i class="fab fa-whatsapp mr-2"></i>Whatsapp</a>
                </div>
              </div>
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <a href="cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative group">
              <i class="fa-solid fa-cart-shopping text-xl group-hover:text-blue-500 transition-colors duration-200"></i>
              <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"><?php cart_item() ?></span>
              <span class="text-gray-300 text-sm hidden md:inline-block ml-2">Rp.<?php total_cart_price(); ?></span>
            </a>
            <div class="hidden sm:flex items-center space-x-2">
              <?php if (isset($_SESSION['username'])): ?>
                <a href="user/profile.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="far fa-user mr-1"></i>My Account</a>
                <a href="user/logout.php" class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-red-600 transition-colors duration-200">Logout</a>
              <?php else: ?>
                <a href="user/user_login.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
                <a href="user/user_registration.php" class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-blue-600 transition-colors duration-200">Register</a>
              <?php endif; ?>
            </div>
            <button @click="mobileMenu = !mobileMenu" class="sm:hidden text-gray-300 hover:text-white"><i class="fas fa-bars text-xl"></i></button>
          </div>
        </div>
        <div v-show="mobileMenu" class="sm:hidden px-2 pt-2 pb-3 space-y-1">
          <a href="index.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-home mr-2"></i>Home</a>
          <a href="display_all.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-shopping-bag mr-2"></i>Products</a>
          <?php if (isset($_SESSION['username'])): ?>
            <a href="user/profile.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="far fa-user mr-2"></i>My Account</a>
            <a href="user/logout.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
          <?php else: ?>
            <a href="user/user_login.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
            <a href="user/user_registration.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-user-plus mr-2"></i>Register</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-12">
      <h2 class="text-3xl font-bold text-gray-900 mb-6 text-center">Your Shopping Cart</h2>
      <form action="" method="post" class="overflow-x-auto">
        <table class="min-w-full bg-white rounded-lg shadow-md overflow-hidden">
          <thead class="bg-gray-900 text-white">
            <tr>
              <th class="py-3 px-6 text-left">Product Title</th>
              <th class="py-3 px-6 text-left">Product Image</th>
              <th class="py-3 px-6 text-left">Price</th>
              <th class="py-3 px-6 text-center">Remove</th>
            </tr>
          </thead>
          <tbody>
            <?php
            global $con;
            $get_ip_add = getIPAddress();
            $total = 0;
            $cart_query = "SELECT * FROM cart_details WHERE ip_address='$get_ip_add'";
            $result_query = mysqli_query($con, $cart_query);
            $result_count = mysqli_num_rows($result_query);
            if($result_count > 0){
              while($row = mysqli_fetch_array($result_query)){
                $product_id = $row['product_id'];
                $select_products = "SELECT * FROM products WHERE product_id='$product_id'";
                $result_products = mysqli_query($con, $select_products);
                while($row_product_price = mysqli_fetch_array($result_products)){
                  $price_table = $row_product_price['product_price'];
                  $product_title = $row_product_price['product_title'];
                  $product_image1 = $row_product_price['product_image1'];
                  $total += $price_table;
                  echo "<tr class='border-b'>
                          <td class='py-4 px-6'>$product_title</td>
                          <td class='py-4 px-6'><img src='./atmin/product_images/$product_image1' alt='$product_title' class='cart_img'></td>
                          <td class='py-4 px-6'>Rp. $price_table</td>
                          <td class='py-4 px-6 text-center'>
                            <input type='checkbox' name='removeitem[]' value='$product_id' class='form-checkbox h-5 w-5 text-red-600'>
                          </td>
                        </tr>";
                }
              }
            } else {
              echo "<tr><td colspan='4' class='text-center py-6 text-gray-500'>Your cart is empty. Add some products!</td></tr>";
            }
            ?>
          </tbody>
        </table>

        <div class="flex flex-wrap items-center justify-between mt-6 space-y-3">
          <?php if($result_count > 0): ?>
            <h3 class="text-xl font-semibold">Subtotal: <span class="text-blue-600">Rp. <?php echo $total; ?></span></h3>
            <div class="space-x-2">
              <button type="submit" name="continue_shop" class="bg-gray-200 text-gray-800 px-4 py-2 rounded hover:bg-gray-300 transition duration-200">Continue Shopping</button>
              <button type="submit" name="checkout" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Checkout</button>
              <button type="submit" name="remove_cart" class="bg-red-600 text-white px-4 py-2 rounded hover:bg-red-700 transition duration-200">Remove Selected</button>
            </div>
          <?php else: ?>
            <button type="submit" name="continue_shop" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200">Continue Shopping</button>
          <?php endif; ?>
        </div>
      </form>
    </div>

    <?php
    if(isset($_POST['continue_shop'])){
      echo "<script>window.open('index.php','_self')</script>";
    }
    if(isset($_POST['checkout'])){
      echo "<script>window.open('user/checkout.php','_self')</script>";
    }
    if(isset($_POST['remove_cart'])){
      if(!empty($_POST['removeitem'])){
        foreach($_POST['removeitem'] as $remove_id){
          $delete_query = "DELETE FROM cart_details WHERE product_id=$remove_id";
          $run_delete = mysqli_query($con, $delete_query);
        }
        echo "<script>window.open('cart.php','_self')</script>";
      }
    }
    ?>

  </div>

  <!-- Footer -->
  <?php include('./include/footer.php') ?>

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
