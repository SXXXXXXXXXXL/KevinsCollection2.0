    <?php

        if(isset($_GET['edit_category'])){
            $edit_category=$_GET['edit_category'];
            $get_cat="Select * from categories where category_id=$edit_category";
            $result=mysqli_query($con,$get_cat);
            $row=mysqli_fetch_assoc($result);
            $category_title=$row['category_title'];
        }

        if(isset($_POST['edit_cat'])){
            $cat_title=$_POST['category_title'];
            $update_query="Update categories set category_title='$cat_title' where category_id=$edit_category";
            $result_cat=mysqli_query($con,$update_query);
            if($result_cat){
                echo "<script>alert('Category Updated')</script>";
                echo "<script>window.open('indexatmin.php?view_category','_self')</script>";
            }
        }

    ?>

    <div class="container mt-3">
        <h1 class="text-center">Edit Category</h1>
        <form action="" method="post" class="text-center">
            <div class="form-outline mb-4 w-50 m-auto">
                <label for="category_title" class="form-label">Category Title</label>
                <input type="text" name="category_title" id="category_title" value="<?php echo $category_title ?>" class="form-control border border-dark" required="required">
            </div>
                <input type="submit" name="edit_cat" value="Update Category" class="btn btn-dark">
        </form>
    </div>