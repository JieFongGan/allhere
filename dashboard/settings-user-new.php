<?php
ob_start(); // Start output buffering
$pageTitle = "Create User";
include '../database/database-connect.php';
include '../contain/header.php';
?>

<div class="main-content">

    <?php
    $pathtitle = "Create User";
    include '../contain/horizontal-bar.php';
    ?>

    <main>
        <div class="form-container">
            <form action="settings-user-new-post.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Password" required>
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" id="email" name="email" placeholder="Email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone:</label>
                    <input type="text" id="phone" name="phone" placeholder="Phone" required>
                </div>
                <div class="form-group">
                    <label for="userrole">User Role:</label>
                    <select id="userrole" name="userrole">
                        <option value="User">User</option>
                        <option value="Manager">Manager</option>
                        <option value="Admin">Admin</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="firstName">First Name:</label>
                    <input type="text" id="firstName" name="firstName" placeholder="First Name" required>
                </div>
                <div class="form-group">
                    <label for="lastName">Last Name:</label>
                    <input type="text" id="lastName" name="lastName" placeholder="Last Name" required>
                </div>
                <div class="form-group">
                    <button type="submit">Create</button>
                    <button type="button" class="cancel" onclick="window.location.href='../index.php'">Cancel</button>
                </div>
            </form>

            <?php
            // Check if an error message is set
            if (isset($_SESSION['error_message'])) {

                echo '<label>Invalid Input:</label>';
                echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
                // Unset the session variable to clear the error message
                unset($_SESSION['error_message']);
            }
            ?>
        </div>
    </main>
</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>