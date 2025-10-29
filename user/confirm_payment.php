<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../include/connect.php');
session_start();

if (!isset($_SESSION['username'])) {
    header('Location: user_login.php');
    exit();
}

if (!isset($_GET['order_id'])) {
    die('Kesalahan: Order ID tidak ditemukan.');
}

require_once '../vendor/autoload.php';

\Midtrans\Config::$serverKey = 'SB-Mid-server-85I5ReG-tchz8CUfP-jjxYeC';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

$order_id = intval($_GET['order_id']);
$username = $_SESSION['username'];

$query_order = "SELECT * FROM user_orders WHERE order_id = $order_id";
$result_order = mysqli_query($con, $query_order);

if (!$result_order || mysqli_num_rows($result_order) == 0) {
    die('Kesalahan: Pesanan tidak ditemukan.');
}
$order = mysqli_fetch_assoc($result_order);


if ($order['order_status'] == 'gagal') {
    die('Status pesanan ini adalah "' . htmlspecialchars($order['order_status']) . '". Tidak dapat melakukan pembayaran.');
}
// ---------------------------------------------


$invoice_number = $order['invoice_number'];

$amount_due_from_db = (int)$order['amount_due'];

$items = [];
$calculated_gross_amount = $amount_due_from_db;

$query_items = "
    SELECT p.product_title, p.product_price, op.product_id, op.quantity 
    FROM orders_pending op 
    JOIN products p ON op.product_id = p.product_id 
    WHERE op.order_id = $order_id
";
$result_items = mysqli_query($con, $query_items);

// while ($item = mysqli_fetch_assoc($result_items)) {
//     $items[] = [
//         'id'       => (string)$item['product_id'],
//         'price'    => (int)$item['product_price'],
//         'quantity' => (int)$item['quantity'],
//         'name'     => $item['product_title'],
//     ];
//     $calculated_gross_amount += (int)$item['product_price'] * (int)$item['quantity'];
// }

// $shipping_method = $order['ekspedisi'];
// $shipping_costs = [
//     'jne' => 10000,
//     'jnt' => 12000,
//     'sicepat' => 15000,
// ];
// $shipping_cost = $shipping_costs[$shipping_method] ?? 0;

// if ($shipping_cost > 0) {
//     $items[] = [
//         'id'       => 'SHIPPING_COST',
//         'price'    => $shipping_cost,
//         'quantity' => 1,
//         'name'     => 'Ongkos Kirim (' . strtoupper($shipping_method) . ')',
//     ];
//     $calculated_gross_amount += $shipping_cost;
// }

// Validasi terakhir sebelum mengirim, jika total tidak cocok, ada yang salah.
if ($amount_due_from_db !== $calculated_gross_amount) {
    die("Ada kesalahan perhitungan. Total di DB: $amount_due_from_db, Total Kalkulasi: $calculated_gross_amount. Harap hubungi admin.");
}


$transaction_details = [
    'order_id'     => $invoice_number,
    'gross_amount' => $calculated_gross_amount,
];

$customer_details = [
    'first_name' => $username,
    'email'      => 'customer@example.com',
    'phone'      => '081234567890',
];

$params = [
    'transaction_details' => $transaction_details,
    'customer_details'    => $customer_details,
    // 'item_details'        => $items,
];

try {
    $snapToken = \Midtrans\Snap::getSnapToken($params);
} catch (Exception $e) {
    header('Content-Type: text/plain');
    echo "GAGAL MENDAPATKAN SNAP TOKEN DARI MIDTRANS!\n\n";
    echo "Pesan Error: " . $e->getMessage() . "\n\n";
    echo "===============================================\n";
    echo "DATA YANG DIKIRIM KE MIDTRANS (PARAMS):\n";
    echo "===============================================\n\n";
    print_r($params);
    die();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Pembayaran - Kevin's Collection</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-ELsOiz11qDYcaYBg"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" />
</head>
<body class="bg-gray-100 font-sans">
    <div class="min-h-screen flex flex-col items-center justify-center p-4">
        <div class="bg-white p-6 md:p-8 rounded-xl shadow-lg max-w-md w-full text-center">
            <h1 class="text-2xl font-bold text-gray-800 mb-2">Selesaikan Pembayaran Anda</h1>
            <p class="text-gray-600 mb-4">Silakan selesaikan pembayaran untuk pesanan Anda.</p>
            
            <div class="border-t border-b border-gray-200 py-4 my-4">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-gray-500">Nomor Invoice:</span>
                    <strong class="text-gray-800 font-mono"><?php echo htmlspecialchars($invoice_number); ?></strong>
                </div>
                <div class="flex justify-between items-center text-lg">
                    <span class="text-gray-500">Total Tagihan:</span>
                    <strong class="text-blue-600">Rp <?php echo number_format($calculated_gross_amount, 0, ',', '.'); ?></strong>
                </div>
            </div>

            <button id="pay-button" class="w-full bg-blue-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 ease-in-out">
                <i class="fas fa-shield-alt mr-2"></i> Bayar Sekarang
            </button>
        </div>
    </div>

    <script type="text/javascript">
        var payButton = document.getElementById('pay-button');
        
        payButton.addEventListener('click', function () {
            snap.pay('<?php echo $snapToken; ?>', {
                onSuccess: function(result){
                    console.log('Payment Success:', result);
                    // Redirect ke halaman yang akan mengubah status order menjadi 'paid'
                    window.location.href = 'payment_success.php?order_id=<?php echo $order_id; ?>';
                },
                onPending: function(result){
                    console.log('Payment Pending:', result);
                     // Redirect ke halaman yang menampilkan status pending
                    window.location.href = 'payment_pending.php?order_id=<?php echo $order_id; ?>';
                },
                onError: function(result){
                    console.error('Payment Error:', result);
                    alert('Pembayaran Gagal! Silakan coba lagi.');
                },
                onClose: function(){
                    console.log('Customer closed the popup without finishing the payment');
                }
            });
        });
    </script>
</body>
</html>
