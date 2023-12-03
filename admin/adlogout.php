<?php
session_start(); // Start the session
session_destroy(); // Destroy all session data

header("Location: adlogin.php"); // Redirect to login.php
exit(); // Terminate the script execution
?>