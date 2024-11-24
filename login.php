<?php
// Include database configuration file
require_once 'database/db.php';
use Database\Database;

// Start the session at the beginning of the file
session_start();

// Instantiate the Database class and get the connection
$db = new Database();
$connection = $db->connection;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Retrieve form input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Check the `patients` table
    $sql = "SELECT * FROM patients WHERE email = ?";
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $patient = $result->fetch_assoc();
        // Verify password
        if (password_verify($password, $patient['password'])) {
            // Set session variables for patient
            $_SESSION['user_id'] = $patient['patient_id']; // Ensure the column name matches the DB field
            $_SESSION['user_role'] = 'patient';
            
            // Redirect to the patient's page after successful login
            header('Location: patients.php');
            exit; // Stop further script execution
        } else {
            $error_message = 'Incorrect email or password.';
        }
    } else {
        // Check the `doctors` table
        $sql = "SELECT * FROM doctors WHERE email = ?";
        $stmt = $connection->prepare($sql);
        $stmt->bind_param('s', $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $doctor = $result->fetch_assoc();
            if ($password === $doctor['password']) {
                // Set session variables for doctor
                $_SESSION['user_id'] = $doctor['doctor_id']; // Ensure the column name matches the DB field
                $_SESSION['user_role'] = 'doctor';
                $_SESSION['user_name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
                
                // Redirect to the doctor's page after successful login
                header('Location: doctors.php');
                exit; // Stop further script execution
            } else {
                $error_message = 'Incorrect email or password.';
            }
        } else {
            // Check the `systemadmin` table for admin login
            $sql_admin = "SELECT * FROM systemadmin WHERE email = ?";
            $stmt_admin = $connection->prepare($sql_admin);
            $stmt_admin->bind_param('s', $email);
            $stmt_admin->execute();
            $result_admin = $stmt_admin->get_result();

            if ($result_admin->num_rows > 0) {
                $user = $result_admin->fetch_assoc();
                if ($password === $user['password']) {
                    // Set session variables for admin
                    $_SESSION['user_id'] = $user['admin_id']; // Ensure the column name matches the DB field
                    $_SESSION['user_role'] = 'admin';
                    
                    // Redirect to the admin page after successful login
                    header("Location:admin.php");
                    exit; // Stop further script execution
                } else {
                    $error_message = 'Incorrect email or password.';
                }
            } else {
                $error_message = 'Incorrect email or password.';
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
    <title>Login - Afya Bora</title>
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
            <a href="registration.php">Registration</a>
        </div>
    </div>
    <div class="main">
        <div class="form-container">
            <h1>Login</h1>
            <p>Welcome back! Please log in to your account.</p>
            <!-- Error Message -->
            <?php if (isset($error_message)) { echo "<p class='error'>$error_message</p>"; } ?>
            <!-- Login Form -->
            <form method="POST" action="">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Enter your email" required>
                <label for="password">Password</label>
                <input type="password" id="password" name="password" placeholder="Enter your password" required>
                <button type="submit" class="button">Login</button>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
