<?php
ob_start(); // Start output buffering
$pageTitle = "Customer/New";
include '../database/database-connect.php';
include '../contain/header.php';

// Initialize errors array
$errors = [];

function validateCustomerData($customerName, $contact, $email, $address, $remark, &$errors)
{
    // Validate customer name
    if (empty($customerName)) {
        $errors[] = "Customer name is required.";
    } elseif (strlen($customerName) > 255) {
        $errors[] = "Customer name must be 255 characters or less.";
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

    // Validate address
    if (strlen($address) > 255) {
        $errors[] = "Address must be 255 characters or less.";
    }

    // Validate remark
    if (strlen($remark) > 255) {
        $errors[] = "Remark must be 255 characters or less.";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Process form data
    $customerName = trim($_POST["customerName"]);
    $contact = trim($_POST["contact"]);
    $email = trim($_POST["email"]);
    $address = trim($_POST["address"]);
    $remark = trim($_POST["remark"]); // Added for the new remark column

    // Additional validation checks
    validateCustomerData($customerName, $contact, $email, $address, $remark, $errors);

    // If there are validation errors, display them and stop further processing
    if (!empty($errors)) {
        displayErrors($errors);
    } else {
        // Perform database insertion using prepared statement
        $sql = "INSERT INTO Customer (Name, Contact, Email, Address, Remark) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(1, $customerName, PDO::PARAM_STR);
        $stmt->bindParam(2, $contact, PDO::PARAM_STR);
        $stmt->bindParam(3, $email, PDO::PARAM_STR);
        $stmt->bindParam(4, $address, PDO::PARAM_STR);
        $stmt->bindParam(5, $remark, PDO::PARAM_STR);

        if ($stmt->execute()) {
            header("Location: customer.php");
            exit();
        } else {
            echo "Error: " . $stmt->errorInfo()[2];
        }

        $stmt->closeCursor();
    }
}

?>

<div class="main-content">
    <?php
    $pathtitle = "Customer/New";
    include '../contain/horizontal-bar.php';
    ?>
    <main>
        <div class="form-container">
            <form action="" method="post">
                <div class="form-group">
                    <?php
                    if ($_SERVER["REQUEST_METHOD"] === "POST" && !empty($errors)) {
                        displayErrors($errors);
                    } else {
                        echo '<div class="error-container" style="display:none;"></div>';
                    }
                    ?>
                </div>
                <div class="form-group">
                    <label for="customerName">Customer Name:</label>
                    <input type="text" id="customerName" name="customerName" placeholder="Customer name"
                        required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="contact">Contact:</label>
                    <input type="text" id="contact" name="contact" placeholder="Contact information"
                        required maxlength="20">
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Email" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <input type="text" id="address" name="address" placeholder="Address" required maxlength="255">
                </div>
                <div class="form-group">
                    <label for="remark">Remark:</label>
                    <input type="text" id="remark" name="remark" placeholder="Remark" required maxlength="255">
                </div>
                <div class="form-group">
                    <button type="submit">Add</button>
                    <button type="button" class="cancel" onclick="window.location.href='customer.php'">Cancel</button>
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