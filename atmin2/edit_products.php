<?php


$edit_id = null;
$feedback_message = '';

if(isset($_GET['edit_products'])){
    $edit_id = intval($_GET['edit_products']);
    
    $get_data = "SELECT * FROM products WHERE product_id = ?";
    $stmt_get = mysqli_prepare($con, $get_data);
    mysqli_stmt_bind_param($stmt_get, "i", $edit_id);
    mysqli_stmt_execute($stmt_get);
    $result = mysqli_stmt_get_result($stmt_get);
    
    if(mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $product_title = $row['product_title'];
        $product_desc = $row['product_desc'];
        $product_keywords = $row['product_keywords'];
        $category_id = $row['category_id'];
        $brand_id = $row['brand_id'];
        $product_image1 = $row['product_image1'];
        $product_image2 = $row['product_image2'];
        $product_image3 = $row['product_image3'];
        $product_price = $row['product_price'];
    } else {
        echo "<div class='bg-red-100 text-red-700 p-4'>Product not found.</div>";
        exit();
    }
    mysqli_stmt_close($stmt_get);
}

if(isset($_POST['edit_product'])){
    $p_title = $_POST['product_title'];
    $p_desc = $_POST['product_desc'];
    $p_keywords = $_POST['product_keyword'];
    $p_category = $_POST['product_cat'];
    $p_brand = $_POST['product_brand'];
    $p_price = $_POST['product_price'];

    $new_image1 = $_POST['current_image1'];
    $new_image2 = $_POST['current_image2'];
    $new_image3 = $_POST['current_image3'];

    if(isset($_FILES['product_image1']) && $_FILES['product_image1']['error'] === UPLOAD_ERR_OK){
        $img1_name = $_FILES['product_image1']['name'];
        $img1_tmp = $_FILES['product_image1']['tmp_name'];
        move_uploaded_file($img1_tmp, "./product_images/$img1_name");
        $new_image1 = $img1_name;
    }
    if(isset($_FILES['product_image2']) && $_FILES['product_image2']['error'] === UPLOAD_ERR_OK){
        $img2_name = $_FILES['product_image2']['name'];
        $img2_tmp = $_FILES['product_image2']['tmp_name'];
        move_uploaded_file($img2_tmp, "./product_images/$img2_name");
        $new_image2 = $img2_name;
    }
    if(isset($_FILES['product_image3']) && $_FILES['product_image3']['error'] === UPLOAD_ERR_OK){
        $img3_name = $_FILES['product_image3']['name'];
        $img3_tmp = $_FILES['product_image3']['tmp_name'];
        move_uploaded_file($img3_tmp, "./product_images/$img3_name");
        $new_image3 = $img3_name;
    }

    $update_product = "UPDATE products SET 
                        product_title=?, product_desc=?, product_keywords=?, 
                        category_id=?, brand_id=?, product_image1=?, 
                        product_image2=?, product_image3=?, product_price=?, date=NOW() 
                        WHERE product_id=?";
    
    $stmt_update = mysqli_prepare($con, $update_product);
    mysqli_stmt_bind_param($stmt_update, "sssiissssi", $p_title, $p_desc, $p_keywords, $p_category, $p_brand, $new_image1, $new_image2, $new_image3, $p_price, $edit_id);
    
    if(mysqli_stmt_execute($stmt_update)){
        echo "<script>alert('Product updated successfully'); window.open('indexatmin.php?view_products','_self');</script>";
    } else {
        echo "<script>alert('Error updating product');</script>";
    }
    mysqli_stmt_close($stmt_update);
}
?>

<div class="max-w-3xl mx-auto bg-white p-8 rounded-lg shadow-md">
    <h2 class="text-2xl font-bold text-center mb-6 text-gray-800">Edit Product</h2>
    
    <form method="post" enctype="multipart/form-data" class="space-y-6">
        <div>
            <label for="product_title" class="block text-sm font-medium text-gray-700">Product Title</label>
            <input type="text" id="product_title" name="product_title" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($product_title); ?>" required>
        </div>
        <div>
            <label for="product_desc" class="block text-sm font-medium text-gray-700">Product Description</label>
            <input type="text" id="product_desc" name="product_desc" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($product_desc); ?>" required>
        </div>
        <div>
            <label for="product_keyword" class="block text-sm font-medium text-gray-700">Product Keywords</label>
            <input type="text" id="product_keyword" name="product_keyword" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($product_keywords); ?>" required>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label for="product_cat" class="block text-sm font-medium text-gray-700">Product Category</label>
                <select name="product_cat" id="product_cat" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                    <?php
                    $result_cat_all = mysqli_query($con, "SELECT * FROM categories");
                    while($row_cat_all = mysqli_fetch_assoc($result_cat_all)){
                        $cat_id = $row_cat_all['category_id'];
                        $cat_title = $row_cat_all['category_title'];
                        $selected = ($cat_id == $category_id) ? 'selected' : '';
                        echo "<option value='$cat_id' $selected>" . htmlspecialchars($cat_title) . "</option>";
                    }
                    ?>
                </select>
            </div>
            <div>
                <label for="product_brand" class="block text-sm font-medium text-gray-700">Product Brand</label>
                <select name="product_brand" id="product_brand" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm">
                    <?php
                    $result_brand_all = mysqli_query($con, "SELECT * FROM brands");
                    while($row_brand_all = mysqli_fetch_assoc($result_brand_all)){
                        $b_id = $row_brand_all['brand_id'];
                        $b_title = $row_brand_all['brand_title'];
                        $selected = ($b_id == $brand_id) ? 'selected' : '';
                        if (!empty(trim($b_title))) {
                             echo "<option value='$b_id' $selected>" . htmlspecialchars($b_title) . "</option>";
                        }
                    }
                    ?>
                </select>
            </div>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label for="product_image1" class="block text-sm font-medium text-gray-700">Product Image 1</label>
                <div class="mt-1 flex items-center space-x-4">
                    <img src="./product_images/<?php echo htmlspecialchars($product_image1); ?>" class="h-24 w-24 object-cover rounded-md">
                    <input type="file" name="product_image1" class="block w-full text-sm">
                </div>
            </div>
            <div>
                <label for="product_image2" class="block text-sm font-medium text-gray-700">Product Image 2</label>
                <div class="mt-1 flex items-center space-x-4">
                    <img src="./product_images/<?php echo htmlspecialchars($product_image2); ?>" class="h-24 w-24 object-cover rounded-md">
                    <input type="file" name="product_image2" class="block w-full text-sm">
                </div>
            </div>
            <div>
                <label for="product_image3" class="block text-sm font-medium text-gray-700">Product Image 3</label>
                <div class="mt-1 flex items-center space-x-4">
                    <img src="./product_images/<?php echo htmlspecialchars($product_image3); ?>" class="h-24 w-24 object-cover rounded-md">
                    <input type="file" name="product_image3" class="block w-full text-sm">
                </div>
            </div>
        </div>
        
        <input type="hidden" name="current_image1" value="<?php echo htmlspecialchars($product_image1); ?>">
        <input type="hidden" name="current_image2" value="<?php echo htmlspecialchars($product_image2); ?>">
        <input type="hidden" name="current_image3" value="<?php echo htmlspecialchars($product_image3); ?>">

        <div>
            <label for="product_price" class="block text-sm font-medium text-gray-700">Product Price</label>
            <input type="text" id="product_price" name="product_price" class="mt-1 p-2 block w-full border border-gray-300 rounded-md shadow-sm" value="<?php echo htmlspecialchars($product_price); ?>" required>
        </div>

        <div class="text-center pt-4">
            <input type="submit" name="edit_product" class="cursor-pointer py-2 px-6 bg-gray-800 text-white font-semibold rounded-lg shadow-md hover:bg-gray-700" value="Update Product">
        </div>
    </form>
</div>