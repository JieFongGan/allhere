<?php

session_start();

$companyid = '1';
$companyname = validateName($_POST['company_name']);
$companyemail = validateEmail($_POST['company_email']);
$companyphone = validatePhone($_POST['company_phone_number']);
$companyaddress = validateInput($_POST['company_address']);
$username = validateName($_POST['username']);
$password = validatePassword($_POST['password']);
$email = validateEmail($_POST['email']);
$phone = validatePhone($_POST['phone_number']);
$firstname = validateInput($_POST['first_name']);
$lastname = validateInput($_POST['last_name']);
$status = "Active";

// Validate input function
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
        header("Location: register.php");
        exit;
    }
    if (strlen($name) > 255) {
        $_SESSION['error_message'] = "Name cannot exceed 255 characters";
        header("Location: register.php");
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
        header("Location: register.php");
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
        header("Location: register.php");
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
        header("Location: register.php");
        exit;
    }
}

$authCode = validateInput($_POST['auth_code']);

if (empty($authCode)) {
    $_SESSION['error_message'] = "No authentication code found";
    header("Location: register.php");
    exit;
}

// Database connection
try {
    $conn = new PDO(
        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
        "sqladmin",
        "#Allhere",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
    );
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Check if the user already exists
$sql = "SELECT UserID FROM [User] WHERE UserID = :username";
$stmt = $conn->prepare($sql);
$stmt->bindParam(":username", $username, PDO::PARAM_STR);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    $_SESSION['error_message'] = "Username already exists";
    header("Location: register.php");
    exit;
}

// Bind the parameters
$stmt = $conn->prepare("SELECT CompanyName FROM [company] WHERE AuthCode = :authCode");
$stmt->bindParam(":authCode", $authCode, PDO::PARAM_STR);

// Execute the statement
$stmt->execute();

// Fetch the result
$companynamestore = $stmt->fetchColumn();
$companyname = strtolower($companyname);

// Check if CompanyName is not an empty string
if ($companynamestore != "") {
    $_SESSION['error_message'] = "Authentication Code is not available. $companynamestore!";
    header("Location: register.php");
    exit;
} else {
    $stmt->closeCursor();
    if (!empty($companynamestore)) {
        $_SESSION['error_message'] = "Company already exists.";
        header("Location: register.php");
        exit;
    } else {

        try {
            $cone = new PDO(
                "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = master",
                "sqladmin",
                "#Allhere",
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Dynamically create the database
        $cone->query("CREATE DATABASE [$companyname] (EDITION = 'basic')");

        // Create a new connection to the database
        try {
            $cono = new PDO(
                "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = $companyname",
                "sqladmin",
                "#Allhere",
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }

        // Create tables
        $cono->query("CREATE TABLE Company (
            CompanyID INT PRIMARY KEY,
            CompanyName VARCHAR(255) NOT NULL,
            Email VARCHAR(255),
            Phone VARCHAR(20),
            Address VARCHAR(255)
        )");

        // Insert values into Company table
        $cono->query("INSERT INTO Company (CompanyID, CompanyName, Email, Phone, Address) VALUES ('$companyid', '$companyname', '$companyemail', '$companyphone', '$companyaddress')");

        $cono->query("CREATE TABLE [User] (
            UserID INT PRIMARY KEY,
            CompanyID INT,
            Username VARCHAR(50) NOT NULL,
            Password VARCHAR(255) NOT NULL,
            Email VARCHAR(255),
            Phone VARCHAR(20),
            FirstName VARCHAR(50),
            LastName VARCHAR(50),
            UserRole VARCHAR(50),
            LastLoginDate DATETIME,
            UserStatus VARCHAR(20),
            FOREIGN KEY (CompanyID) REFERENCES Company(CompanyID)
        )");

        $cono->query("INSERT INTO [User] (UserID, CompanyID, Username, Password, Email, Phone, FirstName, LastName, UserRole, LastLoginDate, UserStatus) VALUES ('1', '$companyid', '$username', '$password', '$email', '$phone', '$firstname', '$lastname', 'Admin', SYSDATETIME() , '$status')");

        $cono->query("CREATE TABLE Category (
            CategoryID INT PRIMARY KEY,
            Name VARCHAR(50) NOT NULL,
            Description TEXT
        )");

        $cono->query("CREATE TABLE Warehouse (
            WarehouseID INT PRIMARY KEY,
            Name VARCHAR(255) NOT NULL,
            Address VARCHAR(255),
            Contact VARCHAR(20),
            Email VARCHAR(255)
        )");

        $cono->query("CREATE TABLE Customer (
            CustomerID INT PRIMARY KEY,
            Name VARCHAR(255) NOT NULL,
            Contact VARCHAR(20),
            Email VARCHAR(255),
            Address VARCHAR(255),
            Remark VARCHAR(255)
        )");

        $cono->query("CREATE TABLE Product (
            ProductID INT PRIMARY KEY,
            CategoryID INT,
            WarehouseID INT,
            Name VARCHAR(255) NOT NULL,
            Description TEXT,
            Price DECIMAL(10, 2),
            Quantity INT,
            LastUpdatedDate DATETIME,
            FOREIGN KEY (CategoryID) REFERENCES Category(CategoryID),
            FOREIGN KEY (WarehouseID) REFERENCES Warehouse(WarehouseID)
        )");

        $cono->query("CREATE TABLE [Transaction] (
            TransactionID INT PRIMARY KEY,
            WarehouseID INT,
            CustomerID INT,
            TransactionType VARCHAR(50),
            TransactionDate DATETIME,
            DeliveryStatus VARCHAR(50),
            FOREIGN KEY (WarehouseID) REFERENCES Warehouse(WarehouseID),
            FOREIGN KEY (CustomerID) REFERENCES Customer(CustomerID)
        )");

        $cono->query("CREATE TABLE TransactionDetail (
            TransactionDetailID INT PRIMARY KEY,
            TransactionID INT,
            ProductID INT,
            Quantity INT,
            FOREIGN KEY (TransactionID) REFERENCES [Transaction](TransactionID),
            FOREIGN KEY (ProductID) REFERENCES Product(ProductID)
        )");

        // Update Company
        $sqlUpdateCompany = "UPDATE [Company] SET CompanyName = :companyname, Status = :status WHERE AuthCode = :authCode";
        $stmtUpdateCompany = $conn->prepare($sqlUpdateCompany);
        $stmtUpdateCompany->bindParam(':companyname', $companyname, PDO::PARAM_STR);
        $stmtUpdateCompany->bindParam(':status', $status, PDO::PARAM_STR);
        $stmtUpdateCompany->bindParam(':authCode', $authCode, PDO::PARAM_STR);

        try {
            $conn->beginTransaction();

            if ($stmtUpdateCompany->execute()) {
                echo "Company data updated successfully";
            } else {
                echo "Error updating company data: " . $stmtUpdateCompany->errorInfo()[2];
                $conn->rollBack();
                exit;
            }

            // Insert data into the User table
            $sqlInsertUser = "INSERT INTO [User] (UserID, CompanyName, Status) VALUES (:username, :companyname, :status)";
            $stmtInsertUser = $conn->prepare($sqlInsertUser);
            $stmtInsertUser->bindParam(':username', $username, PDO::PARAM_STR);
            $stmtInsertUser->bindParam(':companyname', $companyname, PDO::PARAM_STR);
            $stmtInsertUser->bindParam(':status', $status, PDO::PARAM_STR);

            if ($stmtInsertUser->execute()) {
                echo "User data inserted successfully";
                $conn->commit();
            } else {
                echo "Error inserting user data: " . $stmtInsertUser->errorInfo()[2];
                $conn->rollBack();
            }

        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            $conn->rollBack();
        }

        $_SESSION['companyname'] = $companyname;
        $_SESSION['username'] = $username;
        $_SESSION['userrole'] = "Admin";
        header("Location: dashboard/homepage.php");
    }
}

?>