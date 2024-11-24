<?php
// Include database configuration file
require_once 'database/db.php';
use Database\Database;

// Start a session
session_start();

// Instantiate the Database class and get the connection
$db = new Database();
$connection = $db->connection;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $email = $_POST['email'];
    $county = $_POST['county'];
    $gender = $_POST['gender'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    // Password confirmation validation
    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match. Please try again.');</script>";
    } else {
        // Check if the email already exists
        $sql_check_email = "SELECT * FROM patients WHERE email = ?";
        $stmt_check_email = $connection->prepare($sql_check_email);
        $stmt_check_email->bind_param('s', $email);
        $stmt_check_email->execute();
        $result_check_email = $stmt_check_email->get_result();

        if ($result_check_email->num_rows > 0) {
            echo "<script>alert('This email is already in use. Please choose a different email.');</script>";
        } else {
            // Hash the password before saving it to the database
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // Insert data into the `patients` table
            $sql = "INSERT INTO patients (first_name, last_name, email, county, gender, phone_number, password) VALUES (?, ?, ?, ?, ?, ?, ?)";
            $stmt = $connection->prepare($sql);
            $stmt->bind_param('sssssss', $first_name, $last_name, $email, $county, $gender, $phone_number, $hashed_password);

            if ($stmt->execute()) {
                // Redirect to login page on success
                echo "<script>alert('Registration successful! Redirecting to login page...');
                      window.location.href = 'login.php';</script>";
            } else {
                echo "<p style='color:red;'>Error: " . $stmt->error . "</p>";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Registration - Afya Bora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }
        .navbar {
            width: 100%;
            background-color: #003300;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .navbar .nav-items {
            display: flex;
            align-items: center;
        }
        .navbar a {
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .navbar a:hover {
            background-color: #005500;
        }
        .main {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
        }
        .form-container {
            background-color: #f9f9f9;
            padding: 40px;
            width: 80%;
            max-width: 600px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h1 {
            margin-bottom: 20px;
            font-size: 2em;
            color: #003300;
            text-align: center;
        }
        .form-container label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }
        .form-container input,
        .form-container select {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-container .button {
            padding: 15px 25px;
            font-size: 16px;
            color: white;
            background-color: #FF6600;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
            display: block;
            width: 100%;
            text-align: center;
        }
        .form-container .button:hover {
            background-color: #CC5200;
        }
        footer {
            background-color: #003300;
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
            box-shadow: 0 -2px 4px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="navbar">
        <img src="logo.png" alt="Afya Bora Logo" style="height: 50px;">
        <div class="nav-items">
            <a href="index.php">Home</a>
            <a href="login.php">Login</a>
        </div>
    </div>
    <div class="main">
        <div class="form-container">
            <h1>Patient Registration</h1>
            <p>Please fill out the form below to register as a patient with Afya Bora.</p>
            <form method="POST" action="">
                <label for="first_name">First Name</label>
                <input type="text" id="first_name" name="first_name" required>

                <label for="last_name">Last Name</label>
                <input type="text" id="last_name" name="last_name" required>

                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>

                <label for="county">County</label>
                <select id="county" name="county" required>
                    <option value="">Select County</option>
                    <option value="Nairobi">Nairobi</option>
                    <option value="Mombasa">Mombasa</option>
                    <option value="Kiambu">Kiambu</option>
                    <option value="Machakos">Machakos</option>
                    <option value="Kisumu">Kisumu</option>
                    <!-- Add other counties here -->
                </select>

                <label for="gender">Gender</label>
                <select id="gender" name="gender" required>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select>

                <label for="phone_number">Phone Number</label>
                <input type="text" id="phone_number" name="phone_number" required>

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>

                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required>

                <button type="submit" class="button">Register</button>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
