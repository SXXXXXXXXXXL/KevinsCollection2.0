<?php
// Pastikan path ke file koneksi sudah benar
include('../include/connect.php'); 
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Payments</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body class="bg-gray-100">

<div class="container mx-auto p-4 md:p-8">
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <div class="p-6 bg-gray-900 border-b border-gray-700">
            <h2 class="text-xl font-semibold text-white">All Payments</h2>
        </div>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Username</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php
                    // Query mengambil dari user_orders sebagai tabel utama
                    $get_orders = "SELECT uo.*, ut.username, up.payment_mode, up.transaction_details 
                                   FROM user_orders AS uo
                                   JOIN user_table AS ut ON uo.user_id = ut.user_id
                                   LEFT JOIN user_payments AS up ON uo.order_id = up.order_id
                                   WHERE uo.order_status IN ('Paid', 'Shipped','Arrived')
                                   ORDER BY FIELD(uo.order_status, 'Paid', 'Pending', 'Shipped'), uo.order_date DESC";
                    
                    $result = mysqli_query($con, $get_orders);

                    if(mysqli_num_rows($result) > 0) {
                        while($row = mysqli_fetch_assoc($result)){
                            $order_id = $row['order_id'];
                            $invoice_number = $row['invoice_number'];
                            $username = $row['username'];
                            $amount_due = $row['amount_due'];
                            $order_date = $row['order_date'];
                            $order_status = $row['order_status'];
                            $transaction_details = $row['transaction_details'];

                            // Styling untuk badge order status
                            $status_color = match($order_status) {
                                'Pending', 'Paid' => 'bg-yellow-100 text-yellow-800',
                                'Shipped' => 'bg-blue-100 text-blue-800',
                                'Success - Arrived' => 'bg-green-100 text-green-800',
                                'Failed' => 'bg-red-100 text-red-800',
                                default => 'bg-gray-100 text-gray-800'
                            };
                            ?>
                            
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?php echo htmlspecialchars($invoice_number); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo htmlspecialchars($username); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">Rp.<?php echo number_format($amount_due); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?php echo date("d M Y, H:i", strtotime($order_date)); ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-center">
                                     <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_color; ?>">
                                        <?php echo htmlspecialchars($order_status); ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-center">
                                    <?php if ($order_status == 'Paid'): ?>
                                        <a href="conf_payment.php?order_id=<?php echo $order_id; ?>" 
                                           class="text-blue-600 hover:text-blue-900 bg-blue-100 px-3 py-2 rounded-lg inline-flex items-center"
                                           title="Confirm Payment & Ship Order"
                                           onclick="return confirm('Confirm payment and set order status to Shipped?')">
                                            <i class="fas fa-check-circle mr-2"></i><span>Ship</span></a>
                                    <?php elseif ($order_status == 'Pending'): ?>
                                        <span class="text-gray-400 p-2" title="Waiting for payment from user"><i class="fas fa-hourglass-half fa-lg"></i></span>
                                    <?php elseif ($order_status == 'Shipped'): ?>
                                         <button type="button" 
                                   class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-full inline-flex items-center"
                                   onclick="openResiModal('<?= $row['order_id'] ?>', '<?= htmlspecialchars($row['ekspedisi'] ?? '') ?>', '<?= htmlspecialchars($row['resi'] ?? '') ?>')">
                                    <i class="fas fa-truck"></i>
                                </button>
                                    <?php else: ?>
                                        <span class="text-gray-400 p-2" title="Order has been processed"><i class="fas fa-check-double fa-lg"></i></span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php 
                        } // Akhir while loop
                    } else { // Jika tidak ada pesanan yang perlu diurus
                        ?>
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">No active orders requiring action found.</td>
                        </tr>
                    <?php 
                    } // Akhir if
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_resi'])) {
    $order_id = $_POST['order_id_resi'];
    $resi = $_POST['nomor_resi'];
    $ekspedisi = $_POST['nama_ekspedisi'];
    $order_status = 'Shipped'; // Otomatis ubah status menjadi "Dikirim"

    // Gunakan PREPARED STATEMENT untuk keamanan (mencegah SQL Injection)
    $query_update = "UPDATE user_orders SET resi = ?, ekspedisi = ?, order_status = ? WHERE order_id = ?";
    $stmt = mysqli_prepare($con, $query_update);

    // Bind parameter ke statement
    // s = string, i = integer
    mysqli_stmt_bind_param($stmt, "sssi", $resi, $ekspedisi, $order_status, $order_id);

    // Eksekusi statement
    if (mysqli_stmt_execute($stmt)) {
        echo "<script>alert('Nomor resi berhasil ditambahkan!')</script>";
        echo "<script>window.open(window.location.href, '_self')</script>"; // Refresh halaman
    } else {
        echo "<script>alert('Gagal menambahkan nomor resi.')</script>";
    }

    // Tutup statement
    mysqli_stmt_close($stmt);
}
?>

<div id="resiModal" class="fixed z-10 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
            <form action="" method="POST">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Input Nomor Resi
                    </h3>
                    <div class="mt-4 space-y-4">
                        <input type="hidden" name="order_id_resi" id="order_id_resi">
                        <div>
                            <label for="nama_ekspedisi" class="block text-sm font-medium text-gray-700">Nama Ekspedisi</label>
                            <input type="text" name="nama_ekspedisi" id="nama_ekspedisi" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                        <div>
                            <label for="nomor_resi" class="block text-sm font-medium text-gray-700">Nomor Resi</label>
                            <input type="text" name="nomor_resi" id="nomor_resi" required class="mt-1 focus:ring-indigo-500 focus:border-indigo-500 block w-full shadow-sm sm:text-sm border-gray-300 rounded-md">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="submit" name="submit_resi" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                        Simpan
                    </button>
                    <button type="button" onclick="closeResiModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Batal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const modal = document.getElementById('resiModal');
    const orderIdInput = document.getElementById('order_id_resi');
    const ekspedisiInput = document.getElementById('nama_ekspedisi');
    const resiInput = document.getElementById('nomor_resi');

    function openResiModal(orderId, ekspedisi, resi) {
        orderIdInput.value = orderId;
        ekspedisiInput.value = ekspedisi;
        resiInput.value = resi;
        modal.classList.remove('hidden');
    }

    function closeResiModal() {
        modal.classList.add('hidden');
    }

    // Optional: Tutup modal jika user klik di luar area modal
    window.onclick = function(event) {
        if (event.target == modal) {
            closeResiModal();
        }
    }
</script>

</body>
</html>