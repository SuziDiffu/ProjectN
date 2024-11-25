<?php
require_once 'database/db.php'; // Include database connection
use Database\Database;
session_start();

// Instantiate Database
$db = new Database();
$connection = $db->connection;

// Verify patient login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'patient') {
    header("Location: login.php");
    exit;
}

$patient_id = $_SESSION['user_id'];

// Fetch medical records for the patient
$sql_medical_records = "
    SELECT 
        mr.record_id, 
        CONCAT(d.first_name, ' ', d.last_name) AS doctor_name, 
        d.speciality, 
        mr.session_description, 
        mr.meds_prescribed, 
        mr.prescription, 
        mr.created_at 
    FROM medical_records mr
    INNER JOIN doctors d ON mr.doctor_id = d.doctor_id
    WHERE mr.patient_id = ?
    ORDER BY mr.created_at DESC";
$stmt_records = $connection->prepare($sql_medical_records);
$stmt_records->bind_param('i', $patient_id);
$stmt_records->execute();
$result_records = $stmt_records->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Medical Records - Afya Bora</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('background2.jpg') no-repeat center center fixed;
            background-size: cover;
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
            padding: 20px;
        }
        .records-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .records-table th, .records-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .records-table th {
            background-color: #FF6600; /* Orange background */
            color: white; /* White text */
        }
        .records-table td {
            background-color: #f9f9f9;
        }
        footer {
            background-color: #003300;
            color: white;
            text-align: center;
            padding: 10px 20px;
        }
    </style>
</head>
<body>
    <div class="header">
        <img src="logo.png" alt="Afya Bora Logo">
        <a href="patients.php">Back</a>
        <a href="logout.php">Logout</a>
    </div>
    <div class="main">
        <h1>My Medical Records</h1>
        <?php if ($result_records->num_rows > 0): ?>
            <table class="records-table">
                <thead>
                    <tr>
                        <th>Record ID</th>
                        <th>Doctor Name</th>
                        <th>Specialty</th>
                        <th>Session Description</th>
                        <th>Medications Prescribed</th>
                        <th>Prescription</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($record = $result_records->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($record['record_id']); ?></td>
                            <td><?php echo htmlspecialchars($record['doctor_name']); ?></td>
                            <td><?php echo htmlspecialchars($record['speciality']); ?></td>
                            <td><?php echo htmlspecialchars($record['session_description']); ?></td>
                            <td><?php echo $record['meds_prescribed'] ? 'Yes' : 'No'; ?></td>
                            <td><?php echo htmlspecialchars($record['prescription']); ?></td>
                            <td><?php echo htmlspecialchars($record['created_at']); ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>No medical records found.</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
