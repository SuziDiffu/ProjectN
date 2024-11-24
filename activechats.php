<?php 
require_once 'database/db.php'; // Ensure this path points to your database connection file
use Database\Database;
session_start();

// Check if the user is logged in and is a patient
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header("Location: login.php");
    exit;
} 

$patient_id = $_SESSION['user_id'];

// Instantiate the Database class
$db = new Database();
$connection = $db->connection;

// Retrieve active chats for the logged-in patient
$sql_chats = "
    SELECT 
        doctors.doctor_id, 
        doctors.first_name, 
        doctors.last_name, 
        doctors.speciality
    FROM messages 
    JOIN doctors ON messages.doctor_id = doctors.doctor_id 
    WHERE messages.patient_id = ?
    GROUP BY doctors.doctor_id";
$stmt_chats = $connection->prepare($sql_chats);
$stmt_chats->bind_param('i', $patient_id);
$stmt_chats->execute();
$result_chats = $stmt_chats->get_result();

if ($result_chats->num_rows === 0) {
    $message = "You have no active chats.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Active Chats - Afya Bora</title>
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
        .chat-card {
            width: 100%;
            max-width: 600px;
            background: white;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .chat-card h3 {
            margin: 0;
            font-size: 18px;
            color: #003300;
        }
        .chat-card p {
            margin: 5px 0;
            font-size: 16px;
        }
        .chat-card a {
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
            <a href="patients.php">Back</a>
            <a href="login.php">Logout</a>
        </div>
    </div>
    <div class="main">
        <h1>Your Active Chats</h1>
        <?php if (isset($message)) : ?>
            <p><?php echo htmlspecialchars($message); ?></p>
        <?php else : ?>
            <?php while ($row = $result_chats->fetch_assoc()) : ?>
                <div class="chat-card">
                    <h3>Doctor: <?php echo htmlspecialchars($row['first_name'] . ' ' . $row['last_name']); ?></h3>
                    <p>Specialty: <?php echo htmlspecialchars($row['speciality']); ?></p>
                    <a href="chat.php?doctor_id=<?php echo htmlspecialchars($row['doctor_id']); ?>">Chat</a>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
