<?php
// cart function
function cart(){
    if(isset($_GET['add_to_cart'])){
        global $con;
        $get_ip_add = getIPAddress();
        $get_product_id = $_GET['add_to_cart'];
        
        $select_query = "SELECT * FROM cart_details WHERE ip_address='$get_ip_add' AND product_id=$get_product_id";
        $result_query = mysqli_query($con, $select_query);

        if (!$result_query) {
            die('Select Query Failed: ' . mysqli_error($con));
        }

        $num_rows = mysqli_num_rows($result_query);

        if ($num_rows > 0) {
            $_SESSION['alert'] = [
                'message' => 'This item is already in your cart',
                'type' => 'warning'
            ];
            echo "<script>window.location = window.location.pathname;</script>";
        } else {
            $insert_query = "INSERT INTO cart_details (product_id, ip_address, quantity) VALUES ($get_product_id, '$get_ip_add', 1)";
            $insert_result = mysqli_query($con, $insert_query);
            $_SESSION['alert'] = [
                'message' => 'Successfully added to cart!',
                'type' => 'success'
            ];
            echo "<script>window.location = window.location.pathname;</script>";
        }
    }
}

function cart_item(){
    if(isset($_GET['add_to_cart'])){
        global $con;
        $get_ip_add = getIPAddress();
        $select_query = "select * from cart_details where ip_address='$get_ip_add'";
        $result_query = mysqli_query($con, $select_query);
        $count_cart_items = mysqli_num_rows($result_query);
    } else {
        global $con;
        $get_ip_add = getIPAddress();
        $select_query = "select * from cart_details where ip_address='$get_ip_add'";
        $result_query = mysqli_query($con, $select_query);
        $count_cart_items = mysqli_num_rows($result_query);
    }
    echo $count_cart_items;
}

function getproducts(){
    global $con;
    if(!isset($_GET['category']) && !isset($_GET['brand'])){
        $select_query="select * from products order by rand() LIMIT 0,6";
        $result_query=mysqli_query($con,$select_query);
        while($row=mysqli_fetch_assoc($result_query)){
            $product_id=$row['product_id'];
            $product_title=$row['product_title'];
            $product_desc=$row['product_desc'];
            $product_image1=$row['product_image1'];
            $product_price=$row['product_price'];
            $category_id=$row['category_id'];
            $brand_id=$row['brand_id'];
            echo "<div class='col-md-4 mb-2'>
                <div class='card border-dark h-full flex flex-col'>
                    <img src='./atmin/product_images/$product_image1' class='w-full h-48 object-cover' alt='$product_title'>
                    <div class='card-body flex-1 flex flex-col'>
                        <div class='flex-1'>
                            <h5 class='card-title text-lg font-semibold mb-2'>$product_title</h5>
                            <p class='card-title text-blue-600 font-bold mb-2'>Rp.$product_price</p>
                            <p class='card-text text-gray-600 mb-4'>$product_desc</p>
                        </div>
                        <div class='mt-auto flex gap-2'>
                            <a href='index.php?add_to_cart=$product_id' class='flex-1 bg-gray-900 text-white py-2 px-4 rounded hover:bg-gray-800 transition duration-200 text-center'>Add to cart</a>
                            <a href='product_details.php?product_id=$product_id' class='flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-50 transition duration-200 text-center'>View more</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }
}

function get_all_products(){
    global $con;
    if(!isset($_GET['category']) && !isset($_GET['brand'])){
        $select_query="select * from products order by rand()";
        $result_query=mysqli_query($con,$select_query);
        while($row=mysqli_fetch_assoc($result_query)){
            $product_id=$row['product_id'];
            $product_title=$row['product_title'];
            $product_desc=$row['product_desc'];
            $product_image1=$row['product_image1'];
            $product_price=$row['product_price'];
            $category_id=$row['category_id'];
            $brand_id=$row['brand_id'];
            echo "<div class='col-md-4 mb-2'>
                <div class='card border-dark h-full flex flex-col'>
                    <img src='./atmin/product_images/$product_image1' class='w-full h-48 object-cover' alt='$product_title'>
                    <div class='card-body flex-1 flex flex-col'>
                        <div class='flex-1'>
                            <h5 class='card-title text-lg font-semibold mb-2'>$product_title</h5>
                            <p class='card-title text-blue-600 font-bold mb-2'>Rp.$product_price</p>
                            <p class='card-text text-gray-600 mb-4'>$product_desc</p>
                        </div>
                        <div class='mt-auto flex gap-2'>
                            <a href='index.php?add_to_cart=$product_id' class='flex-1 bg-gray-900 text-white py-2 px-4 rounded hover:bg-gray-800 transition duration-200 text-center'>Add to cart</a>
                            <a href='product_details.php?product_id=$product_id' class='flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-50 transition duration-200 text-center'>View more</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }
}

function get_un_categories(){
    global $con;
    if(isset($_GET['category'])){
        $category_id=$_GET['category'];
        $select_query="select * from products where category_id=$category_id AND (available_until IS NULL OR available_until < NOW())";
        $result_query=mysqli_query($con,$select_query);
        $num_rows=mysqli_num_rows($result_query);
        if($num_rows==0){
            echo "<h3 class='text-center text-dark'>Sorry... This Category Is Out Of Stock :(</h3>";
        }
        while($row=mysqli_fetch_assoc($result_query)){
            $product_id=$row['product_id'];
            $product_title=$row['product_title'];
            $product_desc=$row['product_desc'];
            $product_image1=$row['product_image1'];
            $product_price=$row['product_price'];
            echo "<div class='col-md-4 mb-2'>
                <div class='card border-dark h-full flex flex-col'>
                    <img src='./atmin/product_images/$product_image1' class='w-full h-48 object-cover' alt='$product_title'>
                    <div class='card-body flex-1 flex flex-col'>
                        <div class='flex-1'>
                            <h5 class='card-title text-lg font-semibold mb-2'>$product_title</h5>
                            <p class='card-title text-blue-600 font-bold mb-2'>Rp.$product_price</p>
                            <p class='card-text text-gray-600 mb-4'>$product_desc</p>
                        </div>
                        <div class='mt-auto flex gap-2'>
                            <a href='index.php?add_to_cart=$product_id' class='flex-1 bg-gray-900 text-white py-2 px-4 rounded hover:bg-gray-800 transition duration-200 text-center'>Add to cart</a>
                            <a href='product_details.php?product_id=$product_id' class='flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-50 transition duration-200 text-center'>View more</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }
}

function get_un_brand(){
    global $con;
    if(isset($_GET['brand'])){
        $brand_id=$_GET['brand'];
        $select_query="SELECT * FROM products WHERE brand_id = ? AND (available_until IS NULL OR available_until < NOW())";
        $result_query=mysqli_query($con,$select_query);
        $num_rows=mysqli_num_rows($result_query);
        if($num_rows==0){
            echo "<h3 class='text-center text-dark'>Sorry... This Brand Is Out Of Stock :(</h3>";
        }
        while($row=mysqli_fetch_assoc($result_query)){
            $product_id=$row['product_id'];
            $product_title=$row['product_title'];
            $product_desc=$row['product_desc'];
            $product_image1=$row['product_image1'];
            $product_price=$row['product_price'];
            echo "<div class='col-md-4 mb-2'>
                <div class='card border-dark h-full flex flex-col'>
                    <img src='./atmin/product_images/$product_image1' class='w-full h-48 object-cover' alt='$product_title'>
                    <div class='card-body flex-1 flex flex-col'>
                        <div class='flex-1'>
                            <h5 class='card-title text-lg font-semibold mb-2'>$product_title</h5>
                            <p class='card-title text-blue-600 font-bold mb-2'>Rp.$product_price</p>
                            <p class='card-text text-gray-600 mb-4'>$product_desc</p>
                        </div>
                        <div class='mt-auto flex gap-2'>
                            <a href='index.php?add_to_cart=$product_id' class='flex-1 bg-gray-900 text-white py-2 px-4 rounded hover:bg-gray-800 transition duration-200 text-center'>Add to cart</a>
                            <a href='product_details.php?product_id=$product_id' class='flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded hover:bg-gray-50 transition duration-200 text-center'>View more</a>
                        </div>
                    </div>
                </div>
            </div>";
        }
    }
}


function getbrands(){
    global $con;
    $select_brands = "SELECT * FROM brands";
    $result_brands = mysqli_query($con, $select_brands);
    
    while($row_data = mysqli_fetch_assoc($result_brands)){
        $brand_title = htmlspecialchars($row_data['brand_title']);
        $brand_id = $row_data['brand_id'];

        $is_active = isset($_GET['brand']) && $_GET['brand'] == $brand_id;
        
        $active_class = $is_active 
            ? 'bg-blue-50 text-blue-600 border-blue-500 font-semibold' 
            : 'border-transparent text-gray-700 hover:bg-gray-100 hover:text-gray-900'; 

        if (!empty(trim($brand_title))) {
            echo "
            <li>
                <a href='index.php?brand=$brand_id' 
                   class='block px-4 py-3 border-l-4 $active_class transition-colors duration-200'>
                    $brand_title
                </a>
            </li>";
        }
    }
}

function getcategory(){
    global $con;
    $select_categories = "SELECT * FROM categories";
    $result_categories = mysqli_query($con, $select_categories);
    
    while($row_data = mysqli_fetch_assoc($result_categories)){
        $category_title = htmlspecialchars($row_data['category_title']);
        $category_id = $row_data['category_id'];

        $is_active = isset($_GET['category']) && $_GET['category'] == $category_id;
        
        $active_class = $is_active 
            ? 'bg-blue-50 text-blue-600 border-blue-500 font-semibold' 
            : 'border-transparent text-gray-700 hover:bg-gray-100 hover:text-gray-900'; 

        echo "
        <li>
            <a href='index.php?category=$category_id' 
               class='block px-4 py-3 border-l-4 $active_class transition-colors duration-200'>
                $category_title
            </a>
        </li>";
    }
}


function display_product($product_id, $product_title, $product_desc, $product_image1, $product_price) {
    
    $formatted_price = number_format($product_price, 0, ',', '.');

    echo "
    <div class='bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-transform duration-300 transform hover:-translate-y-1 flex flex-col h-full'>
        <a href='product_details.php?product_id=$product_id' class='block'>
            <img src='./atmin/product_images/" . htmlspecialchars($product_image1) . "' class='w-full h-56 object-cover' alt='" . htmlspecialchars($product_title) . "'>
        </a>
        <div class='p-4 flex flex-col flex-1'>
            <div class='flex-1 mb-4'>
                <h3 class='text-lg font-semibold text-gray-900 mb-2 truncate' title='" . htmlspecialchars($product_title) . "'>
                    <a href='product_details.php?product_id=$product_id'>" . htmlspecialchars($product_title) . "</a>
                </h3>
                <p class='text-gray-600 text-sm mb-3 line-clamp-2'>" . htmlspecialchars($product_desc) . "</p>
            </div>
            <div class='mt-auto'>
                <p class='text-xl font-bold text-blue-600 mb-4'>Rp. $formatted_price</p>
                <div class='flex space-x-2'>
                    <a href='index.php?add_to_cart=$product_id' 
                       class='flex-1 bg-gray-900 text-white py-2 px-4 rounded-md hover:bg-gray-800 transition duration-200 text-center text-sm font-medium'>
                        <i class='fas fa-cart-plus mr-2'></i>Add to cart
                    </a>
                    <a href='product_details.php?product_id=$product_id' 
                       class='flex-1 border border-gray-300 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-100 transition duration-200 text-center text-sm font-medium'>
                        View Details
                    </a>
                </div>
            </div>
        </div>
    </div>";
}



function get_pending_product_exclusion_clause() {
    $exclusion_sql = " AND product_id NOT IN (
        SELECT op.product_id 
        FROM orders_pending op
        JOIN user_orders uo ON op.order_id = uo.order_id
        WHERE uo.order_status = 'Pending' AND uo.order_date >= NOW() - INTERVAL 24 HOUR
    )";
    return $exclusion_sql;
}




function search_product(){
    global $con;
    if(isset($_GET['search_data_product'])){
        $search_data_value = $_GET['search_data'];
        
        $sort_option = $_GET['sort'] ?? 'newest';
        $order_by_clause = "ORDER BY date DESC";
        switch ($sort_option) {
            case 'price_asc':
                $order_by_clause = "ORDER BY CAST(product_price AS UNSIGNED) ASC";
                break;
            case 'price_desc':
                $order_by_clause = "ORDER BY CAST(product_price AS UNSIGNED) DESC";
                break;
        }

        $availability_clause = "AND (available_until IS NULL OR available_until < NOW())";

        $search_term = "%" . $search_data_value . "%";
        $search_query = "SELECT * FROM products WHERE product_keywords LIKE ? {$availability_clause} " . $order_by_clause;
        
        $stmt = mysqli_prepare($con, $search_query);
        mysqli_stmt_bind_param($stmt, "s", $search_term);
        mysqli_stmt_execute($stmt);
        $result_query = mysqli_stmt_get_result($stmt);
        
        $num_rows = mysqli_num_rows($result_query);
        if($num_rows == 0){
            echo "
            <div class='col-span-full text-center bg-white p-12 rounded-lg border-2 border-dashed border-gray-200'>
                <div class='inline-block p-4 bg-yellow-100 rounded-full mb-4'>
                    <i class='fas fa-search text-4xl text-yellow-500'></i>
                </div>
                <h3 class='text-2xl font-bold text-gray-800'>No Results Found</h3>
                <p class='text-gray-500 mt-2'>Sorry, we couldn't find any products matching '<span class=\"font-semibold\">" . htmlspecialchars($search_data_value) . "</span>'.</p>
            </div>";
        }

        while($row = mysqli_fetch_assoc($result_query)){
            display_product(
                $row['product_id'], $row['product_title'], $row['product_desc'],
                $row['product_image1'], $row['product_price']
            );
        }
        mysqli_stmt_close($stmt);
    }
}


function view_details(){
    global $con;
    if(isset($_GET['product_id'])){
        if(!isset($_GET['category']) && !isset($_GET['brand'])){
            $product_id=$_GET['product_id'];
            $select_query="Select * from products where product_id=$product_id";
            $result_query=mysqli_query($con,$select_query);
            while($row=mysqli_fetch_assoc($result_query)){
                $product_id=$row['product_id'];
                $product_title=$row['product_title'];
                $product_desc=$row['product_desc'];
                $product_image1=$row['product_image1'];
                $product_image2=$row['product_image2'];
                $product_image3=$row['product_image3'];
                $product_price=$row['product_price'];
                echo "<div class='col-md-4 mb-2'>
                    <div class='card border-dark'>
                        <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
                        <div class='card-body'>
                            <h5 class='card-title'>$product_title</h5>
                            <p class='card-title'><b>Rp.$product_price</b></p>
                            <p class='card-text'>$product_desc</p>
                            <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
                            <a href='index.php' class='btn btn-outline-dark'>Go Home</a>
                        </div>
                    </div>
                </div>
                <div class='col-md-8'>
                    <div class='row'>
                        <div class='col-md-12'>
                            <h4 class='text-center'>More Product Image</h4>
                            <div class='col-md-6'>
                                <img src='./atmin/product_images/$product_image2' class='card-img-top' alt='$product_title'>
                            </div>
                            <div class='col-md-6'>
                                <img src='./atmin/product_images/$product_image3' class='card-img-top' alt='$product_title'>
                            </div>
                        </div>
                    </div>
                </div>";
            }
        }
    }
}

function getIPAddress(){
    if(!empty($_SERVER['HTTP_CLIENT_IP'])){
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    }
    elseif(!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    }
    else{
        $ip = $_SERVER['REMOTE_ADDR'];
    }
    return $ip;
}

function total_cart_price(){
    global $con;
    $get_ip_add = getIPAddress();
    $total=0;
    $cart_query = "Select * from cart_details where ip_address='$get_ip_add'";
    $result_query = mysqli_query($con,$cart_query);
    while($row=mysqli_fetch_array($result_query)){
        $product_id= $row['product_id'];
        $select_products = "Select * from products where product_id='$product_id'";
        $result_products = mysqli_query($con,$select_products);
        while($row_product_price=mysqli_fetch_array($result_products)){
            $product_price=array($row_product_price['product_price']);
            $product_values=array_sum($product_price);
            $total+=$product_values;
        }
    }
    echo $total;
}



function get_user_order_details(){
    global $con;

    if (!isset($_SESSION['username'])) {
        return; 
    }

    $username = $_SESSION['username'];
    $get_details = "SELECT * FROM user_table WHERE username=?";
    
    $stmt_user = mysqli_prepare($con, $get_details);
    mysqli_stmt_bind_param($stmt_user, "s", $username);
    mysqli_stmt_execute($stmt_user);
    $result_query = mysqli_stmt_get_result($stmt_user);

    if($row_query = mysqli_fetch_assoc($result_query)){
        $user_id = $row_query["user_id"];
        
        if(!isset($_GET['edit_account']) && !isset($_GET['my_orders']) && !isset($_GET['delete_account'])){
            
            $get_orders = "SELECT * FROM user_orders WHERE user_id=? AND order_status='Pending'";
            $stmt_orders = mysqli_prepare($con, $get_orders);
            mysqli_stmt_bind_param($stmt_orders, "i", $user_id);
            mysqli_stmt_execute($stmt_orders);
            $result_orders_query = mysqli_stmt_get_result($stmt_orders);
            $row_count = mysqli_num_rows($result_orders_query);
            
            if($row_count > 0){
                echo "
                <div class='bg-yellow-50 border-l-4 border-yellow-400 p-6 rounded-lg shadow-md'>
                    <div class='flex items-start'>
                        <div class='flex-shrink-0'>
                            <i class='fas fa-hourglass-half text-2xl text-yellow-500'></i>
                        </div>
                        <div class='ml-4 flex-grow'>
                            <h3 class='text-lg font-bold text-yellow-900'>
                                You have <span class='bg-yellow-200 px-2 py-1 rounded-full'>$row_count</span> pending order(s), Please complete the payment in 24 hours or your order will be automatically canceled.
                            </h3>
                            <p class='mt-1 text-sm text-yellow-800'>
                                Please complete the payment to process your order.
                            </p>
                            <div class='mt-4'>
                                <a href='profile.php?my_orders' class='inline-block bg-yellow-500 text-white font-semibold px-4 py-2 rounded-md hover:bg-yellow-600 transition-colors duration-200 text-sm'>
                                    View Order Details
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                ";
            } else {
                echo "
                <div class='text-center bg-white p-12 rounded-lg border-2 border-dashed border-gray-200'>
                    <div class='inline-block p-4 bg-gray-100 rounded-full mb-4'>
                        <i class='fas fa-shopping-bag text-4xl text-gray-400'></i>
                    </div>
                    <h3 class='text-2xl font-bold text-gray-800'>You have no pending orders</h3>
                    <p class='text-gray-500 mt-2'>Looks like you haven't made any orders yet. Let's find something for you!</p>
                    <div class='mt-6'>
                        <a href='../index.php' class='inline-block bg-gray-800 text-white font-semibold px-6 py-2 rounded-lg hover:bg-gray-700 transition-colors'>
                            Explore Products
                        </a>
                    </div>
                </div>
                ";
            }
            mysqli_stmt_close($stmt_orders);
        }
    }
    mysqli_stmt_close($stmt_user);
}


            
?>
