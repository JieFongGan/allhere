<?php
ob_start(); // Start output buffering
$pageTitle = "Settings/User-edit";
include '../database/database-connect.php';
include '../contain/header.php';

if (isset($_GET['userID'])) {
    $userID = $_GET['userID'];

    // Fetch user information based on the user ID
    $sql = "SELECT * FROM [User] WHERE UserID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(1, $userID, PDO::PARAM_INT);
    $stmt->execute();
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$userData) {
        // Handle the case where no user is found with the given ID
        echo "User not found.";
        exit();
    }
} else {
    // Handle the case where user ID is not set
    echo "User ID not specified.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if the user ID is set in the form
    if (isset($_POST['userID'])) {
        $userID = $_POST['userID'];

        // Retrieve UserRole and UserStatus from the form data
        $userRole = $_POST['userRole'];
        $userStatus = $_POST['userStatus'];

        try {
            // Update the user's UserRole and UserStatus in the database
            $updateSql = "UPDATE [User] SET UserRole = ?, UserStatus = ? WHERE UserID = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bindParam(1, $userRole, PDO::PARAM_STR);
            $updateStmt->bindParam(2, $userStatus, PDO::PARAM_STR);
            $updateStmt->bindParam(3, $userID, PDO::PARAM_INT);
            $updateStmt->execute();

            // Check if the update was successful
            if ($updateStmt->rowCount() > 0) {
                // Create a new connection using the company name
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
                // Update user status in the new connection
                $sql = "UPDATE [User] SET Status = ? WHERE UserID = ?";
                $stmt = $connn->prepare($sql);
                $stmt->bindParam(1, $userStatus, PDO::PARAM_STR);
                $stmt->bindParam(2, $userData['Username'], PDO::PARAM_STR);

                // Check if the second update was successful
                if ($stmt->execute()) {
                    // Redirect back to the previous page
                    $_SESSION['userrole'] = $userRole;
                    header('Location: settings-user.php');
                    exit();
                } else {
                    echo "Error updating user status: " . $stmt->errorInfo()[2];
                }
            } else {
                echo "Error updating user details: " . $updateStmt->errorInfo()[2];
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}

// Close the database connection
$conn = null;
?>

<div class="main-content">
    <?php
    $pathtitle = "Settings/User-edit";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="userID">User ID:</label>
                    <input type="text" id="userID" name="userID" value="<?= $userData['UserID'] ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?= $userData['Username'] ?>" readonly>
                </div>

                <div class="form-group">
                    <label for="userRole">User Role:</label>
                    <select id="userRole" name="userRole">
                        <option value="User" <?= ($userData['UserRole'] == 'User') ? 'selected' : '' ?>>User</option>
                        <option value="Manager" <?= ($userData['UserRole'] == 'Manager') ? 'selected' : '' ?>>Manager
                        </option>
                        <option value="Admin" <?= ($userData['UserRole'] == 'Admin') ? 'selected' : '' ?>>Admin</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="userStatus">User Status:</label>
                    <select id="userStatus" name="userStatus">
                        <option value="Active" <?= ($userData['UserStatus'] == 'Active') ? 'selected' : '' ?>>Active
                        </option>
                        <option value="Disable" <?= ($userData['UserStatus'] == 'Disable') ? 'selected' : '' ?>>
                            Disable</option>
                    </select>
                </div>

                <div class="form-group">
                    <input type="hidden" name="userID" value="<?= $userData['UserID'] ?>">
                    <button type="submit">Update</button>
                    <button type="button" class="cancel"
                        onclick="window.location.href='settings-user.php'">Cancel</button>
                </div>
            </form>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>