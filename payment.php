<!-- $successCardNumber = "4111111111111111";
$successExpiryMonth = "12";
$successExpiryYear = "2024";
$successCVV = "123";
$successCardholder = "John Doe"; -->

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Secure Payment</title>
  <style>
    body {
      font-family: 'Arial', sans-serif;
      margin: 0;
      padding: 0;
      background-color: #f9f9f9;
      color: #333;
    }

    header {
      background-color: #4caf50;
      padding: 10px;
      text-align: center;
      color: white;
    }

    main {
      max-width: 600px;
      margin: 20px auto;
      padding: 20px;
      background-color: #fff;
      border: 1px solid #ddd;
      border-radius: 5px;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }

    form {
      display: grid;
      gap: 15px;
    }

    label {
      font-weight: bold;
      display: block;
    }

    input,
    select {
      width: 100%;
      padding: 10px;
      box-sizing: border-box;
      border: 1px solid #ccc;
      border-radius: 5px;
    }

    .expiry-cvv-container {
      display: grid;
      gap: 15px;
      grid-template-columns: 1fr 1fr 1fr;
    }

    #pay-now {
      background-color: #4caf50;
      color: white;
      padding: 15px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    .back-button {
      background-color: #333;
      color: white;
      padding: 10px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
    }

    #loading-spinner {
      /* Add styles for the loading spinner */
    }

    footer {
      margin-top: 20px;
      text-align: center;
      color: #777;
    }

    footer a {
      margin: 0 10px;
      text-decoration: none;
      color: #333;
    }

    /* Add styles for error messages if any */
    .error-message {
      color: red;
      font-weight: bold;
    }

    .success-message {
      color: green;
      font-weight: bold;
    }
  </style>
</head>

<body>
  <header>
    <h1>Secure Payment</h1>
  </header>
  <br><br><br><br>
  <main>
    <?php

    function validateCard($cardNumber, $expiryMonth, $expiryYear, $cvv, $cardholder)
    {
      // Remove spaces and non-numeric characters from the card number
      $cardNumber = preg_replace('/\D/', '', $cardNumber);

      // Validate card number using Luhn's algorithm
      if (!luhnCheck($cardNumber)) {
        return false;
      }

      // Check if the card has not expired
      $currentYear = date("Y");
      $currentMonth = date("n");
      if ($expiryYear < $currentYear || ($expiryYear == $currentYear && $expiryMonth < $currentMonth)) {
        return false; // Card has expired
      }

      // Perform additional checks if needed (e.g., cardholder name, CVV length, etc.)
      // Example checks:
      if (strlen($cvv) !== 3) {
        return false; // CVV should be 3 digits
      }

      // Add more checks as required
    
      // If all checks pass, the card is considered valid
      return true;
    }

    function luhnCheck($number)
    {
      $number = strrev(preg_replace('/[^\d]/', '', $number));
      $sum = 0;

      for ($i = 0, $j = strlen($number); $i < $j; $i++) {
        $digit = (int) $number[$i];

        if ($i % 2 == 1) {
          $digit *= 2;

          if ($digit > 9) {
            $digit -= 9;
          }
        }

        $sum += $digit;
      }

      return $sum % 10 == 0;
    }
    // Basic card validation
    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $conn = new PDO(
        "sqlsrv:server = tcp:allhereserver.database.windows.net,1433; Database = allheredb",
        "sqladmin",
        "#Allhere",
        array(PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION)
      );

      $companyName = "";
      $sql = "SELECT AuthCode FROM company WHERE CompanyName = :companyName";
      $stmt = $conn->prepare($sql);
      $stmt->execute([':companyName' => $companyName]);
      $row = $stmt->fetch(PDO::FETCH_ASSOC);
      if ($row) {
        $authcode = $row["AuthCode"];
      } else {
        echo "Auth Code is not available.";
        echo "<button class='back-button'><a href='register.php' style='text-decoration: none; color: white;'>Back</a></button>";
        exit;
      }


      // Close the connection (optional as PDO closes the connection automatically when the script ends)
      $conn = null;


      $cardNumber = $_POST["card-number"];
      $expiryMonth = $_POST["expiry-month"];  // Fix array key
      $expiryYear = $_POST["expiry-year"];
      $cvv = $_POST["cvv"];
      $cardholder = $_POST["cardholder"];


      // Perform basic card validation
      $isValid = validateCard($cardNumber, $expiryMonth, $expiryYear, $cvv, $cardholder);

      if ($isValid) {
        // Assuming you are using MySQL
    

        echo '<p class="success-message">Payment successful! ';
        echo $authcode;
        echo ' is your Auth Code! Remember to save this code for Register!</p>';
      } else {
        echo '<p class="error-message">Invalid card details. Please check and try again.</p>';
      }
    }


    ?>

    <h2>Payment Details</h2>
    <p>Total : RM15/month</p>
    <form id="payment-form" method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
      <label for="card-number">Card Number:</label>
      <input type="text" id="card-number" name="card-number" placeholder="Your Card Numbers" required>

      <div class="expiry-cvv-container">
        <div>
          <label for="expiry-month">Expiry Date:</label>
          <select id="expiry-month" name="expiry-month" required>
            <option value="">-- Month --</option>
            <?php
            for ($i = 1; $i <= 12; $i++) {
              echo "<option value='" . sprintf("%02d", $i) . "'>" . date("F", mktime(0, 0, 0, $i, 1)) . "</option>";
            }
            ?>
          </select>
        </div>
        <div>
          <label for="expiry-year">Year:</label>
          <select id="expiry-year" name="expiry-year" required>
            <option value="">-- Year --</option>
            <?php
            $currentYear = date("Y");
            for ($i = $currentYear; $i <= $currentYear + 10; $i++) {
              echo "<option value='$i'>$i</option>";
            }
            ?>
          </select>
        </div>
        <div>
          <label for="cvv">CVV:</label>
          <input type="text" id="cvv" name="cvv" placeholder="Your Card CVV" required>
        </div>
      </div>

      <label for="cardholder">Cardholder Name:</label>
      <input type="text" id="cardholder" name="cardholder" placeholder="Your Name" required>

      <button type="submit" id="pay-now">Pay Now</button>
      <a href="register.php" style="text-decoration: none; color: black; text-align:center;">Back</a>
    </form>


    <div id="loading-spinner" class="hidden"></div>
    <!-- Add error message display area -->
  </main>
  <br><br><br>
  <footer>
    <p>&copy; 2023 All Here. All rights reserved.</p>
  </footer>
</body>

</html>