    <?php
include('include/connect.php');
include('functions/common_function.php');
session_start();

if(!isset($_SESSION['username'])){
    echo "<script>alert('Please login first')</script>";
    echo "<script>window.open('user/user_login.php','_self')</script>";
    exit();
}

// Get user details
$username = $_SESSION['username'];
$get_user = "SELECT * FROM user_table WHERE username='$username'";
$result = mysqli_query($con, $get_user);
$row_fetch = mysqli_fetch_assoc($result);
$user_id = $row_fetch['user_id'];
$user_address = $row_fetch['user_address'];
$user_mobile = $row_fetch['user_mobile'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Kevin's Collection</title>
    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- Vue.js -->
    <script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div id="app">
        <!-- Navbar - Shopee Style -->
        <nav class="bg-orange-500">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between h-16">
                    <div class="flex items-center">
                        <a href="index.php" class="text-white text-2xl font-bold">
                            <i class="fas fa-shopping-cart mr-2"></i>
                            CHECKOUT
                        </a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 py-8">
            <!-- Progress Bar -->
            <div class="mb-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center">1</div>
                        <div class="ml-2 text-orange-500 font-medium">Address</div>
                    </div>
                    <div class="flex-1 mx-4 h-1 bg-orange-500"></div>
                    <div class="flex items-center">
                        <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center">2</div>
                        <div class="ml-2 text-orange-500 font-medium">Payment</div>
                    </div>
                </div>
            </div>

            <!-- Shipping Address -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-orange-500 text-xl mr-2"></i>
                        <h2 class="text-lg font-semibold">Shipping Address</h2>
                    </div>
                </div>
                <div class="ml-8">
                    <p class="font-medium text-gray-800"><?php echo $username ?></p>
                    <p class="text-gray-600"><?php echo $user_mobile ?></p>
                    <p class="text-gray-600"><?php echo $user_address ?></p>
                </div>
            </div>

            <!-- Products -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shopping-bag text-orange-500 text-xl mr-2"></i>
                    <h2 class="text-lg font-semibold">Products Ordered</h2>
                </div>
                
                <?php
                $ip = getIPAddress();
                $total = 0;
                $cart_query = "SELECT * FROM cart_details WHERE ip_address='$ip'";
                $result = mysqli_query($con, $cart_query);
                $result_count = mysqli_num_rows($result);
                if($result_count > 0){
                    while($row = mysqli_fetch_array($result)){
                        $product_id = $row['product_id'];
                        $select_products = "SELECT * FROM products WHERE product_id='$product_id'";
                        $result_products = mysqli_query($con, $select_products);
                        while($row_product_price = mysqli_fetch_array($result_products)){
                            $product_price = $row_product_price['product_price'];
                            $product_title = $row_product_price['product_title'];
                            $product_image1 = $row_product_price['product_image1'];
                            $total += $product_price;
                ?>
                <div class="flex items-center py-4 border-b">
                    <img src="./atmin/product_images/<?php echo $product_image1 ?>" 
                         class="w-20 h-20 object-cover rounded-md" alt="<?php echo $product_title ?>">
                    <div class="ml-4 flex-grow">
                        <h3 class="font-medium text-gray-800"><?php echo $product_title ?></h3>
                        <p class="text-orange-500 font-medium">Rp.<?php echo $product_price ?></p>
                    </div>
                </div>
                <?php
                        }
                    }
                }
                ?>
            </div>

            <!-- Shipping Method -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-truck text-orange-500 text-xl mr-2"></i>
                    <h2 class="text-lg font-semibold">Shipping Method</h2>
                </div>
                <div class="space-y-3">
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50" 
                           :class="{ 'border-orange-500 bg-orange-50': shipping === 'jne' }">
                        <input type="radio" v-model="shipping" value="jne" class="text-orange-500">
                        <span class="ml-2">
                            <span class="font-medium">JNE Regular (2-3 days)</span>
                            <span class="block text-sm text-gray-500">Rp.10,000</span>
                        </span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="{ 'border-orange-500 bg-orange-50': shipping === 'jnt' }">
                        <input type="radio" v-model="shipping" value="jnt" class="text-orange-500">
                        <span class="ml-2">
                            <span class="font-medium">J&T Express (1-2 days)</span>
                            <span class="block text-sm text-gray-500">Rp.12,000</span>
                        </span>
                    </label>
                    <label class="flex items-center p-3 border rounded-lg cursor-pointer hover:bg-gray-50"
                           :class="{ 'border-orange-500 bg-orange-50': shipping === 'sicepat' }">
                        <input type="radio" v-model="shipping" value="sicepat" class="text-orange-500">
                        <span class="ml-2">
                            <span class="font-medium">SiCepat Express (1 day)</span>
                            <span class="block text-sm text-gray-500">Rp.15,000</span>
                        </span>
                    </label>
                </div>
            </div>

            <!-- Payment Summary -->
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-receipt text-orange-500 text-xl mr-2"></i>
                    <h2 class="text-lg font-semibold">Payment Summary</h2>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between text-gray-600">
                        <span>Merchandise Subtotal:</span>
                        <span>Rp.<?php echo $total ?></span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Shipping Fee:</span>
                        <span>{{ shippingCost }}</span>
                    </div>
                    <div class="flex justify-between text-gray-600">
                        <span>Total Payment:</span>
                        <span class="text-xl font-bold text-orange-500">Rp.{{ totalPayment }}</span>
                    </div>
                </div>
            </div>

            <!-- Place Order Button -->
            <form action="" method="post" class="sticky bottom-0 bg-white p-4 border-t shadow-lg">
                <input type="hidden" name="shipping_method" :value="shipping">
                <input type="hidden" name="shipping_cost" :value="getShippingCost">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div class="text-gray-600">
                        Total: <span class="text-xl font-bold text-orange-500">Rp.{{ totalPayment }}</span>
                    </div>
                    <button type="submit" name="confirm_order" 
                            class="bg-orange-500 text-white px-8 py-3 rounded-lg hover:bg-orange-600 transition duration-200"
                            :disabled="!shipping">
                        Place Order
                    </button>
                </div>
            </form>
        </div>
    </div>

    <?php
    if(isset($_POST['confirm_order'])){
        if(!isset($_POST['shipping_method']) || empty($_POST['shipping_method'])){
            echo "<script>alert('Please select a shipping method')</script>";
            exit();
        }

        $shipping_method = $_POST['shipping_method'];
        $shipping_cost = $_POST['shipping_cost'];
        $order_date = date('Y-m-d H:i:s');
        $status = 'pending';

        // Get cart items
        $ip = getIPAddress();
        $cart_query = "SELECT * FROM cart_details WHERE ip_address='$ip'";
        $result = mysqli_query($con, $cart_query);
        
        $total_products = mysqli_num_rows($result);
        if($total_products == 0){
            echo "<script>alert('Your cart is empty')</script>";
            echo "<script>window.open('cart.php','_self')</script>";
            exit();
        }

        // Calculate total amount
        $total_amount = $total + intval($shipping_cost);
        
        // Generate invoice number
        $invoice_number = 'INV-' . date('YmdHis') . '-' . rand(100,999);

        // Insert order
        $insert_order = "INSERT INTO user_orders (user_id, amount_due, invoice_number, total_products, order_date, order_status, shipping_method, shipping_cost) 
                        VALUES ($user_id, $total_amount, '$invoice_number', $total_products, '$order_date', '$status', '$shipping_method', $shipping_cost)";
        
        if(mysqli_query($con, $insert_order)){
            $order_id = mysqli_insert_id($con);
            
            // Move cart items to orders_pending
            while($row = mysqli_fetch_array($result)){
                $product_id = $row['product_id'];
                $insert_pending = "INSERT INTO orders_pending (user_id, order_id, product_id, order_status) 
                                 VALUES ($user_id, $order_id, $product_id, '$status')";
                mysqli_query($con, $insert_pending);
            }
            
            // Clear cart
            $delete_cart = "DELETE FROM cart_details WHERE ip_address='$ip'";
            mysqli_query($con, $delete_cart);
            
            echo "<script>alert('Order placed successfully!')</script>";
            echo "<script>window.open('user/profile.php?my_orders','_self')</script>";
        } else {
            echo "<script>alert('Error placing order')</script>";
        }
    }
    ?>

    <script>
        const { createApp } = Vue
        createApp({
            data() {
                return {
                    shipping: '',
                    subtotal: <?php echo $total ?>,
                    shippingCosts: {
                        'jne': 10000,
                        'jnt': 12000,
                        'sicepat': 15000
                    }
                }
            },
            computed: {
                shippingCost() {
                    return this.shipping ? `Rp.${this.shippingCosts[this.shipping].toLocaleString()}` : 'Rp.0'
                },
                totalPayment() {
                    const shippingCost = this.shipping ? this.shippingCosts[this.shipping] : 0
                    return (this.subtotal + shippingCost).toLocaleString()
                },
                getShippingCost() {
                    return this.shipping ? this.shippingCosts[this.shipping] : 0
                }
            }
        }).mount('#app')
    </script>
</body>
</html>
