<?php

$feedback_message = ''; 

$user_id = null;
if (isset($_SESSION['username'])) {
    $user_session_name = $_SESSION['username'];
    
    $stmt_select = mysqli_prepare($con, "SELECT * FROM user_table WHERE username = ?");
    mysqli_stmt_bind_param($stmt_select, "s", $user_session_name);
    mysqli_stmt_execute($stmt_select);
    $result_query = mysqli_stmt_get_result($stmt_select);
    $row_fetch = mysqli_fetch_assoc($result_query);

    if ($row_fetch) {
        $user_id = $row_fetch['user_id'];
        $username = $row_fetch['username'];
        $user_email = $row_fetch['user_email'];
        $user_address = $row_fetch['user_address'];
        $user_mobile = $row_fetch['user_mobile'];
        $user_image_old = $row_fetch['user_image']; 
    }
    mysqli_stmt_close($stmt_select);
}

if (is_null($user_id)) {
    die("Error: User session not found.");
}


if (isset($_POST['user_update'])) {
    $update_id = $user_id;
    $username_new = trim($_POST['username']);
    $user_email_new = trim($_POST['user_email']);
    $user_address_new = trim($_POST['user_address']);
    $user_mobile_new = trim($_POST['user_mobile']);
    
    $user_image_new = $user_image_old;

    if (isset($_FILES['user_image']) && $_FILES['user_image']['error'] === UPLOAD_ERR_OK) {
        $user_image_tmp = $_FILES['user_image']['tmp_name'];
        $original_filename = basename($_FILES['user_image']['name']);
        
        $new_filename = uniqid() . '-' . $original_filename;
        $target_path = "user_images/" . $new_filename;

        if (move_uploaded_file($user_image_tmp, $target_path)) {
            $user_image_new = $new_filename; 
            
            if (!empty($user_image_old) && file_exists("user_images/" . $user_image_old)) {
                // unlink("user_images/" . $user_image_old);
            }
        }
    }

    $update_data = "UPDATE user_table SET username=?, user_email=?, user_image=?, user_address=?, user_mobile=? WHERE user_id=?";
    $stmt_update = mysqli_prepare($con, $update_data);
    mysqli_stmt_bind_param($stmt_update, "sssssi", $username_new, $user_email_new, $user_image_new, $user_address_new, $user_mobile_new, $update_id);
    
    if (mysqli_stmt_execute($stmt_update)) {
        $_SESSION['username'] = $username_new;
        $feedback_message = "<div class='bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6' role='alert'>Data updated successfully. Page will refresh.</div>";
        echo "<script>setTimeout(() => { window.location.href = 'profile.php?edit_account'; }, 2000);</script>";
    } else {
         $feedback_message = "<div class='bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6' role='alert'>Error updating data.</div>";
    }
    mysqli_stmt_close($stmt_update);
}
?>

<div class="max-w-xl mx-auto">
    <h3 class="text-2xl font-bold text-center mb-6 text-gray-800">Edit Account</h3>
    
    <?php echo $feedback_message; ?>

    <form action="" method="post" enctype="multipart/form-data" class="space-y-6 bg-white p-8 rounded-lg shadow-md">
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700">Username</label>
            <div class="mt-1">
                <input type="text" id="username" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-800 focus:border-gray-800 sm:text-sm p-2 border" value="<?php echo htmlspecialchars($username); ?>" name="username">
            </div>
        </div>

        <div>
            <label for="user_email" class="block text-sm font-medium text-gray-700">E-mail</label>
            <div class="mt-1">
                <input type="email" id="user_email" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-800 focus:border-gray-800 sm:text-sm p-2 border" value="<?php echo htmlspecialchars($user_email); ?>" name="user_email">
            </div>
        </div>
        
        <div class="flex items-center space-x-4">
            <div class="flex-shrink-0">
                <img class="h-16 w-16 rounded-full object-cover" src="./user_images/<?php echo htmlspecialchars($user_image_old); ?>" alt="Current profile photo">
            </div>
            <div class="flex-grow">
                <label for="user_image" class="block text-sm font-medium text-gray-700">Change Image</label>
                <input type="file" id="user_image" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-gray-100 file:text-gray-800 hover:file:bg-gray-200" name="user_image">
            </div>
        </div>

        <div>
            <label for="user_address" class="block text-sm font-medium text-gray-700">Address</label>
            <div class="mt-1">
                <input type="text" id="user_address" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-800 focus:border-gray-800 sm:text-sm p-2 border" value="<?php echo htmlspecialchars($user_address); ?>" name="user_address">
            </div>
        </div>

        <div>
            <label for="user_mobile" class="block text-sm font-medium text-gray-700">Mobile Number</label>
            <div class="mt-1">
                <input type="text" id="user_mobile" class="block w-full border-gray-300 rounded-md shadow-sm focus:ring-gray-800 focus:border-gray-800 sm:text-sm p-2 border" value="<?php echo htmlspecialchars($user_mobile); ?>" name="user_mobile">
            </div>
        </div>

        <div class="text-center pt-4">
            <input type="submit" class="w-full cursor-pointer inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-gray-800 hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-800" name="user_update" value="Update Account">
        </div>
    </form>
</div>