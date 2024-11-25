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

// Retrieve patient information from the database
$patient_id = $_SESSION['user_id'];
$sql_patient = "SELECT patient_id, first_name, last_name FROM patients WHERE patient_id = ?";
$stmt_patient = $connection->prepare($sql_patient);
$stmt_patient->bind_param('i', $patient_id);
$stmt_patient->execute();
$result_patient = $stmt_patient->get_result();
$patient = $result_patient->fetch_assoc();

if (!$patient) {
    echo "Error: Patient not found.";
    exit;
}

// Handle search functionality
$search_results = [];
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['search'])) {
    $search_term = '%' . $_POST['search'] . '%'; // Allow partial matching
    $sql_search = "SELECT doctor_id, first_name, last_name, speciality, email FROM doctors WHERE speciality LIKE ?";
    $stmt_search = $connection->prepare($sql_search);
    $stmt_search->bind_param('s', $search_term);
    $stmt_search->execute();
    $result_search = $stmt_search->get_result();

    if ($result_search->num_rows > 0) {
        while ($row = $result_search->fetch_assoc()) {
            $search_results[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Patient Dashboard - Afya Bora</title>
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
        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-bottom: 20px;
        }
        .button {
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
        .button:hover {
            background-color: #e65c00;
        }
        .search-bar {
            margin-top: 20px;
            display: flex;
            width: 100%;
        }
        .search-bar input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
        }
        .search-bar button {
            padding: 10px 20px;
            border: none;
            background: #FF6600;
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
            font-size: 16px;
        }
        .search-bar button:hover {
            background: #e65c00;
        }
        .search-results {
            margin-top: 20px;
        }
        .search-results ul {
            list-style-type: none;
            padding: 0;
        }
        .search-results li {
            margin-bottom: 10px;
            background: #f9f9f9;
            padding: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
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
    <div class="main">
        <div class="button-container">
            <a href="activechats.php" class="button">View Existing Chats</a>
            <a href="appointments.php" class="button">Appointments</a>
            <a href="medicalrecords.php" class="button">View Medical Records</a>
        </div>
        <div class="profile-container">
            <h1>Patient Profile</h1>
            <div class="field">
                <label>Patient ID:</label>
                <p><?php echo htmlspecialchars($patient['patient_id']); ?></p>
            </div>
            <div class="field">
                <label>First Name:</label>
                <p><?php echo htmlspecialchars($patient['first_name']); ?></p>
            </div>
            <div class="field">
                <label>Last Name:</label>
                <p><?php echo htmlspecialchars($patient['last_name']); ?></p>
            </div>
            <div class="search-bar">
                <form method="POST" action="">
                    <input type="text" name="search" placeholder="Search for doctors by speciality..." required>
                    <button type="submit">Search</button>
                </form>
            </div>
            <?php if (!empty($search_results)): ?>
                <div class="search-results">
                    <h2>Search Results</h2>
                    <ul>
                    <?php foreach ($search_results as $result): ?>
                        <li>
                        <strong>Doctor ID:</strong> <?php echo htmlspecialchars($result['doctor_id']); ?><br>
                        <strong>Name:</strong> <?php echo htmlspecialchars($result['first_name'] . ' ' . $result['last_name']); ?><br>
                        <strong>Speciality:</strong> <?php echo htmlspecialchars($result['speciality']); ?><br>
                        <strong>Email:</strong> <?php echo htmlspecialchars($result['email']); ?><br>
                        <a href="chat.php?doctor_id=<?php echo $result['doctor_id']; ?>">Chat</a>
                        <a href="bookappointment.php?doctor_id=<?php echo $result['doctor_id']; ?>">Book Appointment</a>
                        </li>
                    <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
