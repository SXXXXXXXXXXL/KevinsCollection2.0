<?php
include('../include/connect.php');

$message = '';
$message_type = ''; // 'success' or 'error'
$token_is_valid = false;
$token = '';

// Step 1: Validate Token from URL
if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $stmt = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result) > 0) {
        $token_is_valid = true;
    } else {
        $message_type = 'error';
        $message = 'That reset token is invalid or expired. Go request a new one.';
    }
} else {
     $message_type = 'error';
     $message = 'No token found.';
}

// Step 2: Process New Password Form
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_password'])) {
    $token_from_form = $_POST['token'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Re-validate token from form
    $stmt_check = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE reset_token = ? AND reset_token_expires_at > NOW()");
    mysqli_stmt_bind_param($stmt_check, "s", $token_from_form);
    mysqli_stmt_execute($stmt_check);
    $result_check = mysqli_stmt_get_result($stmt_check);
    mysqli_stmt_close($stmt_check);

    if (mysqli_num_rows($result_check) > 0) {
        if ($new_password !== $confirm_password) {
            $message_type = 'error';
            $message = 'Those passwords don\'t match.';
            $token_is_valid = true; // Keep form visible
        } elseif (strlen($new_password) < 6) { // Example password length validation
            $message_type = 'error';
            $message = 'Your password needs to be at least 6 characters.';
            $token_is_valid = true; // Keep form visible
        } else {
            // All valid, update password
            $row = mysqli_fetch_assoc($result_check);
            $user_id = $row['user_id'];

            // Securely hash new password
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

            // Update password and nullify token so it can't be reused
            $stmt_update = mysqli_prepare($con, "UPDATE user_table SET user_pw = ?, reset_token = NULL, reset_token_expires_at = NULL WHERE user_id = ?");
            mysqli_stmt_bind_param($stmt_update, "si", $hashed_password, $user_id);
            mysqli_stmt_execute($stmt_update);
            mysqli_stmt_close($stmt_update);

            $message_type = 'success';
            $message = 'Your password has been reset! Go ahead and log in with your new password.';
            $token_is_valid = false; // Hide form on success
        }
    } else {
        $message_type = 'error';
        $message = 'That password reset session isn\'t valid. Try again.';
        $token_is_valid = false;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Reset Your Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your new password below.</p>
        </div>

        <?php if ($message): ?>
        <div class="p-4 rounded-md <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
            <p><?php echo $message; ?></p>
        </div>
        <?php endif; ?>

        <?php if ($token_is_valid): ?>
        <form class="space-y-6" action="" method="POST">
            <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700">New Password</label>
                <div class="mt-1">
                    <input id="new_password" name="new_password" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>
             <div>
                <label for="confirm_password" class="block text-sm font-medium text-gray-700">Confirm New Password</label>
                <div class="mt-1">
                    <input id="confirm_password" name="confirm_password" type="password" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <button type="submit" name="submit_password"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Reset Password
                </button>
            </div>
        </form>
        <?php endif; ?>
        
        <div class="text-center text-sm">
            <a href="user_login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>