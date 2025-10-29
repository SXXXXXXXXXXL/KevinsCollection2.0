<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- Font Link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body class="bg-secondary">
<div class="container-fluid my-3">
    <h2 class="text-center">Login</h2>
    <div class="row d-flex align-items-center justify-content-center">
        <div class="lg-12 col-xl-6 mt-5">
            <form action="" method="post">
                <!-- User Username -->
                <div class="form-outline mb-4">
                    <label for="user_username" class="form-label">Username</label>
                    <input type="text" id="user_username" name="user_username" class="form-control" placeholder="Enter Username.." required="required"/>
                </div>
                <!-- Password field -->
                <div class="form-outline mb-4">
                    <label for="user_pw" class="form-label">Password</label>
                    <input type="password" id="user_pw" name="user_pw" class="form-control" placeholder="Enter Password.." autocomplete="off" required="required"/>
                </div>
                <div class="text-center">
                    <input type="submit" value="Login" class="btn btn-dark px-3 py-2" name="user_login">
                </div>
                <div class="">
                    <p>Don't have an account? <a href="user_registration.php" class="text-danger">Register Now</a></p> 
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>

<?php
include('../include/connect.php');
include('../functions/common_function.php');
session_start();

if(isset($_POST['user_login'])){
    $user_username = mysqli_real_escape_string($con, $_POST['user_username']);
    $user_pw = mysqli_real_escape_string($con, $_POST['user_pw']);

    $select_query = "SELECT * FROM user_table WHERE username='$user_username'";
    $result = mysqli_query($con, $select_query);
    $user_ip = getIPAddress();
    
    // Cart items query
    $select_query_cart = "SELECT * FROM cart_details WHERE ip_address='$user_ip'";
    $select_cart = mysqli_query($con, $select_query_cart);
    $row_count_cart = mysqli_num_rows($select_cart);

    if($result && mysqli_num_rows($result) > 0){
        $row = mysqli_fetch_assoc($result);
        $hashed_pw = $row['user_pw'];
        
        if(password_verify($user_pw, $hashed_pw)){
            $_SESSION['username'] = $user_username;
            $role = $row['role'];

            if($row_count_cart > 0){
                echo "<script>alert('Logged in successfully')</script>";
                echo "<script>window.open('payment.php', '_self')</script>";
            } else {
                echo "<script>alert('Logged in successfully')</script>";
                if($role == 'admin'){
                    echo "<script>window.open('../atmin/indexatmin.php', '_self')</script>";
                } else {
                    echo "<script>window.open('profile.php', '_self')</script>";
                }
            }
        } else {
            echo "<script>alert('Invalid Credentials')</script>";
        }
    } else {
        echo "<script>alert('Invalid Credentials')</script>";
    }
}
?>
