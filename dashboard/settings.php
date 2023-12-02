<?php
ob_start(); // Start output buffering
$pageTitle = "Settings";
include("../database/database-connect.php");
include '../contain/header.php';
?>

<div class="main-content">

    <?php
    $pathtitle = "Settings";
    include '../contain/horizontal-bar.php';
    ?>

    <?php if ($userrole == 'User'): ?>
        <br><br><br>
        <div class="button-and-search">
            <h3>Sorry, user cannot access this page.</h3>
        </div>
    <?php endif; ?>

    <?php if ($userrole !== 'User'): ?>

        <main>
            <ul class="settings-container">
                <li class="settings-item">
                    <a class="settings-link" href="settings-category.php">
                        <div class="settings-header">Inventory Category</div>
                        <span class="settings-icon">&#9656;</span>
                    </a>
                </li>
                <?php if ($userrole !== 'Manager'): ?>
                    <li class="settings-item">
                        <a class="settings-link" href="settings-user.php">
                            <div class="settings-header">User</div>
                            <span class="settings-icon">&#9656;</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </main>
    <?php endif; ?>

</div>

<?php
ob_end_flush(); // Flush the output buffer and turn off output buffering
?>

</body>

</html>