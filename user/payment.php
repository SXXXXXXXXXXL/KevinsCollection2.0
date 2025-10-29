<?php
include('../include/connect.php');
include('../functions/common_function.php');
session_start();

// Check if the session variable is set
if (!isset($_SESSION['username'])) {
    echo "<script>alert('You\'re not logged in');</script>";
    echo "<script>window.open('user_login.php', '_self');</script>";
    exit();
}

$username = $_SESSION['username'];
$get_user = "SELECT * FROM user_table WHERE username='$username'";
$result = mysqli_query($con, $get_user);
$run_query = mysqli_fetch_array($result);

// Check if the user was found
if (!$run_query) {
    echo "<script>alert('User not found');</script>";
    echo "<script>window.open('profile.php', '_self');</script>";
    exit();
}

$user_id = $run_query['user_id'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment</title>
    <!-- Bootstrap css -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <h2 class="text-center my-5">Continue checkout?</h2>
    <div class="row d-flex justify-content-center align-items-center my-5">
        <div class="col-md-6 text-center">
            <button class="btn btn-dark btn-block py-3 px-4">
                <a href="order.php?user_id=<?php echo $user_id ?>" class="text-white text-decoration-none">Yes</a>
            </button>
        </div>
        <div class="col-md-6 text-center">
            <button class="btn btn-secondary btn-block py-3 px-4">
                <a href="../cart.php" class="text-white text-decoration-none">Cancel</a>
            </button>
        </div>
    </div>
</div>
</body>
</html>