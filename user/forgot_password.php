<?php
include('../include/connect.php');
// You will need a function to send emails. We'll create a placeholder for it.
include('../api/send_email.php');

$message = '';
$message_type = ''; // 'success' or 'error'

// This logic runs when the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_email'])) {
    $email = $_POST['email'];

    // 1. Check if the email exists in the database
    $stmt = mysqli_prepare($con, "SELECT user_id FROM user_table WHERE user_email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);

    if (mysqli_num_rows($result) > 0) {
        // Email found, continue the process
        $row = mysqli_fetch_assoc($result);
        $user_id = $row['user_id'];

        // 2. Create a secure reset token
        $token = bin2hex(random_bytes(32));

        // 3. Save the token and expiration time to the database
        // Let MySQL calculate the expiration time using its internal NOW() function
        $stmt_update = mysqli_prepare($con, "UPDATE user_table SET reset_token = ?, reset_token_expires_at = DATE_ADD(NOW(), INTERVAL 1 HOUR) WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt_update, "si", $token, $user_id);
        mysqli_stmt_execute($stmt_update);
        mysqli_stmt_close($stmt_update);

        // 4. Send the email to the user (REPLACE WITH YOUR EMAIL FUNCTION)
        // This is a placeholder. You must implement an email sending function
        // using a library like PHPMailer.
        $reset_link = "http://localhost/SKRIPSI/MAIN/user/reset_password.php?token=" . $token;
        send_reset_email($email, $reset_link); // Example call to your email function

        // IMPORTANT: Do not tell the user if their email exists or not.
        // This is to prevent 'user enumeration attacks'.
    }

    // Always show the same success message
    $message_type = 'success';
    $message = 'If your email address is registered in our system, you will receive an email with a link to reset your password.';

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
</head>
<body class="bg-gray-100">

<div class="min-h-screen flex flex-col items-center justify-center">
    <div class="w-full max-w-md p-8 space-y-6 bg-white rounded-xl shadow-lg">
        <div class="text-center">
            <h2 class="text-3xl font-bold text-gray-900">Forgot Password</h2>
            <p class="mt-2 text-sm text-gray-600">Enter your email address to receive a password reset link.</p>
        </div>

        <?php if ($message): ?>
        <div class="p-4 rounded-md <?php echo $message_type === 'success' ? 'bg-green-50 text-green-700' : 'bg-red-50 text-red-700'; ?>">
            <p><?php echo $message; ?></p>
        </div>
        <?php endif; ?>

        <form class="space-y-6" action="" method="POST">
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700">Email Address</label>
                <div class="mt-1">
                    <input id="email" name="email" type="email" autocomplete="email" required
                           class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500">
                </div>
            </div>

            <div>
                <button type="submit" name="submit_email"
                        class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                    Send Reset Link
                </button>
            </div>
        </form>
         <div class="text-center text-sm">
            <a href="user_login.php" class="font-medium text-indigo-600 hover:text-indigo-500">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>