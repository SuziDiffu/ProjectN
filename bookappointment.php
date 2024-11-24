<?php
require_once 'database/db.php'; // Ensure this path points to your database connection file
use Database\Database;
session_start();

// Instantiate the Database class
$db = new Database();
$connection = $db->connection;

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

// Retrieve the doctor_id from the query string
if (!isset($_GET['doctor_id'])) {
    echo "Error: Doctor not specified.";
    exit;
}
$doctor_id = intval($_GET['doctor_id']);

// Retrieve doctor details
$sql_doctor = "SELECT first_name, last_name, speciality FROM doctors WHERE doctor_id = ?";
$stmt_doctor = $connection->prepare($sql_doctor);
$stmt_doctor->bind_param('i', $doctor_id);
$stmt_doctor->execute();
$result_doctor = $stmt_doctor->get_result();
$doctor = $result_doctor->fetch_assoc();

if (!$doctor) {
    echo "Error: Doctor not found.";
    exit;
}

// Handle appointment booking
$message = '';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $patient_id = $_SESSION['user_id'];

    // Insert the appointment into the database
    $sql_appointment = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time) VALUES (?, ?, ?, ?)";
    $stmt_appointment = $connection->prepare($sql_appointment);
    $stmt_appointment->bind_param('iiss', $patient_id, $doctor_id, $appointment_date, $appointment_time);

    if ($stmt_appointment->execute()) {
        $message = "Appointment successfully booked with Dr. " . htmlspecialchars($doctor['first_name']) . " " . htmlspecialchars($doctor['last_name']) . ".";
    } else {
        $message = "Error: Could not book the appointment. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Appointment - Afya Bora</title>
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
        form {
            margin-top: 20px;
        }
        form label {
            display: block;
            font-weight: bold;
            margin-bottom: 5px;
        }
        form input, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        form button {
            background: #FF6600;
            color: white;
            font-weight: bold;
            cursor: pointer;
            border: none;
        }
        form button:hover {
            background: #e65c00;
        }
        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 5px;
            color: white;
            background: #003300;
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
        <a href="patients.php">Back</a>
        <a href="login.php">Logout</a>
    </div>
    <div class="main">
        <div class="profile-container">
            <h1>Book Appointment</h1>
            <div class="field">
                <label>Doctor Name:</label>
                <p><?php echo htmlspecialchars($doctor['first_name']) . ' ' . htmlspecialchars($doctor['last_name']); ?></p>
            </div>
            <div class="field">
                <label>Speciality:</label>
                <p><?php echo htmlspecialchars($doctor['speciality']); ?></p>
            </div>
            <?php if ($message): ?>
                <div class="message"><?php echo $message; ?></div>
            <?php endif; ?>
            <form method="POST" action="">
                <label for="appointment_date">Appointment Date:</label>
                <input type="date" name="appointment_date" id="appointment_date" required>
                <label for="appointment_time">Appointment Time:</label>
                <input type="time" name="appointment_time" id="appointment_time" required>
                <button type="submit">Book Appointment</button>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
