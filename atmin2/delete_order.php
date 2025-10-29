    <?php

    if(isset($_GET['delete_order'])){
        $delete_id = $_GET['delete_order'];
        
        $delete_orders="Delete from user_orders where order_id=$delete_id";
        $result_delete=mysqli_query($con,$delete_orders);
        if($result_delete){
            echo "<script>alert('Order deleted')</script>";
            echo "<script>window.open('indexatmin.php?list_orders','_self')</script>";
        }
    }

    ?>