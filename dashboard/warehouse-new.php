<?php
ob_start(); // Start output buffering
$pageTitle = "Warehouse/Create";
include '../database/database-connect.php';
include '../contain/header.php';

$errors = [];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process form data
    $warehouseName = $_POST["warehouseName"];
    $address = $_POST["address"];
    $contact = $_POST["contact"];
    $email = $_POST["email"];

    // Validate warehouse name
    if (empty($warehouseName)) {
        $errors[] = "Warehouse name is required.";
    } elseif (strlen($warehouseName) > 255) {
        $errors[] = "Warehouse name must be 255 characters or less.";
    }

    // Validate address
    if (strlen($address) > 255) {
        $errors[] = "Address must be 255 characters or less.";
    }

    // Validate contact
    if (strlen($contact) > 20) {
        $errors[] = "Contact must be 20 characters or less.";
    }

    // Validate email
    if (empty($email)) {
        $errors[] = "Email is required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    } elseif (strlen($email) > 255) {
        $errors[] = "Email must be 255 characters or less.";
    }

    // If there are validation errors, display them and stop further processing
    if (!empty($errors)) {
        foreach ($errors as $error) {
            echo "<p class='error'>$error</p>";
        }
    } else {
        // Perform database insertion using PDO
        $insertSql = "INSERT INTO Warehouse (Name, Address, Contact, Email) VALUES (:name, :address, :contact, :email)";
        $stmt = $conn->prepare($insertSql);
        $stmt->bindParam(':name', $warehouseName);
        $stmt->bindParam(':address', $address);
        $stmt->bindParam(':contact', $contact);
        $stmt->bindParam(':email', $email);

        if ($stmt->execute()) {
            header("Location: warehouse.php");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }
    }
}
?>

<div class="main-content">
    <?php
    $pathtitle = "Warehouse/Create";
    include '../contain/horizontal-bar.php';
    ?>
    <main>
        <?php if (!empty($errors)): ?>
            <div class="error-container">
                <p class="error">Please fix the following errors:</p>
                <ul>
                    <?php foreach ($errors as $error): ?>
                        <li>
                            <?= htmlspecialchars($error) ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        <?php endif; ?>

        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <label for="warehouseName">Warehouse name:</label>
                    <input type="text" id="warehouseName" name="warehouseName"
                        placeholder="Please enter a warehouse name" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Please enter the address" required
                        maxlength="255">
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" placeholder="Please enter the contact information"
                        required maxlength="20">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Please enter the email" required
                        maxlength="255">
                </div>
                <div class="form-group">
                    <button type="submit">Create</button>
                    <button type="button" class="cancel" onclick="window.location.href='warehouse.php'">Cancel</button>
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