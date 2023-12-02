<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the session variable is set
if (isset($_SESSION['companyname'])) {
    $companyname = $_SESSION['companyname'];
    $username = $_SESSION['username'];
    $userrole = $_SESSION['userrole'];
} else {
    header("Location: ../login.php");
    exit();
}

// Replace these values with your Azure SQL Database connection details
$serverName = "tcp:allhereserver.database.windows.net,1433";
$database = $companyname;
$uid = "sqladmin";
$pwd = "#Allhere";

// Check the connection
try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $uid, $pwd);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Log the error to a file for debugging purposes
    error_log("Connection failed: " . $e->getMessage(), 3, "error.log");
    // Display a user-friendly message
    echo "Connection failed. Please try again later.";
    exit();
}

?>