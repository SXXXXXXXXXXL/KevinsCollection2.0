<?php
// --- BAGIAN LOGIKA PHP ---

// FUNGSI PEMBANTU (HELPERS)
function format_rupiah($angka){
    return "Rp " . number_format($angka, 0, ',', '.');
}

function format_tanggal($tanggal){
    if (empty($tanggal)) return '-';
    return date("d M Y, H:i", strtotime($tanggal));
}


// AMBIL SEMUA DATA ORDER DARI DATABASE
$query_get = "SELECT * FROM user_orders ORDER BY order_date DESC";
$result = mysqli_query($con, $query_get);
$orders = [];
while($row = mysqli_fetch_assoc($result)){
    $orders[] = $row;
}

?>

<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 bg-gray-900 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-white">All Orders</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Ekspedisi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">No. Resi</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php if (empty($orders)): ?>
                    <tr>
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">No orders found</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($orders as $order): ?>
                        <?php
                            // Logika warna untuk status badge
                            $status_class = 'bg-yellow-100 text-yellow-800'; // Default untuk 'Pending'
                            if ($order['order_status'] == 'Complete') {
                                $status_class = 'bg-green-100 text-green-800';
                            } elseif ($order['order_status'] == 'dikirim') {
                                $status_class = 'bg-blue-100 text-blue-800';
                            }
                        ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($order['order_id']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars(format_rupiah($order['amount_due'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($order['invoice_number']) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($order['ekspedisi'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($order['resi'] ?? '-') ?></td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars(format_tanggal($order['order_date'])) ?></td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?= $status_class ?>">
                                    <?= htmlspecialchars($order['order_status']) ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">                
                                <a href="indexatmin.php?delete_order=<?= htmlspecialchars($order['order_id']) ?>" 
                                   class="text-red-600 hover:text-red-900 bg-red-100 p-2 rounded-full inline-flex items-center"
                                   onclick="return confirm('Are you sure you want to delete this order?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

