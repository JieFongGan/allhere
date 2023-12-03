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


$serverName = "tcp:allhereserver.database.windows.net,1433";
$database = "allheredb";
$username = "sqladmin";
$password = "#Allhere";

try {
    $conn = new PDO("sqlsrv:server=$serverName;Database=$database", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$userid = isset($_GET['userid']) ? $_GET['userid'] : '';
if (empty($userid)) {
    die("User ID is required.");
}

$sql = "SELECT * FROM [user] WHERE UserID = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$userid]);
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$result) {
    die("No data found.");
}

$companyName = $result['CompanyName'];
$status = $result['Status'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newStatus = isset($_POST['status']) ? $_POST['status'] : '';

    $updateUserSql = "UPDATE [user] SET Status = ? WHERE UserID = ?";
    $updateUserStmt = $conn->prepare($updateUserSql);
    $updateUserStmt->execute([$newStatus, $userid]);

    // You may want to check if the user's company name is not empty before creating a new connection
    if (!empty($companyName)) {
        try {
            $connn = new PDO("sqlsrv:server=$serverName;Database=$companyName", $username, $password);
            $connn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

            $updateCompanySql = "UPDATE [user] SET UserStatus = ? WHERE Username = ?";
            $updateCompanyStmt = $connn->prepare($updateCompanySql);
            $updateCompanyStmt->execute([$newStatus, $userid]);

            header('Location: adminuserlist.php');
            exit;
        } catch (PDOException $e) {
            echo "Error updating user details: " . $e->getMessage();
        }
    }
}

$conn = null;
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
            <label for="userid">UserID:</label>
            <input type="text" id="userid" name="userid" value="<?php echo $_GET['userid']; ?>" readonly><br><br>

            <label for="companyname">Company Name:</label>
            <input type="text" id="companyname" name="companyname" value="<?php echo $companyName; ?>" readonly><br><br>

            <label for="status">Status:</label>
            <select id="status" name="status">
                <option value="Active" <?php if ($status == 'Active')
                    echo 'selected'; ?>>Active</option>
                <option value="Disable" <?php if ($status == 'Disable')
                    echo 'selected'; ?>>Disable</option>
            </select><br><br>

            <input type="submit" value="Submit">
            <input type="reset" value="Reset">
            <a href="adminuserlist.php">Back</a>
        </form>
    </div>
</body>

</html>