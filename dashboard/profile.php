<?php
ob_start(); // Start output buffering
$pageTitle = "Profile";
include '../database/database-connect.php';
include '../contain/header.php';

// Fetch user data based on the username
$userDataQuery = $conn->prepare("SELECT * FROM [User] WHERE Username = :username");
$userDataQuery->bindParam(':username', $username);
$userDataQuery->execute();
$userData = $userDataQuery->fetch(PDO::FETCH_ASSOC);

// Update user data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userID = $_POST['userID'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];

    $updateUserSql = "UPDATE [User] SET Email = :email, Phone = :phone, FirstName = :firstName, LastName = :lastName WHERE UserID = :userID";
    $stmtUpdateUser = $conn->prepare($updateUserSql);
    $stmtUpdateUser->bindParam(':email', $email);
    $stmtUpdateUser->bindParam(':phone', $phone);
    $stmtUpdateUser->bindParam(':firstName', $firstName);
    $stmtUpdateUser->bindParam(':lastName', $lastName);
    $stmtUpdateUser->bindParam(':userID', $userID);

    if ($stmtUpdateUser->execute()) {
        // Redirect back to the previous page or perform any other action
        header('Location: ../index.php');
        exit;
    } else {
        echo "Error updating user details: " . $stmtUpdateUser->errorInfo()[2];
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
                    <label for="userID">Username:</label>
                    <input type="text" id="userID" name="userID" value="<?= htmlspecialchars($userData['Username']) ?>"
                        readonly>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" value="<?= htmlspecialchars($userData['Email']) ?>"
                        placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" value="<?= htmlspecialchars($userData['Phone']) ?>"
                        placeholder="Phone" required>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName"
                        value="<?= htmlspecialchars($userData['FirstName']) ?>" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" value="<?= htmlspecialchars($userData['LastName']) ?>"
                        placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <input type="hidden" name="userID" value="<?= htmlspecialchars($userData['UserID']) ?>">
                    <button type="submit">Update</button>
                    <button type="button" class="cancel"
                        onclick="window.location.href='../index.php'">Cancel</button>
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