<?php

if(isset($_GET['delete_brand'])){
    $delete_id = $_GET['delete_brand'];
    
    $delete_brand="Delete from brands where brand_id=$delete_id";
    $result_delete=mysqli_query($con,$delete_brand);
    if($result_delete){
        echo "<script>alert('brand deleted')</script>";
        echo "<script>window.open('indexatmin.php?view_brand','_self')</script>";
    }
}

?>