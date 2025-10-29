<?php

if(isset($_GET['delete_products'])){
    $delete_id = $_GET['delete_products'];
    
    $delete_products="Delete from products where product_id=$delete_id";
    $result_delete=mysqli_query($con,$delete_products);
    if($result_delete){
        echo "<script>alert('Product deleted')</script>";
        echo "<script>window.open('indexatmin.php?view_products','_self')</script>";
    }
}

?>