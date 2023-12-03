<?php

session_start();

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

$username = validateInput($_POST['username']);
$password = $_POST['password']; // No need to validate password at this stage

function validateInput($data)
{
    $data = trim($data);
    $data = strip_tags($data);
    $data = htmlspecialchars($data);
    return $data;
}

$lastLoginDate = new DateTime('now', new DateTimeZone('Asia/Kuala_Lumpur'));

if ($username && $password) {
    try {
        $stmt = $conn->prepare("SELECT CompanyName, Status FROM [user] WHERE UserID = :username");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $companyname = $row['CompanyName'];
            $status = $row['Status'];

            if ($status == "Disable") {
                $_SESSION['error_message'] = "Account has been terminated";
                header("Location: login.php");
                exit;
            }

            $stmt = $conn->prepare("SELECT Status FROM [company] WHERE CompanyName = :companyname");
            $stmt->bindParam(':companyname', $companyname);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $status = $row['Status'];

            if ($status == 'Disable') {
                $_SESSION['error_message'] = "Company has been terminated";
                header("Location: login.php");
                exit;
            }

            $cone = new PDO(
                "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = easywire",
                "sqladmin",
                "#Allhere",
                array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
            );

            $stmt = $cone->prepare("SELECT Username, Password, UserRole FROM [user] WHERE Username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            $row = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($row) {
                $storedPassword = $row['Password'];
                $userrole = $row['UserRole'];

                if ($password == $storedPassword) {
                    // Update LastLoginDate
                    $updateSql = "UPDATE [user] SET LastLoginDate = {$lastLoginDate->format('Y-m-d H:i:s')} WHERE Username = :username";
                    $stmt = $cone->prepare($updateSql);
                    $stmt->bindParam(':username', $username);
                    $stmt->execute();

                    $_SESSION['companyname'] = $companyname;
                    $_SESSION['username'] = $username;
                    $_SESSION['userrole'] = $userrole;
                    header("Location: dashboard/homepage.php");
                    echo "Login successful";
                    exit;
                } else {
                    $_SESSION['error_message'] = "Incorrect password";
                    header("Location: login.php");
                    exit;
                }
            } else {
                $_SESSION['error_message'] = "Username not found";
                header("Location: login.php");
                exit;
            }
        } else {
            $_SESSION['error_message'] = "Username not found";
            header("Location: login.php");
            exit;
        }
    } catch (PDOException $e) {
        die("Error: " . $e->getMessage());
    } finally {
        $conn = null;
    }
}
?>