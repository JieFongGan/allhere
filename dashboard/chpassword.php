<?php
ob_start(); // Start output buffering
$pageTitle = "Change Password";
include '../database/database-connect.php';
include '../contain/header.php';

// Fetch user data based on the username
$userDataQuery = $conn->prepare("SELECT * FROM [User] WHERE Username = :username");
$userDataQuery->bindParam(':username', $username, PDO::PARAM_STR);
$userDataQuery->execute();
$userData = $userDataQuery->fetch(PDO::FETCH_ASSOC);

// Define variables for error messages
$currentPasswordError = $newPasswordError = $retypePasswordError = '';

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currentPassword = $_POST['currentPassword'];
    $newPassword = $_POST['newPassword'];
    $retypePassword = $_POST['retypePassword'];

    if ($currentPassword === $userData['Password']) {
        // Check if the new password and retype password match
        if ($newPassword === $retypePassword) {
            try {
                // Update the user password in the database
                $updatePasswordSql = "UPDATE [User] SET Password = :newPassword WHERE Username = :username";
                $stmtUpdatePassword = $conn->prepare($updatePasswordSql);
                $stmtUpdatePassword->bindParam(':newPassword', $newPassword, PDO::PARAM_STR);
                $stmtUpdatePassword->bindParam(':username', $username, PDO::PARAM_STR);
                $stmtUpdatePassword->execute();

                // Password updated successfully
                echo "Password updated successfully.";
                header("Location: homepage.php");
                exit();
            } catch (PDOException $e) {
                // Handle the update failure
                echo "Failed to update the password: " . $e->getMessage();
            }
        } else {
            // Passwords do not match
            $retypePasswordError = "New password and retype password do not match.";
        }
    } else {
        // Current password is incorrect
        $currentPasswordError = "Current password is incorrect.";
    }
}
?>

<div class="main-content">

    <?php
    $pathtitle = "Profile";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="currentPassword">Current password:</label>
                    <input type="password" id="currentPassword" name="currentPassword" required>
                    <span class="error">
                        <?= $currentPasswordError ?>
                    </span>
                </div>
                <div class="form-group">
                    <label for="newPassword">New password:</label>
                    <input type="password" id="newPassword" name="newPassword" required>
                </div>
                <div class="form-group">
                    <label for="retypePassword">Retype new password:</label>
                    <input type="password" id="retypePassword" name="retypePassword" required>
                    <span class="error">
                        <?= $retypePasswordError ?>
                    </span>
                </div>
                <div class="form-group">
                    <input type="hidden" name="userID" value="<?= $userData['UserID'] ?>">
                    <button type="submit">Change Password</button>
                    <button type="button" class="cancel" onclick="window.location.href='../index.php'">Cancel</button>
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
