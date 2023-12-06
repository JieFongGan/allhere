<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'phpmailer/src/Exception.php';
require 'phpmailer/src/PHPMailer.php';
require 'phpmailer/src/SMTP.php';

session_start();

$username = $_POST['username'];
$companyName = $_POST['companyName'];

try {
    $connect = new PDO(
        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
        "sqladmin",
        "#Allhere"
    );

    // Use prepared statements to prevent SQL injection
    $sql = "SELECT CompanyName FROM [user] WHERE UserID = :username";
    $stmt = $connect->prepare($sql);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->execute(); // Execute the query
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || empty($row['CompanyName'])) {
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
    $query = "SELECT Email, Password FROM [user] WHERE Username = :username";
    $stmte = $conn->prepare($query);
    $stmte->bindParam(':username', $username);
    $stmte->execute();

    $rows = $stmte->fetch(PDO::FETCH_ASSOC);

    if ($rows) {
        $email = $rows['Email'];
        $password = $rows['Password'];

        // Send email using PHPMailer
        $mail = new PHPMailer(true);

        // Server settings
        $mail->SMTPDebug = 0; // Set to 2 for debugging
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server
        $mail->SMTPAuth = true;
        $mail->Username = 'koroxermarxus@gmail.com'; // Replace with your SMTP username
        $mail->Password = 'jnya qbeg ppgk fphd'; // Replace with your SMTP password
        $mail->SMTPSecure = 'tls'; // Use TLS instead of SSL
        $mail->Port = 587; // Azure uses port 587 for TLS

        // Recipients
        $mail->setFrom('koroxermarxus@gmail.com', 'All Here');
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
    $_SESSION['error_message'] = "Failed to send email. Error: {$e->getMessage()}";
    header("Location: forgetpassword.php");
    exit;
}
?>