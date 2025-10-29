<?php
// Diasumsikan koneksi ($con) dan session sudah aktif dari file profil utama

// --- FUNGSI PEMBANTU (HELPERS) ---
function e($string) { return htmlspecialchars($string, ENT_QUOTES, 'UTF-8'); }
function format_rupiah($angka) { return "Rp " . number_format($angka, 0, ',', '.'); }
function format_tanggal($tanggal) { return empty($tanggal) ? '-' : date("d M Y, H:i", strtotime($tanggal)); }

// 1. Ambil User ID dengan Aman
$user_id = null;
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $stmt_user = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE username = ?");
    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_user = mysqli_stmt_get_result($stmt_user);
    if ($row_user = mysqli_fetch_assoc($result_user)) {
        $user_id = $row_user['user_id'];
    }
    mysqli_stmt_close($stmt_user);
}
if (is_null($user_id)) { die("Sesi tidak valid."); }


// ### QUERY FINAL YANG DIPERBAIKI ###
// Menggabungkan logika terbaik dari kedua query Anda
$query_orders = "
    SELECT 
        uo.*, 
        GROUP_CONCAT(COALESCE(p.product_title, op.product_title) SEPARATOR '||') as product_titles,
        GROUP_CONCAT(COALESCE(p.product_image1, op.product_image) SEPARATOR '||') as product_images
    FROM 
        user_orders uo
    LEFT JOIN 
        orders_pending op ON uo.order_id = op.order_id
    LEFT JOIN 
        products p ON op.product_id = p.product_id
    WHERE 
        uo.user_id = ?
    GROUP BY 
        uo.order_id
    ORDER BY 
        uo.order_date DESC
";

$stmt_orders = mysqli_prepare($con, $query_orders);
mysqli_stmt_bind_param($stmt_orders, "i", $user_id);
mysqli_stmt_execute($stmt_orders);
$result_orders = mysqli_stmt_get_result($stmt_orders);
$orders = [];
while ($row = mysqli_fetch_assoc($result_orders)) {
    $orders[] = $row;
}
mysqli_stmt_close($stmt_orders);

?>

<div class="space-y-6">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">My Orders</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Details</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount Due</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <?php if (empty($orders)): ?>
                        <tr><td colspan="4" class="px-6 py-4 text-center text-gray-500">No orders found</td></tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <?php
                                // Logika untuk warna status
                                $status_class = match($order['order_status']) {
                                    'Pending' => 'text-yellow-800 bg-yellow-100',
                                    'Paid' => 'text-orange-800 bg-orange-100',
                                    'Shipped' => 'text-blue-800 bg-blue-100',
                                    'Success - Arrived' => 'text-green-800 bg-green-100',
                                    'Failed' => 'text-red-800 bg-red-100',
                                    'Canceled' => 'text-red-800 bg-red-100',
                                    default => ''
                                };

                                // Memecah string produk dan gambar menjadi array
                                $product_titles = explode('||', $order['product_titles']);
                                $product_images = explode('||', $order['product_images']);
                            ?>
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">Invoice: <?= e($order['invoice_number']) ?></div>
                                    <div class="text-sm text-gray-500"><?= e(format_tanggal($order['order_date'])) ?></div>
                                    <div class="mt-2 border-t pt-2">
                                        <?php foreach ($product_titles as $index => $title): ?>
                                            <div class="flex items-center space-x-3 mt-2">
                                                <img src="../atmin/product_images/<?= e($product_images[$index]) ?>" alt="<?= e($title) ?>" class="w-10 h-10 object-cover rounded-md flex-shrink-0">
                                                <span class="text-sm text-gray-700"><?= e($title) ?></span>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </td>

                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 align-top"><?= e(format_rupiah($order['amount_due'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap align-top">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class ?>">
                                        <?= e($order['order_status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm space-y-2 align-top">
                                    <?php if ($order['order_status'] == 'Pending'): ?>
                                        <a href="confirm_payment.php?order_id=<?= e($order['order_id']) ?>" class="inline-flex items-center w-full justify-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <i class="fas fa-credit-card mr-2"></i> Confirm
                                        </a>
                                    <?php elseif ($order['order_status'] == 'Shipped'): ?>
                                        <?php if (!empty($order['resi'])): 
                                            // ### PERBAIKAN DI SINI: Mengambil hanya kode kurir ###
                                            $courier_code = strtolower(explode(' - ', e($order['ekspedisi']))[0]);
                                        ?>
                                        <button type="button" onclick="showTrackingModal('<?= $courier_code ?>', '<?= e($order['resi']) ?>')" class="inline-flex items-center w-full justify-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                            <i class="fas fa-truck mr-2"></i> Track
                                        </button>
                                        <?php endif; ?>
                                        <a href="confirm_arrival.php?order_id=<?= e($order['order_id']) ?>" onclick="return confirm('Are you sure this order has arrived?')" class="inline-flex items-center w-full justify-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                            <i class="fas fa-check-square mr-2"></i> Arrived
                                        </a>
                                    <?php elseif ($order['order_status'] == 'Failed'): ?>
                                         <a href="confirm_payment.php?order_id=<?= e($order['order_id']) ?>" class="inline-flex items-center w-full justify-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <i class="fas fa-credit-card mr-2"></i> Retry Payment
                                        </a>
                                        <?php elseif ($order['order_status'] == 'Canceled'): ?>
                                         <a href="../index.php" class="inline-flex items-center w-full justify-center px-3 py-1 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700">
                                            <i class="fas fa-credit-card mr-2"></i> Go to Homepage
                                        </a>
                                    <?php else: ?>
                                        <span>-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ### KODE MODAL LENGKAP DI SINI ### -->
<div id="trackingModal" class="fixed z-50 inset-0 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true" onclick="closeTrackingModal()"></div>
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-4 pt-5 pb-4 sm:p-6">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                        <i class="fas fa-truck text-blue-600"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">Tracking Details</h3>
                        
                        <div id="trackingDetailsContainer" class="mt-2 grid grid-cols-1 md:grid-cols-2 gap-x-4 gap-y-2 text-sm">
                            <!-- Detail ringkasan akan dimuat di sini -->
                        </div>

                        <div class="mt-4 border-t pt-4">
                            <h4 class="text-md font-semibold text-gray-800 mb-2">Shipment History</h4>
                            <div id="trackingHistoryContainer" class="max-h-60 overflow-y-auto pr-2">
                                <!-- Riwayat perjalanan akan dimuat di sini -->
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button type="button" onclick="closeTrackingModal()" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ### KODE JAVASCRIPT LENGKAP DI SINI ### -->
<script>
    const trackingModal = document.getElementById('trackingModal');
    const detailsContainer = document.getElementById('trackingDetailsContainer');
    const historyContainer = document.getElementById('trackingHistoryContainer');

    async function showTrackingModal(courier, awb) {
        // 1. Tampilkan modal dan status loading
        trackingModal.classList.remove('hidden');
        detailsContainer.innerHTML = ''; // Kosongkan detail sebelumnya
        historyContainer.innerHTML = `<p class="text-center text-gray-500 py-4"><i class="fas fa-spinner fa-spin mr-2"></i>Fetching tracking data...</p>`;

        try {
            // 2. Panggil handler API di server Anda
            const response = await fetch(`../api/track_handler.php?courier=${courier}&awb=${awb}`);
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            const result = await response.json();

            // 3. Cek status dari API BinderByte
            if (result.status !== 200) {
                historyContainer.innerHTML = `<p class="text-center text-red-500 py-4">${result.message}</p>`;
                return;
            }

            // 4. Render data ke modal jika sukses
            const summary = result.data.summary;
            const detail = result.data.detail;
            const history = result.data.history;

            detailsContainer.innerHTML = `
                <div>
                    <p class="font-semibold text-gray-800">Summary</p>
                    <p><strong>Courier:</strong> ${summary.courier} (${summary.service})</p>
                    <p><strong>AWB:</strong> <span class="font-mono bg-gray-100 px-1 rounded">${summary.awb}</span></p>
                    <p><strong>Status:</strong> <span class="font-bold text-green-700">${summary.status}</span></p>
                </div>
                <div>
                    <p class="font-semibold text-gray-800">Shipment</p>
                    <p><strong>Shipper:</strong> ${detail.shipper}</p>
                    <p><strong>Receiver:</strong> ${detail.receiver}</p>
                </div>
            `;

            if (history && history.length > 0) {
                historyContainer.innerHTML = history.map(item => `
                    <div class="relative pl-8 pb-4 border-l border-gray-200 last:border-l-transparent last:pb-0">
                        <div class="absolute -left-2 top-0 h-4 w-4 rounded-full bg-blue-100 border-4 border-white"></div>
                        <p class="text-sm font-semibold text-gray-800">${item.desc}</p>
                        <p class="text-xs text-gray-500">${item.date}</p>
                    </div>
                `).join('');
            } else {
                historyContainer.innerHTML = `<p class="text-center text-gray-500 py-4">Shipment history not available.</p>`;
            }

        } catch (error) {
            console.error('Fetch error:', error);
            historyContainer.innerHTML = `<p class="text-center text-red-500 py-4">An error occurred while loading data.</p>`;
        }
    }

    function closeTrackingModal() {
        trackingModal.classList.add('hidden');
    }
</script>