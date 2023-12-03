<?php
session_start();

// Check if form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get username and password from form
    $username = $_POST["username"];
    $password = $_POST["password"];
    echo $username;
    echo $password;

    try {
        // Connect to the Azure SQL Database
        $conn = new PDO(
            "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
            "sqladmin",
            "#Allhere",
            array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
        );

        // Prepare and execute the query with parameters
        $stmt = $conn->prepare("SELECT * FROM admin WHERE AdminID = :username");
        $stmt->execute(['username' => $username]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // Check if username and password match
        if ($result && $password == $result['AdminPassword']) {
            $_SESSION["admin"] = $username;
            header("Location: admincomplist.php");
            exit();
        } else {
            // Display error message
            echo "Incorrect username or password";
        }
    } catch (PDOException $e) {
        // Log the error and handle it appropriately
        error_log("Database connection failed: " . $e->getMessage(), 3, "error.log");
        echo "Database connection failed. Please try again later.";
        exit();
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Login</title>
    <style>
        body {
            background-color: blue;
        }
        .container {
            background-color: white;
            padding: 20px;
            margin: 0 auto;
            width: 300px;
            text-align: center;
            margin-top: 100px;
        }
        .form-group {
            margin-bottom: 10px;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 5px;
            margin-bottom: 10px;
        }
        button[type="submit"] {
            padding: 10px 20px;
            background-color: blue;
            color: white;
            border: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Admin Login</h2>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <button type="submit">Login</button>
            </div>
        </form>
    </div>
</body>
</html>
