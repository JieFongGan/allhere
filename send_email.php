<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

// Assuming you have already established a database connection
session_start(); // Start the session at the beginning of the script

$username = $_POST['username'];
$companyName = $_POST['companyName'];

try {
    $checkvalidcompany = new PDO(
        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
        "sqladmin",
        "#Allhere"
    );

    // Use prepared statements to prevent SQL injection
    $checkvalidcompanyquery = "SELECT CompanyName FROM [user] WHERE CompanyName = :companyName";
    $stmt = $checkvalidcompany->prepare($checkvalidcompanyquery);
    $stmt->bindParam(':companyName', $companyName);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $_SESSION['error_message'] = "Company does not exist";
        header("Location: forgetpassword.php");
        exit;
    }
} catch (PDOException $e) {
    $_SESSION['error_message'] = "Error checking company existence: " . $e->getMessage();
    header("Location: forgetpassword.php");
    exit;
}

try {
    $conn = new PDO(
        "sqlsrv:server = tcp:yourserver.database.windows.net,1433;Database=$companyName",
        "sqladmin",
        "#Allhere"
    );

    // Use prepared statements to prevent SQL injection
    $query = "SELECT Email, Password FROM [user] WHERE username = :username";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row) {
        $password = $row['password'];
        $email = $row['email'];

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'allherewebapp@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'pplcxcrsocwxnkpx'; // Replace with your SMTP password
        $mail->SMTPSecure = 'tls'; // Use TLS instead of SSL
        $mail->Port = 587; // Azure uses port 587 for TLS

        // Recipients
        $mail->setFrom('allherewebapp@gmail.com', 'All Here');
        $mail->addAddress($email); // Add recipient

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Password Reminder';
        $mail->Body = "Your password is: $password";

        $mail->send();
        header('Location: login.php');
        exit;
    } else {
        $_SESSION['error_message'] = "Failed to retrieve password from the database.";
        header("Location: forgetpassword.php");
        exit;
    }
} catch (Exception $e) {
    $_SESSION['error_message'] = "Failed to send email. Error: {$mail->ErrorInfo}";
    header("Location: forgetpassword.php");
    exit;
} finally {
    // Close the database connection
    $conn = null;
}
?>
