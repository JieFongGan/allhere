<?php
session_start();
function validateInput($data)
{
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validateName($name)
{
    if (!preg_match("/^[a-zA-Z0-9]+$/", $name)) {
        $_SESSION['error_message'] = "Name can only contain alphabets and numbers";
        header("Location: settings-user-new.php");
        exit;
    }
    if (strlen($name) > 255) {
        $_SESSION['error_message'] = "Name cannot exceed 255 characters";
        header("Location: settings-user-new.php");
        exit;
    }
    return $name;
}

// Validate email function
function validateEmail($email)
{
    $pattern = "/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/";
    if (preg_match($pattern, $email)) {
        return $email;
    } else {
        $_SESSION['error_message'] = "Invalid email format";
        header("Location: settings-user-new.php");
        exit;
    }
}

// Validate phone function
function validatePhone($phone)
{
    $phone = preg_replace("/[^0-9]/", "", $phone);
    if (strlen($phone) >= 10 && strlen($phone) <= 15) {
        return $phone;
    } else {
        $_SESSION['error_message'] = "Invalid phone number";
        header("Location: settings-user-new.php");
        exit;
    }
}

// Validate password function
function validatePassword($password)
{
    if (strlen($password) >= 6 && preg_match("/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).+$/", $password)) {
        return $password;
    } else {
        $_SESSION['error_message'] = "Password must be at least 6 characters long and contain at least one uppercase letter, one lowercase letter, and one number";
        header("Location: settings-user-new.php");
        exit;
    }
}

//Create user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $companyid = 1;
    $newusername = validateName($_POST['username']);
    $password = validatePassword($_POST['password']);
    $email = validateEmail($_POST['email']);
    $phone = validatePhone($_POST['phone']);
    $firstname = validateInput($_POST['firstName']);
    $lastname = validateInput($_POST['lastName']);
    $currentDateTime = date('Y-m-d H:i:s');
    $userrole = validateInput($_POST['userrole']);
    $UserStatus = "Active";

    $companyname = $_SESSION['companyname'];

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

    

    // Check if username already exists
    $sql = "SELECT * FROM user WHERE Username = :newusername";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':newusername', $newusername);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $_SESSION['error_message'] = "Username already exists";
        header("Location: settings-user-new.php");
        exit;
    }

    // Get the biggest UserID and increment it by 1
    $sql = "SELECT MAX(UserID) AS max_id FROM user";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $newUserID = $row['max_id'] + 1;

    // Create user
    $sql = "INSERT INTO [User] (UserID, CompanyID, Username, Password, Email, Phone, FirstName, LastName, UserStatus, UserRole, LastLoginDate) 
            VALUES (:newUserID, :companyid, :newusername, :password, :email, :phone, :firstname, :lastname, :UserStatus, :userrole, SYSDATETIME())";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':newUserID', $newUserID);
    $stmt->bindParam(':companyid', $companyid);
    $stmt->bindParam(':newusername', $newusername);
    $stmt->bindParam(':password', $password);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone', $phone);
    $stmt->bindParam(':firstname', $firstname);
    $stmt->bindParam(':lastname', $lastname);
    $stmt->bindParam(':UserStatus', $UserStatus);
    $stmt->bindParam(':userrole', $userrole);

    try {
        $conn->beginTransaction();
        $stmt->execute();

        // Check the connection
        try {
        $connn = new PDO("sqlsrv:server=$serverName;Database = allheredb", $uid, $pwd);
        $connn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
        // Log the error to a file for debugging purposes
        error_log("Connection failed: " . $e->getMessage(), 3, "error.log");
        // Display a user-friendly message
        echo "Connection failed. Please try again later.";
        exit();
        }

        // Create user in the new connection
        $sql = "INSERT INTO [user] (UserID, CompanyName, Status) Values (:newusername, :companyname, 'Active')";
        $stmt = $connn->prepare($sql);
        $stmt->bindParam(':newusername', $newusername);
        $stmt->bindParam(':companyname', $companyname);

        $stmt->execute();

        $conn->commit();

        // Redirect back to the previous page or perform any other action
        header('Location: settings-user.php');
        exit;
    } catch (PDOException $e) {
        $conn->rollBack();
        echo "Error creating user: " . $e->getMessage();
    }
}
?>