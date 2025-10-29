<div class="bg-white rounded-lg shadow-lg overflow-hidden">
    <div class="p-6 bg-gray-900 border-b border-gray-200">
        <h2 class="text-xl font-semibold text-white">All Brands</h2>
    </div>
    
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand ID</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Brand Title</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <?php
                $select_brand = "SELECT * FROM brands";
                $result = mysqli_query($con, $select_brand);
                while($row = mysqli_fetch_assoc($result)){
                    $brand_id = $row['brand_id'];
                    $brand_title = $row['brand_title'];
                    ?>
                    
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            <?php echo $brand_id ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <?php echo $brand_title ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-3">
                            <a href="indexatmin.php?edit_brand=<?php echo $brand_id ?>" 
                               class="text-blue-600 hover:text-blue-900 bg-blue-100 p-2 rounded-full inline-flex items-center">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="indexatmin.php?delete_brand=<?php echo $brand_id ?>" 
                               class="text-red-600 hover:text-red-900 bg-red-100 p-2 rounded-full inline-flex items-center"
                               onclick="return confirm('Are you sure you want to delete this brand?')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php } ?>
                
                <?php if (mysqli_num_rows($result) == 0): ?>
                <tr>
                    <td colspan="3" class="px-6 py-4 text-center text-gray-500">
                        No brands found
                    </td>
                </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
/* Custom scrollbar for Webkit browsers */
.overflow-x-auto::-webkit-scrollbar {
    height: 8px;
}

.overflow-x-auto::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

.overflow-x-auto::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
