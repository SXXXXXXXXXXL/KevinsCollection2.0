<?php

//getting products
function getproducts(){
    global $con;
    // condition to check isset or not
    if(!isset($_GET['category'])){
        if(!isset($_GET['brand'])){
    
    $select_query="select * from products order by rand() LIMIT 0,6";
          $result_query=mysqli_query($con,$select_query);
          
          // echo $row['product_title']; 
          while($row=mysqli_fetch_assoc($result_query)){
            $product_id=$row['product_id'];
            $product_title=$row['product_title'];
            $product_desc=$row['product_desc'];
            $product_image1=$row['product_image1'];
            $product_price=$row['product_price'];
            $category_id	=$row['category_id'];
            $brand_id=$row['brand_id'];

            echo"<div class='col-md-4 mb-2'>
            <div class='card border-dark'>
              // <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
              <div class='card-body'>
              <h5 class='card-title'>$product_title</h5>
              <p class='card-title'><b>Rp.$product_price</b></p>
              <p class='card-text'>$product_desc</p>
              <a href='index.php?add_to_cart=$product_id' class='btn btn-dark' name='add' id='add'>Add to cart</a>
              <a href='product_details.php?product_id=$product_id' class='btn btn-outline-dark '>View more</a>              
              </div>
            </div>
          </div>";
            }
        }
    }
}

    // getting all products
    function get_all_products(){
      global $con;
      // condition to check isset or not
      if(!isset($_GET['category'])){
          if(!isset($_GET['brand'])){
      
      $select_query="select * from products order by rand()";
            $result_query=mysqli_query($con,$select_query);
            
            // echo $row['product_title']; 
            while($row=mysqli_fetch_assoc($result_query)){
              $product_id=$row['product_id'];
              $product_title=$row['product_title'];
              $product_desc=$row['product_desc'];
              $product_image1=$row['product_image1'];
              $product_price=$row['product_price'];
              $category_id	=$row['category_id'];
              $brand_id=$row['brand_id'];
  
              echo"<div class='col-md-4 mb-2'>
              <div class='card border-dark'>
                <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
                <div class='card-body'>
                <h5 class='card-title'>$product_title</h5>
                <p class='card-title'><b>Rp.$product_price</b></p>
                <p class='card-text'>$product_desc</p>
                <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
                <a href='product_details.php?product_id=$product_id' class='btn btn-outline-dark '>View more</a>              
                </div>
              </div>
            </div>";
              }
          }
      }
    }

    // getting unique categories
    function get_un_categories(){
        global $con;
        // condition to check isset or not
        if(isset($_GET['category'])){
            $category_id=$_GET['category'];

        $select_query="select * from products where category_id=$category_id";
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
                $category_id	=$row['category_id'];
                $brand_id=$row['brand_id'];
    
                echo"<div class='col-md-4 mb-2'>
                <div class='card border-dark'>
                  <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
                  <div class='card-body'>
                  <h5 class='card-title'>$product_title</h5>
                  <p class='card-title'><b>Rp.$product_price</b></p>
                  <p class='card-text'>$product_desc</p>
                  <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
                  <a href='product_details.php?product_id=$product_id' class='btn btn-outline-dark '>View more</a>              
                  </div>
                </div>
              </div>";
                }
            }
        }

         // getting unique brand
    function get_un_brand(){
        global $con;
        // condition to check isset or not
        if(isset($_GET['brand'])){
            $brand_id=$_GET['brand'];

        $select_query="select * from products where category_id=$brand_id";
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
                $category_id	=$row['category_id'];
                $brand_id=$row['brand_id'];
    
                echo"<div class='col-md-4 mb-2'>
                <div class='card border-dark'>
                  <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
                  <div class='card-body'>
                  <h5 class='card-title'>$product_title</h5>
                  <p class='card-title'><b>Rp.$product_price</b></p>
                  <p class='card-text'>$product_desc</p>
                  <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
                  <a href='product_details.php?product_id=$product_id' class='btn btn-outline-dark '>View more</a>              
                  </div>
                </div>
              </div>";
                }
            }
        }
    // display sidenav
    function getbrands(){
        global $con;
        $select_brands="Select * from brands";
          $result_brands=mysqli_query($con,$select_brands);
          while($row_data=mysqli_fetch_assoc($result_brands)){
            $brand_title=$row_data['brand_title'];
            $brand_id=$row_data['brand_id'];
            echo"<li class='nav-item bg-secondary text-light'>
            <a href='index.php?brand=$brand_id' class='nav-link'>$brand_title</a>
          </li>";
          }
    }

    // display category
    function getcategory(){
        global $con;
        $select_categories="Select * from categories";
          $result_categories=mysqli_query($con,$select_categories);
          while($row_data=mysqli_fetch_assoc($result_categories)){
            $category_title=$row_data['category_title'];
            $category_id=$row_data['category_id'];
            echo"<li class='nav-item bg-secondary text-light'>
            <a href='index.php?category=$category_id' class='nav-link'>$category_title</a>
          </li>";
          }  
    }

    // search function
    function search_product(){
        global $con;
            
            if(isset($_GET['search_data_product'])){
                $search_data_value=$_GET['search_data'];
                $search_query="Select * from products where product_keywords like '%$search_data_value%'";
            $result_query=mysqli_query($con,$search_query);
            $num_rows=mysqli_num_rows($result_query);
              if($num_rows==0){
                echo "<h3 class='text-center text-dark'>Ermm... Keyword Not Found_-</h3>";
              }
              // echo $row['product_title']; 
              while($row=mysqli_fetch_assoc($result_query)){
                $product_id=$row['product_id'];
                $product_title=$row['product_title'];
                $product_desc=$row['product_desc'];
                $product_image1=$row['product_image1'];
                $product_price=$row['product_price'];
                $category_id	=$row['category_id'];
                $brand_id=$row['brand_id'];
    
                echo"<div class='col-md-4 mb-2'>
                <div class='card border-dark'>
                  <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
                  <div class='card-body'>
                  <h5 class='card-title'>$product_title</h5>
                  <p class='card-title'><b>Rp.$product_price</b></p>
                  <p class='card-text'>$product_desc</p>
                  <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
                  <a href='product_details.php?product_id=$product_id' class='btn btn-outline-dark '>View more</a>              
                  </div>
                </div>
              </div>";
                }
            }
        }

        //view more details

        function view_details(){
          global $con;
    // condition to check isset or not
    if(isset($_GET['product_id'])){
      if(!isset($_GET['category'])){
        if(!isset($_GET['brand'])){
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
            $category_id	=$row['category_id'];
            $brand_id=$row['brand_id'];

            echo"<div class='col-md-4 mb-2'>
            <div class='card border-dark'>
              <img src='./atmin/product_images/$product_image1' class='card-img-top' alt='$product_title'>
              <div class='card-body'>
              <h5 class='card-title'>$product_title</h5>
              <p class='card-title'><b>Rp.$product_price</b></p>
              <p class='card-text'>$product_desc</p>
              <a href='index.php?add_to_cart=$product_id' class='btn btn-dark'>Add to cart</a>
              <a href='index.php' class='btn btn-outline-dark '>Go Home</a>              
              </div>
            </div>
          </div>

          <div class='col-md-8'>
          <!-- related image -->
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
        }
        
    // Get User Ip
    function getIPAddress() {  
      //whether ip is from the share internet  
       if(!empty($_SERVER['HTTP_CLIENT_IP'])) {  
                  $ip = $_SERVER['HTTP_CLIENT_IP'];  
          }  
      //whether ip is from the proxy  
      elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  
                  $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
       }  
  //whether ip is from the remote address  
      else{  
               $ip = $_SERVER['REMOTE_ADDR'];  
       }  
       return $ip;  
  }  
  
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
            echo "<script>showAlert('This item is already in your cart', 'warning');</script>";
            echo "<script>window.location.href='index.php';</script>";
        } else {
            // Insert query to add the product to the cart
            $insert_query = "INSERT INTO cart_details (product_id, ip_address, quantity) VALUES ($get_product_id, '$get_ip_add', 1)";
            $insert_result = mysqli_query($con, $insert_query);
            echo "<script>showAlert('Successfully added to cart!');</script>";
            echo "<script>window.location.href='index.php';</script>";
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

  // cart total price
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

  // get user order details
  function get_user_order_details(){
    global $con;
    $username=$_SESSION['username'];
    $get_details="Select * from user_table where username='$username'";
    $result_query = mysqli_query($con,$get_details);
    while($row_query=mysqli_fetch_array($result_query)){
      $user_id=$row_query["user_id"];
      if(!isset($_GET['edit_account'])){
        if(!isset($_GET['my_orders'])){
          if(!isset($_GET['delete_account'])){
            $get_orders="Select * from user_orders where user_id=$user_id and order_status='pending'";
            $result_orders_query = mysqli_query($con,$get_orders);
            $row_count=mysqli_num_rows($result_orders_query);
            if($row_count>0){
             echo "<h3 class='text-center mt-5'>You have <span class='text-danger'>$row_count</span> pending orders</h3>
             <p class='text-center'><a href='profile.php?my_orders'>Order Details</a></p>"; 
            }else{
              echo "<h3 class='text-center mt-5'>You have no pending orders</h3>
             <p class='text-center'><a href='../index.php'>Explore something</a></p>";
            }
            
          }
        }
      }
    }
  
  }
    
