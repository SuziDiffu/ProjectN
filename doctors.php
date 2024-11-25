<?php
require_once 'database/db.php'; // Ensure this path points to your database connection file
use Database\Database;
session_start();

// Instantiate the Database class
$db = new Database();
$connection = $db->connection;

// Check if the user is logged in and is a doctor
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

// Retrieve doctor information from the database
$doctor_id = $_SESSION['user_id'];
$sql_doctor = "SELECT doctor_id, first_name, last_name, speciality, email, phone_number FROM doctors WHERE doctor_id = ?";
$stmt_doctor = $connection->prepare($sql_doctor);
$stmt_doctor->bind_param('i', $doctor_id);
$stmt_doctor->execute();
$result_doctor = $stmt_doctor->get_result();
$doctor = $result_doctor->fetch_assoc();

if (!$doctor) {
    echo "Error: Doctor not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Doctor Profile - Afya Bora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background2.jpg') no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            color: #333;
        }
        .header {
            width: 100%;
            background: #003300;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: white;
        }
        .header img {
            height: 50px;
            width: auto;
        }
        .header a {
            color: white;
            text-decoration: none;
            font-size: 18px;
            padding: 10px 20px;
        }
        .header a:hover {
            background-color: #005500;
        }
        .main {
            flex: 1;
            display: flex;
            justify-content: center;
            padding: 20px;
        }
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px; /* Adds spacing between buttons */
        }
        .button-container a {
            display: block;
            background-color: #FF6600;
            color: white;
            text-decoration: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }
        .profile-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        .profile-container h1 {
            margin-bottom: 20px;
            color: #003300;
            text-align: center;
        }
        .field {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }
        .field label {
            font-weight: bold;
            color: #333;
        }
        .field p {
            margin: 0;
            padding: 10px;
            background: #f9f9f9;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        footer {
            background-color: #003300;
            color: white;
            text-align: center;
            padding: 10px 20px;
            width: 100%;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Afya Bora Logo">
        <a href="login.php">Logout</a>
    </div>
    <div class="main" style="display: flex; justify-content: center; align-items: flex-start; gap: 20px;">
        <div class="button-container">
            <a href="viewmessages.php">View Messages</a>
            <a href="upcomingappointments.php">Upcoming Appointments</a>
            <a href="updatepatientfile.php">Update Patient File</a>
        </div>
        <div class="profile-container">
            <h1>Doctor Profile</h1>
            <div class="field">
                <label>Doctor ID:</label>
                <p><?php echo htmlspecialchars($doctor['doctor_id']); ?></p>
            </div>
            <div class="field">
                <label>First Name:</label>
                <p><?php echo htmlspecialchars($doctor['first_name']); ?></p>
            </div>
            <div class="field">
                <label>Last Name:</label>
                <p><?php echo htmlspecialchars($doctor['last_name']); ?></p>
            </div>
            <div class="field">
                <label>Speciality:</label>
                <p><?php echo htmlspecialchars($doctor['speciality']); ?></p>
            </div>
            <div class="field">
                <label>Email:</label>
                <p><?php echo htmlspecialchars($doctor['email']); ?></p>
            </div>
            <div class="field">
                <label>Phone Number:</label>
                <p><?php echo htmlspecialchars($doctor['phone_number']); ?></p>
            </div>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
