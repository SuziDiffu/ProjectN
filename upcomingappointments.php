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

// Retrieve doctor ID from the session
$doctor_id = $_SESSION['user_id'];

// Fetch upcoming appointments for the logged-in doctor
$sql_appointments = "
    SELECT 
        a.appointment_id, 
        a.patient_id, 
        a.appointment_date, 
        a.appointment_time,
        p.first_name, 
        p.last_name, 
        p.email, 
        p.phone_number 
    FROM 
        appointments a 
    INNER JOIN 
        patients p 
    ON 
        a.patient_id = p.patient_id 
    WHERE 
        a.doctor_id = ? 
    ORDER BY 
        a.appointment_date ASC, a.appointment_time ASC";
        
$stmt_appointments = $connection->prepare($sql_appointments);
$stmt_appointments->bind_param('i', $doctor_id);
$stmt_appointments->execute();
$result_appointments = $stmt_appointments->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upcoming Appointments - Afya Bora</title>
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
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            width: 100%;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        table th {
            background-color: #003300;
            color: white;
            font-weight: bold;
        }
        .expired {
            color: red;
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
        <a href="doctors.php">Back</a>
        <a href="login.php">Logout</a>
    </div>
    <div class="main">
        <h1>Upcoming Appointments</h1>
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Appointment ID</th>
                        <th>Patient Name</th>
                        <th>Email</th>
                        <th>Phone Number</th>
                        <th>Appointment Date</th>
                        <th>Appointment Time</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($appointment = $result_appointments->fetch_assoc()): 
                        $current_datetime = new DateTime();
                        $appointment_datetime = new DateTime($appointment['appointment_date'] . ' ' . $appointment['appointment_time']);
                        $status = ($appointment_datetime < $current_datetime) ? 'Expired' : 'Upcoming';
                    ?>
                        <tr>
                            <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['phone_number']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                            <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                            <td class="<?php echo $status === 'Expired' ? 'expired' : ''; ?>">
                                <?php echo htmlspecialchars($status); ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                    <?php if ($result_appointments->num_rows === 0): ?>
                        <tr>
                            <td colspan="7" style="text-align: center;">No appointments found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
