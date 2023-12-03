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

function generateRandomAuthCode($existingAuthCodes) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $length = 7;
    $authCode = '';

    do {
        $authCode = '';
        for ($i = 0; $i < $length; $i++) {
            $authCode .= $characters[rand(0, strlen($characters) - 1)];
        }
    } while (in_array($authCode, $existingAuthCodes));

    return $authCode;
}

try {
    // Database connection
    $conn = new PDO(
        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
        "sqladmin",
        "#Allhere",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );

    // Fetch existing AuthCodes from the database
    $sql = "SELECT AuthCode FROM [company]";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $existingAuthCodes = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // Check if form is submitted
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Generate a random seven-character string that does not exist in the database
        $authCode = generateRandomAuthCode($existingAuthCodes);

        // Insert new record into the company table
        $sqlInsert = "INSERT INTO company (CompanyName, Status, AuthCode) VALUES ('', '', :authCode)";
        $stmtInsert = $conn->prepare($sqlInsert);
        $stmtInsert->bindParam(':authCode', $authCode);

        if ($stmtInsert->execute()) {
            header("Location: admincomplist.php");
            exit();
        } else {
            echo "Error: " . $stmtInsert->errorInfo()[2];
        }
    }

} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
} finally {
    // Close the database connection
    $conn = null;
}
?>



<!DOCTYPE html>
<html>

<head>
    <title>New Company AuthCode</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        h2 {
            text-align: center;
        }

        label {
            display: block;
            margin-bottom: 10px;
        }

        select,
        button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        button {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        button:hover {
            background-color: #45a049;
        }
    </style>
</head>

<body>
    <div class="container">
<h2>Generate Code</h2>
<form action="" method="post">
    <br>
    <button type="submit">Generate and Save Codes</button>
</form>

<button onclick="goBack()">Back</button>

<script>
    function goBack() {
        window.history.back();
    }
</script>

</div>
</body>
</html>