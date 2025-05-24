<?php
date_default_timezone_set('Asia/Manila');

require 'PHPMailer.php';
require 'SMTP.php';
require 'Exception.php';

$host = 'localhost';
$db = 'accounts';
$user = 'root';
$pass = '';

$conn = new mysqli($host, $user, $pass, $db);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST["email"]);

    // Check if email exists
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($user = $result->fetch_assoc()) {
        $token = bin2hex(random_bytes(16));
        $expiry = date("Y-m-d H:i:s", strtotime('+15 minutes'));

        // Save token and expiry
        $stmt = $conn->prepare("UPDATE users SET reset_token = ?, token_expiry = ? WHERE email = ?");
        $stmt->bind_param("sss", $token, $expiry, $email);
        $stmt->execute();

        // Send email
        $mail = new PHPMailer\PHPMailer\PHPMailer(true);

        try {
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';            // Replace with your SMTP server
            $mail->SMTPAuth = true;
            $mail->Username = 'lewiskieltierra@gmail.com';  // Replace with your email
            $mail->Password = 'ogsiqdybkeelujai';     // Replace with your app password
            $mail->SMTPSecure = 'tls';                 // Use 'ssl' if needed
            $mail->Port = 587;                         // Use 465 for SSL

            $mail->setFrom('your-email@gmail.com', 'Compass Trip Planner');
            $mail->addAddress($email);

            $mail->Subject = 'Password Reset Request';
            $mail->Body = "Click the link below to reset your password:\n\n" .
                          "http://localhost/signup-login/reset-password.php?token=$token";

            $mail->send();
            $message = "Reset link sent to your email.";
        } catch (Exception $e) {
            $message = "Mailer Error: " . $mail->ErrorInfo;
        }
    } else {
        $message = "Email not found.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link rel="stylesheet" type="text/css" href="styles/forgotPass-style.css">
</head>
<body>
    <div class="reset-container">
        <h2>Forgot Password</h2>
        <form method="post">
            <label>Email:</label>
            <input type="email" name="email" required>
            <button type="submit">Send Reset Link</button>
        </form>
        <p class="message"><?php echo $message; ?></p>
    </div>
</body>
</html>
