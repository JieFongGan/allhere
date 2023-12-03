<?php
ob_start(); // Start output buffering
$pageTitle = "Settings/Users";
include '../database/database-connect.php';
include '../contain/header.php';


try {
    // Fetch all users
    $sqlAllUsers = "SELECT * FROM [User]";
    $stmtAllUsers = $conn->prepare($sqlAllUsers);
    $stmtAllUsers->execute();
    $allUsers = $stmtAllUsers->fetchAll(PDO::FETCH_ASSOC);

    // Delete user logic
    if (isset($_POST['deleteUser'])) {
        $userIDToDelete = $_POST['deleteUser'];

        // Get the username of the user to be deleted
        $sqlGetUsername = "SELECT Username FROM [User] WHERE UserID = ?";
        $stmtGetUsername = $conn->prepare($sqlGetUsername);
        $stmtGetUsername->bindParam(1, $userIDToDelete, PDO::PARAM_INT);
        $stmtGetUsername->execute();
        $usernameToDelete = $stmtGetUsername->fetchColumn();

        try {
            $connn = new PDO("sqlsrv:server=$serverName;Database=adminallhere", $uid, $pwd);
            $connn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Log the error to a file for debugging purposes
            error_log("Connection failed: " . $e->getMessage(), 3, "error.log");
            // Display a user-friendly message
            echo "Connection failed. Please try again later.";
            exit();
        }
        

        // Use a prepared statement to prevent SQL injection
        $sqlDeleteOtherTable = "DELETE FROM [user] WHERE UserID = ?";
        $stmtDeleteOtherTable = $connn->prepare($sqlDeleteOtherTable);
        $stmtDeleteOtherTable->bindParam(1, $usernameToDelete, PDO::PARAM_STR);
        $stmtDeleteOtherTable->execute();

        // Check for errors
        if ($stmtDeleteOtherTable->errorCode() !== '00000') {
            $errorInfo = $stmtDeleteOtherTable->errorInfo();
            echo "Error deleting from other table: " . $errorInfo[2];
            exit();
        }

        // Perform the deletion in the main user table
        $sqlDeleteUser = "DELETE FROM [User] WHERE UserID = ?";
        $stmtDeleteUser = $conn->prepare($sqlDeleteUser);
        $stmtDeleteUser->bindParam(1, $userIDToDelete, PDO::PARAM_INT);
        $stmtDeleteUser->execute();

        // Check for errors
        if ($stmtDeleteUser->errorCode() !== '00000') {
            $errorInfo = $stmtDeleteUser->errorInfo();
            echo "Error deleting user: " . $errorInfo[2];
            exit();
        }

        // Redirect to the same page to refresh the user list
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
} catch (PDOException $e) {
    // Handle database errors
    echo "Error: " . $e->getMessage();
    exit();
}

$conn = null; // Close the main database connection
?>

<div class="main-content">
    <?php
    $pathtitle = "Settings/Users";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="button-and-search">
            <button name="createUser"><a href="settings-user-new.php"
                    style="text-decoration: none; color: white;">Create new user</a></button>
            <input type="text" id="searchInput" placeholder="Search on current list..." onkeyup="searchTable()">
        </div>

        <div class="table-responsive">
            <table id="myTable" class="table-container" style="width:100%">
                <thead>
                    <tr>
                        <th>UserID</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>FirstName</th>
                        <th>LastName</th>
                        <th>UserRole</th>
                        <th>LastLoginDate</th>
                        <th>UserStatus</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if (empty($allUsers)): ?>
                        <tr>
                            <td colspan="10">No data available.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($allUsers as $user): ?>
                            <tr>
                                <td><?= $user['UserID'] ?></td>
                                <td><?= $user['Username'] ?></td>
                                <td><?= $user['Email'] ?></td>
                                <td><?= $user['Phone'] ?></td>
                                <td><?= $user['FirstName'] ?></td>
                                <td><?= $user['LastName'] ?></td>
                                <td><?= $user['UserRole'] ?></td>
                                <td><?= $user['LastLoginDate'] ?></td>
                                <td><?= $user['UserStatus'] ?></td>
                                <td>
                                    <form method="GET" action="settings-user-edit.php">
                                        <input type="hidden" name="userID" value="<?= $user['UserID'] ?>">
                                        <button class="edit" type="submit">edit</button>
                                    </form>
                                    <form method="POST">
                                        <button class="delete" name="deleteUser" type="submit"
                                            onclick="return confirm('Are you sure you want to remove this user?')">remove
                                        </button>
                                        <input type="hidden" name="deleteUser" value="<?= $user['UserID'] ?>">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>
</html>