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

$doctor_id = $_SESSION['user_id'];

// Retrieve messages for the logged-in doctor
$sql_messages = "
    SELECT 
        patients.patient_id, 
        patients.first_name, 
        patients.last_name, 
        patients.gender
    FROM messages 
    JOIN patients ON messages.patient_id = patients.patient_id 
    WHERE messages.doctor_id = ?
    GROUP BY patients.patient_id";
$stmt_messages = $connection->prepare($sql_messages);
$stmt_messages->bind_param('i', $doctor_id);
$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();

if ($result_messages->num_rows === 0) {
    $message = "No messages from patients.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Messages - Afya Bora</title>
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
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }
        .patient-card {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .patient-card h3 {
            margin: 0;
            font-size: 18px;
            color: #003300;
        }
        .patient-card p {
            margin: 5px 0;
            font-size: 16px;
        }
        .patient-card a {
            color: #FF6600;
            text-decoration: none;
            font-weight: bold;
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
        <div>
            <a href="doctors.php">Back</a>
            <a href="login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <h1>Messages from Patients</h1>
        <?php if (isset($message)) : ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php else : ?>
            <?php while ($row = $result_messages->fetch_assoc()) : ?>
                <div class="patient-card">
                    <h3>Patient ID: <?php echo htmlspecialchars($row['patient_id']); ?></h3>
                    <p>Name: <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></p>
                    <p>Gender: <?php echo htmlspecialchars($row['gender']); ?></p>
                    <a href="chat.php?patient_id=<?php echo htmlspecialchars($row['patient_id']); ?>">Chat</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
