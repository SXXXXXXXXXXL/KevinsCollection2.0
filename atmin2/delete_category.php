<?php

if(isset($_GET['delete_category'])){
    $delete_id = $_GET['delete_category'];
    
    $delete_category="Delete from categories where category_id=$delete_id";
    $result_delete=mysqli_query($con,$delete_category);
    if($result_delete){
        echo "<script>alert('Category deleted')</script>";
        echo "<script>window.open('indexatmin.php?view_category','_self')</script>";
    }
}

?>