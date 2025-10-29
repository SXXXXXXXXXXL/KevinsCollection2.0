<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 bg-gray-900">
        <h2 class="text-xl font-semibold text-white">All Products</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Image</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase">Recommended</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                // Diasumsikan $con sudah di-include dari file utama
                $get_products = "SELECT * FROM products";
                $result = mysqli_query($con, $get_products);
                if (mysqli_num_rows($result) == 0): ?>
                    <tr><td colspan="7" class="px-6 py-4 text-center text-gray-500">No products found</td></tr>
                <?php else:
                while($row = mysqli_fetch_assoc($result)):
                    $product_id = $row['product_id'];
                    $status = $row['status'];
                    $is_recommended = $row['is_recommended'];
                    
                    $status_color = $status == 'true' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    
                    // --- PERBAIKAN 1: Logika untuk menentukan kelas tombol dan ikon ---
                    $rec_class = $is_recommended ? 'bg-yellow-200 text-yellow-800 hover:bg-yellow-300' : 'bg-gray-200 text-gray-800 hover:bg-gray-300';
                    // Menggunakan kelas Font Awesome v6 yang benar: fa-solid / fa-regular
                    $rec_icon_class = $is_recommended ? 'fa-solid fa-star' : 'fa-regular fa-star';
                    ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo $product_id; ?></td>
                        <td class="px-6 py-4 text-sm text-gray-900"><?php echo htmlspecialchars($row['product_title']); ?></td>
                        <td class="px-6 py-4"><img src="./product_images/<?php echo htmlspecialchars($row['product_image1']); ?>" class="w-16 h-16 rounded-md object-cover"></td>
                        <td class="px-6 py-4 text-sm text-gray-900">Rp.<?php echo number_format($row['product_price']); ?></td>
                        <td class="px-6 py-4 text-center"><span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_color; ?>"><?php echo $status; ?></span></td>
                        
                        <td class="px-6 py-4 text-center">
                             <button type="button" class="toggle-recommend-btn p-2 rounded-full <?php echo $rec_class; ?>" data-id="<?php echo $product_id; ?>" title="<?php echo $is_recommended ? 'Unrecommend' : 'Recommend'; ?>">
                                <i class="<?php echo $rec_icon_class; ?>" aria-hidden="true"></i>
                            </button>
                        </td>

                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="indexatmin.php?edit_products=<?php echo $product_id; ?>" class="text-blue-600 hover:text-blue-900" title="Edit"><i class="fas fa-edit"></i></a>
                            <a href="indexatmin.php?delete_products=<?php echo $product_id; ?>" class="text-red-600 hover:text-red-900" title="Delete" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                        </td>
                    </tr>
                <?php endwhile; endif; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script>
$(document).ready(function(){
    $('tbody').on('click', '.toggle-recommend-btn', function(){
        const button = $(this);
        const productId = button.data('id');

        $.ajax({
            url: 'ajax_toggle_recommend.php', // Pastikan path ini benar
            type: 'POST',
            data: { product_id: productId },
            dataType: 'json',
            success: function(response){
                if(response.success){
                    const icon = button.find('i');
                    // --- PERBAIKAN 3: Menggunakan kelas Font Awesome v6 yang benar di JavaScript ---
                    if(response.new_status == 1){
                        // Update style menjadi "Recommended"
                        button.removeClass('bg-gray-200 text-gray-800 hover:bg-gray-300').addClass('bg-yellow-200 text-yellow-800 hover:bg-yellow-300').attr('title', 'Unrecommend');
                        icon.removeClass('fa-regular fa-star').addClass('fa-solid fa-star');
                    } else {
                        // Update style menjadi "Not Recommended"
                        button.removeClass('bg-yellow-200 text-yellow-800 hover:bg-yellow-300').addClass('bg-gray-200 text-gray-800 hover:bg-gray-300').attr('title', 'Recommend');
                        icon.removeClass('fa-solid fa-star').addClass('fa-regular fa-star');
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            // --- PERBAIKAN 4: Menambahkan 'error' handler untuk debugging ---
            error: function(jqXHR, textStatus, errorThrown) {
                console.error("AJAX Error:", textStatus, errorThrown);
                console.error("Response:", jqXHR.responseText);
                alert('An error occurred while communicating with the server. Check the console (F12) for details.');
            }
        });
    });
});
</script>