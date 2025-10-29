<?php

if(isset($_GET['edit_brand'])){
    $edit_brand=$_GET['edit_brand'];
    $get_brands="Select * from brands where brand_id=$edit_brand";
    $result=mysqli_query($con,$get_brands);
    $row=mysqli_fetch_assoc($result);
    $brand_title=$row['brand_title'];
}

if(isset($_POST['edit_brands'])){
    $brand_title=$_POST['brand_title'];
    $update_query="Update brands set brand_title='$brand_title' where brand_id=$edit_brand";
    $result_brands=mysqli_query($con,$update_query);
    if($result_brands){
        echo "<script>alert('Brand Updated')</script>";
        echo "<script>window.open('indexatmin.php?view_brand','_self')</script>";
    }
}

?>

<div class="container mt-3">
<h1 class="text-center">Edit Brand</h1>
<form action="" method="post" class="text-center">
    <div class="form-outline mb-4 w-50 m-auto">
        <label for="brand_title" class="form-label">Brand Title</label>
        <input type="text" name="brand_title" id="brand_title" value="<?php echo $brand_title ?>" class="form-control border border-dark" required="required">
    </div>
        <input type="submit" name="edit_brands" value="Update Brand" class="btn btn-dark">
</form>
</div>