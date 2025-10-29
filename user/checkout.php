<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('../include/connect.php');
include('../functions/common_function.php');
date_default_timezone_set('Asia/Jakarta');


if(!isset($_SESSION['username'])){
    echo "<script>alert('Please login first'); window.open('user_login.php','_self');</script>";
    exit();
}

$username = $_SESSION['username'];
$stmt_user = mysqli_prepare($con, "SELECT * FROM user_table WHERE username = ?");
mysqli_stmt_bind_param($stmt_user, "s", $username);
mysqli_stmt_execute($stmt_user);
$result_user = mysqli_stmt_get_result($stmt_user);
$row_fetch = mysqli_fetch_assoc($result_user);
$user_id = $row_fetch['user_id'];
$user_address = $row_fetch['user_address'];
$user_mobile = $row_fetch['user_mobile'];
mysqli_stmt_close($stmt_user);

$ip = getIPAddress();
$total_price = 0;
$total_weight = 0;
$products_in_cart = [];

$cart_query = "SELECT p.*, cd.quantity FROM cart_details cd JOIN products p ON cd.product_id = p.product_id WHERE cd.ip_address = ?";
$stmt_cart = mysqli_prepare($con, $cart_query);
mysqli_stmt_bind_param($stmt_cart, "s", $ip);
mysqli_stmt_execute($stmt_cart);
$result_cart = mysqli_stmt_get_result($stmt_cart);

if(mysqli_num_rows($result_cart) > 0){
    while($row_product = mysqli_fetch_assoc($result_cart)){
        $products_in_cart[] = $row_product;
        $quantity = $row_product['quantity'] > 0 ? $row_product['quantity'] : 1;
        $total_price += $row_product['product_price'] * $quantity;
        $total_weight += ($row_product['product_weight'] ?? 500) * $quantity;
    }
} else {
    echo "<script>alert('Your cart is empty'); window.open('../cart.php','_self');</script>";
    exit();
}
mysqli_stmt_close($stmt_cart);

if(isset($_POST['confirm_order'])){
    if(!isset($_POST['shipping_method']) || empty($_POST['shipping_method']) || !isset($_POST['shipping_cost']) || $_POST['shipping_cost'] === ''){
        echo "<script>alert('Please select a shipping method')</script>";
    } else {
        $shipping_method = $_POST['shipping_method'];
        $shipping_cost = (int)$_POST['shipping_cost'];
        $order_date = date('Y-m-d H:i:s');
        $status = 'Pending';
        $total_amount = $total_price + $shipping_cost;
        $invoice_number = 'INV-' . date('YmdHis') . '-' . rand(100,999);
        
        $total_products_count = 0;
        foreach($products_in_cart as $p) { 
            $total_products_count += ($p['quantity'] > 0 ? $p['quantity'] : 1); 
        }

        $insert_order_query = "INSERT INTO user_orders (user_id, amount_due, invoice_number, total_products, order_date, order_status, ekspedisi) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt_order = mysqli_prepare($con, $insert_order_query);
        mysqli_stmt_bind_param($stmt_order, "idsisss", $user_id, $total_amount, $invoice_number, $total_products_count, $order_date, $status, $shipping_method);
        
        if(!mysqli_stmt_execute($stmt_order)){
            die("Error saat menyimpan pesanan utama: " . mysqli_stmt_error($stmt_order));
        } else {
            $order_id = mysqli_insert_id($con);
            mysqli_stmt_close($stmt_order);

            $insert_pending_query = "INSERT INTO orders_pending (order_id, user_id, invoice_number, product_id, quantity, product_title, product_image, price_at_purchase, order_status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt_pending = mysqli_prepare($con, $insert_pending_query);

            $update_product_query = "UPDATE products SET available_until = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE product_id = ?";
            $stmt_product_update = mysqli_prepare($con, $update_product_query);

            mysqli_stmt_bind_param($stmt_pending, "isssissds", $order_id, $user_id, $invoice_number, $product_id, $quantity, $product_title_to_save, $product_image_to_save, $price_to_save, $status);

            foreach($products_in_cart as $product){
                $product_id = $product['product_id'];
                $quantity = $product['quantity'] > 0 ? $product['quantity'] : 1;
                $product_title_to_save = $product['product_title'];
                $product_image_to_save = $product['product_image1'];
                $price_to_save = $product['product_price'];

                if (!mysqli_stmt_execute($stmt_pending)) {
                    echo "Error saat menyimpan detail produk: " . mysqli_stmt_error($stmt_pending);
                }

                mysqli_stmt_bind_param($stmt_product_update, "i", $product_id);
                mysqli_stmt_execute($stmt_product_update);
            }
            mysqli_stmt_close($stmt_pending);
            mysqli_stmt_close($stmt_product_update);

            $delete_cart_query = "DELETE FROM cart_details WHERE ip_address=?";
            $stmt_delete = mysqli_prepare($con, $delete_cart_query);
            mysqli_stmt_bind_param($stmt_delete, "s", $ip);
            mysqli_stmt_execute($stmt_delete);
            mysqli_stmt_close($stmt_delete);

            $_SESSION['alert'] = ['type' => 'success', 'message' => 'Order placed successfully! Please complete the payment within 24 hours.'];
            echo "<script>window.open('profile.php?my_orders','_self');</script>";
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
    <title>Checkout - Kevin's Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">
    <nav class="bg-gradient-to-r from-gray-900 to-gray-800 shadow-lg">
      <div class="max-w-7xl mx-auto px-4">
        <div class="flex justify-between h-16">
          <!-- Left side -->
          <div class="flex items-center">
            <a href="../index.php" class="flex items-center space-x-2">
              <i class="fas fa-store text-2xl text-blue-500"></i>
              <span class="text-white font-bold text-lg">Kevin's Collection</span>
            </a>
            <div class="hidden sm:flex sm:ml-6 space-x-1">
              <a href="../index.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Home</a>
              <a href="../display_all.php" class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Products</a>
              <div id="contacts-dropdown-wrapper" class="relative">
  <button class="px-3 py-2 text-sm font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700 transition-colors duration-200">Contacts</button>
  <div id="contacts-dropdown-menu" style="display:none;" class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
    <a href="mailto:hildanekevin16@gmail.com" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-t-md"><i class="far fa-envelope mr-2"></i>E-mail</a>
    <a href="https://wa.me/6281290206155" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 rounded-b-md"><i class="fab fa-whatsapp mr-2"></i>Whatsapp</a>
  </div>
</div>
            </div>
          </div>
          <!-- Right side -->
          <div class="flex items-center space-x-4">
            <a href="../cart.php" class="text-gray-300 hover:text-white px-3 py-2 relative group">
              <i class="fa-solid fa-cart-shopping text-xl group-hover:text-blue-500 transition-colors duration-200"></i>
              <span class="absolute -top-1 -right-1 bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"><?php cart_item() ?></span>
              <span class="text-gray-300 text-sm hidden md:inline-block ml-2">Rp.<?php total_cart_price(); ?></span>
            </a>
            <div class="hidden sm:flex items-center space-x-2">
              <?php if (isset($_SESSION['username'])): ?>
                <a href="profile.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="far fa-user mr-1"></i>My Account</a>
                <a href="logout.php" class="bg-red-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-red-600 transition-colors duration-200">Logout</a>
              <?php else: ?>
                <a href="user_login.php" class="text-gray-300 hover:text-white px-3 py-1 text-sm font-medium"><i class="fas fa-sign-in-alt mr-1"></i>Login</a>
                <a href="user_registration.php" class="bg-blue-500 text-white px-4 py-1 rounded-full text-sm font-medium hover:bg-blue-600 transition-colors duration-200">Register</a>
              <?php endif; ?>
            </div>
            <button @click="mobileMenu = !mobileMenu" class="sm:hidden text-gray-300 hover:text-white"><i class="fas fa-bars text-xl"></i></button>
          </div>
        </div>
        <div v-show="mobileMenu" class="sm:hidden px-2 pt-2 pb-3 space-y-1">
          <a href="index.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-home mr-2"></i>Home</a>
          <a href="../display_all.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-shopping-bag mr-2"></i>Products</a>
          <?php if (isset($_SESSION['username'])): ?>
            <a href="profile.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="far fa-user mr-2"></i>My Account</a>
            <a href="logout.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-sign-out-alt mr-2"></i>Logout</a>
          <?php else: ?>
            <a href="user_login.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-sign-in-alt mr-2"></i>Login</a>
            <a href="user_registration.php" class="block px-3 py-2 text-base font-medium rounded-md text-gray-300 hover:text-white hover:bg-gray-700"><i class="fas fa-user-plus mr-2"></i>Register</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <div class="max-w-7xl mx-auto px-4 py-8 w-full">
        <div class="mb-8 max-w-2xl mx-auto">
             <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-orange-500 text-white rounded-full flex items-center justify-center">1</div>
                    <div class="ml-2 text-orange-500 font-medium">Address & Shipping</div>
                </div>
                <div class="flex-1 mx-4 h-1 bg-gray-300"></div>
                <div class="flex items-center">
                    <div class="w-8 h-8 bg-gray-300 text-gray-500 rounded-full flex items-center justify-center">2</div>
                    <div class="ml-2 text-gray-500 font-medium">Payment</div>
                </div>
            </div>
        </div>

     

        <form action="" method="post" class="max-w-2xl mx-auto">
            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center">
                        <i class="fas fa-map-marker-alt text-orange-500 text-xl mr-3"></i>
                        <h2 class="text-lg font-semibold text-gray-800">Shipping Address</h2>
                    </div>
                </div>
                <div class="pl-8">
                    <p class="font-medium text-gray-800"><?php echo htmlspecialchars($username); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user_mobile); ?></p>
                    <p class="text-gray-600"><?php echo htmlspecialchars($user_address); ?></p>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-shopping-bag text-orange-500 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Products Ordered</h2>
                </div>
                <?php foreach($products_in_cart as $product): ?>
                <div class="flex items-center py-4 border-b last:border-b-0">
                    <img src="../atmin/product_images/<?php echo htmlspecialchars($product['product_image1']); ?>"
                         class="w-16 h-16 object-cover rounded-md" alt="<?php echo htmlspecialchars($product['product_title']); ?>">
                    <div class="ml-4 flex-grow">
                        <h3 class="font-medium text-gray-800"><?php echo htmlspecialchars($product['product_title']); ?></h3>
                    </div>
                    <div class="text-orange-500 font-medium">Rp <?php echo number_format($product['product_price'], 0, ',', '.'); ?></div>
                </div>
                <?php endforeach; ?>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                <div class="flex items-center mb-4">
                    <i class="fas fa-truck text-orange-500 text-xl mr-3"></i>
                    <h2 class="text-lg font-semibold text-gray-800">Shipping Method</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="provinsi" class="block text-sm font-medium text-gray-700 mb-1">Provinsi Tujuan</label>
                        <select id="provinsi" name="provinsi" class="w-full p-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 transition">
                            <option selected enabled>Pilih Provinsi...</option>
                        </select>
                    </div>
                    <div>
                        <label for="kota" class="block text-sm font-medium text-gray-700 mb-1">Kota/Kabupaten Tujuan</label>
                        <select id="kota" name="kota" class="w-full p-2 border border-gray-300 rounded-md focus:ring-orange-500 focus:border-orange-500 transition" disabled>
                            <option selected enabled>Pilih Kota/Kabupaten...</option>
                        </select>
                    </div>
                </div>

                <div id="courier-selection" class="mt-5" style="display: none;">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Ekspedisi</label>
                    <div class="grid grid-cols-3 gap-3">
                        <button type="button" data-courier="jne" class="courier-btn p-3 border border-gray-300 rounded-lg text-center font-semibold transition-all hover:border-orange-500 hover:bg-orange-50">JNE</button>
                        <button type="button" data-courier="pos" class="courier-btn p-3 border border-gray-300 rounded-lg text-center font-semibold transition-all hover:border-orange-500 hover:bg-orange-50">POS</button>
                        <button type="button" data-courier="tiki" class="courier-btn p-3 border border-gray-300 rounded-lg text-center font-semibold transition-all hover:border-orange-500 hover:bg-orange-50">TIKI</button>
                    </div>
                </div>

                <div id="courier-results" class="w-full mt-4">
                    <div id="loading" style="display: none;" class="text-center p-4 text-gray-500">
                        <i class="fas fa-spinner fa-spin mr-2"></i>Mencari layanan pengiriman...
                    </div>
                    <div id="showCourier" class="space-y-3">
                        </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-sm p-6">
                <h2 class="text-lg font-semibold text-gray-800 mb-4">Payment Summary</h2>
                <div class="space-y-2 text-gray-700">
                    <div class="flex justify-between">
                        <span>Subtotal Produk:</span>
                        <span>Rp <?php echo number_format($total_price, 0, ',', '.'); ?></span>
                    </div>
                    <div class="flex justify-between">
                        <span>Ongkos Kirim:</span>
                        <span class="font-medium">Rp <span id="shippingCostDisplay">0</span></span>
                    </div>
                    <div class="flex justify-between font-semibold text-orange-500 text-lg border-t pt-3 mt-2">
                        <span>Total Pembayaran:</span>
                        <span>Rp <span id="totalPaymentDisplay"><?php echo number_format($total_price, 0, ',', '.'); ?></span></span>
                    </div>
                </div>

                <input type="hidden" name="shipping_method" id="shipping_method_input">
                <input type="hidden" name="shipping_cost" id="shipping_cost_input">

                <button type="submit" name="confirm_order"
                        class="bg-orange-500 text-white font-bold px-8 py-3 rounded-lg hover:bg-orange-600 transition duration-200 w-full mt-6">
                    Place Order
                </button>
            </div>
        </form>
    </div>

<script>
$(document).ready(function() {
    // --- KONFIGURASI ---
    const originCityId = '154'; // Ganti dengan ID Kota Asal Toko Anda (Contoh: 152 = Jakarta Pusat)
    const totalWeight = <?php echo $total_weight; ?>;    // diganti  echo $total_weight; 
    const subtotal = <?php echo $total_price; ?>; // diganti  echo $total_pr;

    function formatRupiah(angka) {
        return new Intl.NumberFormat('id-ID').format(angka);
    }

    function resetShippingCost() {
        $('#shippingCostDisplay').text('0');
        $('#totalPaymentDisplay').text(formatRupiah(subtotal));
        $('#shipping_method_input').val('');
        $('#shipping_cost_input').val('');
    }
    var hideTimer; 

    $('#contacts-dropdown-wrapper').on('mouseenter', function() {
        clearTimeout(hideTimer);
        $('#contacts-dropdown-menu').show(); 
    });

    $('#contacts-dropdown-wrapper').on('mouseleave', function() {
        hideTimer = setTimeout(function() {
            $('#contacts-dropdown-menu').hide();
        }, 300);
    });


    $.ajax({
        url: '../api/rajaongkir_proxy.php?endpoint=province',
        method: 'GET',
        dataType: 'json',
        success: function(data) {
            console.log(data.data);

            if (data.data) {
                let provinces = data.data;
                $('#provinsi').empty().append('<option selected enabled>Pilih Provinsi...</option>');
                $.each(provinces, function(i, province) {
                    $('#provinsi').append(`<option value="${province.id}">${province.name}</option>`);
                });
            }
        },
        error: function() {
            alert('Gagal memuat data provinsi. Silakan refresh halaman.');
        }
    });

    $('#provinsi').on('change', function() {
        let provinceId = $(this).val();
        $('#kota').prop('enabled', true).html('<option>Memuat kota...</option>');
        $('#courier-selection').hide();
        $('#showCourier').html('');
        resetShippingCost();

        $.ajax({
            url: `../api/rajaongkir_proxy.php?endpoint=city&province_id=${provinceId}`,
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                console.log(data);
                if (data.data) {
                    let cities = data.data;
                    $('#kota').prop('disabled', false).empty().append('<option selected disabled>Pilih Kota/Kabupaten...</option>');
                    $.each(cities, function(i, city) {
                        $('#kota').append(`<option value="${city.id}">${city.name}</option>`);
                    });
                }
            },
            error: function() {
                alert('Gagal memuat data kota. Coba lagi nanti.');
            }
        });
    });

    $('#kota').on('change', function() {
        if ($(this).val()) {
            $('#courier-selection').slideDown('fast');
            $('.courier-btn').removeClass('border-orange-500 bg-orange-100 ring-2 ring-orange-300');
            $('#showCourier').html('');
            resetShippingCost();
        }
    });

    $('#courier-selection').on('click', '.courier-btn', function() {
        let selectedCourier = $(this).data('courier');
        let destinationCityId = $('#kota').val();

        $('.courier-btn').removeClass('border-orange-500 bg-orange-100 ring-2 ring-orange-300');
        $(this).addClass('border-orange-500 bg-orange-100 ring-2 ring-orange-300');

        if (!destinationCityId) return;

        $('#showCourier').html('');
        $('#loading').show();
        resetShippingCost();

        $.ajax({
            url: '../api/rajaongkir_proxy.php?endpoint=cost',
            method: 'POST',
            dataType: 'json',
            data: {
                origin: originCityId,
                destination: destinationCityId,
                weight: totalWeight,
                courier: selectedCourier
            },
            success: function(data) {
                console.log('data dari corier selection ',data.data);
                $('#loading').hide();
                if (data.data.length > 0) {
                    displayCourierOptions(data.data);
                } else {
                    $('#showCourier').html('<div class="text-center p-4 bg-yellow-100 text-yellow-800 rounded-md">Kurir ini tidak menyediakan layanan untuk tujuan tersebut. Silakan pilih kurir lain.</div>');
                }
            },
            error: function() {
                $('#loading').hide();
                $('#showCourier').html('<div class="text-center p-4 bg-red-100 text-red-700 rounded-md">Terjadi kesalahan saat mengambil data ongkos kirim.</div>');
            }
        });
    });

    function displayCourierOptions(results) {
        let html = '';
        console.log('data dari displayCourierOptions', results);
        results.forEach(function(courier) {
            console.log('data setelah for each service', courier);
            if (courier) {
                
                    let cost = courier.cost[0];
                    let courierName = courier.code.toUpperCase();
                    let serviceName = courier.service;
                    let fullDescription = courier.description;
                    let price = courier.cost;
                    let etd = courier.etd.replace(/ HARI/i, '').replace(/-/g, ' - ');

                    html += `
                        <div class="courier-option border border-gray-300 rounded-lg p-4 flex justify-between items-center cursor-pointer hover:border-orange-500 hover:bg-orange-50 transition-all"
                             data-courier-name="${courierName}"
                             data-service-name="${serviceName}"
                             data-cost="${price}">
                            <div>
                                <p class="font-bold text-gray-800">${courierName} - ${serviceName}</p>
                                <p class="text-sm text-gray-600">${fullDescription}</p>
                            </div>
                            <div class="text-right">
                                <p class="font-semibold text-lg text-orange-600">Rp ${formatRupiah(price)}</p>
                                <p class="text-xs text-gray-500">Estimasi: ${etd} hari</p>
                            </div>
                        </div>
                    `;
                
            } else {
                html += `
                    <div class="text-center p-4 bg-yellow-100 text-yellow-800 rounded-md">
                        cost length nya kurang dari 1.
                    </div>
                `;
            }
        });
        $('#showCourier').html(html);
    }

    $('#courier-results').on('click', '.courier-option', function() {
        $('.courier-option').removeClass('border-orange-500 bg-orange-100 ring-2 ring-orange-300').addClass('border-gray-300');
        $(this).addClass('border-orange-500 bg-orange-100 ring-2 ring-orange-300').removeClass('border-gray-300');

        let cost = $(this).data('cost');
        let courierName = $(this).data('courier-name');
        let serviceName = $(this).data('service-name');
        let totalPayment = subtotal + parseInt(cost);

        $('#shippingCostDisplay').text(formatRupiah(cost));
        $('#totalPaymentDisplay').text(formatRupiah(totalPayment));

        $('#shipping_method_input').val(`${courierName} - ${serviceName}`);
        $('#shipping_cost_input').val(cost);
    });
});
</script>

</body>
</html>