<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forget Password</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            margin: 0;
            padding: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }

        .container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 16px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .back-button {
            display: block;
            margin-top: 20px;
            text-align: center;
        }
    </style>
</head>
<body>
<div class="container">
        <h2>Forget Password</h2>
        <form method="POST" action="send_email.php">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
            <label for="companyName">Company Name:</label>
            <input type="text" id="companyName" name="companyName" required>
            <?php
                    // Check if an error message is set
                    if (isset($_SESSION['error_message'])) {
                      echo '<label>Invalid Input:</label>';
                      echo '<p style="color: red;">' . $_SESSION['error_message'] . '</p>';
                      // Unset the session variable to clear the error message
                      unset($_SESSION['error_message']);
                    }
                    ?>
            <input type="submit" value="Submit">
        </form>
        <a href="login.php" class="back-button">Back</a>
    </div>
</body>
</html>
           
