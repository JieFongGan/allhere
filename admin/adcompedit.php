<?php

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['admin'])) {
    $admin = $_SESSION['admin'];
} else {
    header("Location: adlogin.php");
    exit();
}

// Connect to the database
$serverName = "tcp:allhereserver.database.windows.net,1433";
$database = "allheredb";
$username = "sqladmin";
$password = "#Allhere";

try {
    $conn = new PDO(
        "sqlsrv:server=$serverName;Database=$database",
        $username,
        $password,
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Sanitize and validate AuthCode
$authCode = $_GET['authcode'];

// Check if AuthCode is empty
if (empty($authCode)) {
    die("AuthCode is required.");
}

$sql = "SELECT * FROM [dbo].[company] WHERE AuthCode = :authCode";
$stmt = $conn->prepare($sql);
$stmt->execute([':authCode' => $authCode]); 
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row) {
    $companyName = $row['CompanyName'];
    $status = $row['Status'];
} else {
    $companyName = '';
    $status = '';
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the form data
    $companyName = $_POST['companyname'];
    $status = $_POST['status'];

    // Validate form inputs
    if (empty($companyName) || empty($status)) {
        die("Company Name and Status are required.");
    }

    // Update the company details in the database
    $sql = "UPDATE [company] SET CompanyName = :companyName, Status = :status WHERE AuthCode = :authCode";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':companyName', $companyName, PDO::PARAM_STR);
    $stmt->bindParam(':status', $status, PDO::PARAM_STR);
    $stmt->bindParam(':authCode', $authCode, PDO::PARAM_STR);
    
    try {
        $stmt->execute();
        // Redirect back to the previous page or perform any other action
        header('Location: admincomplist.php');
        exit;
    } catch (PDOException $e) {
        echo "Error updating company details: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Company</title>
    <style>
        /* Add your CSS styling here */
        .container {
            /* Add your container styles */
            margin: 20px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        /* Add more CSS styles as needed */
        label {
            display: block;
            margin-bottom: 10px;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }
        input[type="submit"],
        input[type="reset"],
        a {
            display: inline-block;
            padding: 5px 10px;
            margin-right: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 3px;
        }
        input[type="submit"]:hover,
        input[type="reset"]:hover,
        a:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="container" style="margin: 0 auto; width: 50%;">
        <h2>Edit Company Details</h2>
        <form method="POST" action="">
            <label for="authcode">AuthCode:</label>
            <input type="text" id="authcode" name="authcode" value="<?php echo $_GET['authcode']; ?>" readonly><br><br>

            <label for="companyname">Company Name:</label>
            <input type="text" id="companyname" name="companyname" value="<?php echo $companyName; ?>" readonly><br><br>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Active" <?php if ($status == 'Active') echo 'selected'; ?>>Active</option>
                <option value="Disable" <?php if ($status == 'Disable') echo 'selected'; ?>>Disable</option>
            </select><br><br>

            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
            <a href="admincomplist.php">Back</a>
        </form>
    </div>
</body>
</html>
