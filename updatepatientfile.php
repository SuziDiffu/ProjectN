<?php
require_once 'database/db.php'; // Include database connection
use Database\Database;
session_start();

// Instantiate Database
$db = new Database();
$connection = $db->connection;

// Verify doctor login
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'doctor') {
    header("Location: login.php");
    exit;
}

$doctor_id = $_SESSION['user_id'];

// Fetch expired appointments for the doctor
$current_date_time = date("Y-m-d H:i:s");
$sql_expired_appointments = "
    SELECT a.appointment_id, a.patient_id, a.appointment_date, a.appointment_time, 
           p.first_name, p.last_name, p.email, p.phone_number 
    FROM appointments a 
    INNER JOIN patients p ON a.patient_id = p.patient_id 
    WHERE a.doctor_id = ? AND CONCAT(a.appointment_date, ' ', a.appointment_time) < ? 
    ORDER BY a.appointment_date DESC";
$stmt_expired = $connection->prepare($sql_expired_appointments);
$stmt_expired->bind_param('is', $doctor_id, $current_date_time);
$stmt_expired->execute();
$result_expired = $stmt_expired->get_result();

// Handle form submission for medical record update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $patient_id = $_POST['patient_id'];
    $doctor_id = $_POST['doctor_id'];
    $session_description = $_POST['session_description'];
    $meds_prescribed = isset($_POST['meds_prescribed']) ? 1 : 0; // Convert checkbox to 1 or 0
    $prescription = isset($_POST['prescription']) ? $_POST['prescription'] : null; // Null if no prescription

    // Validate input
    if (empty($patient_id) || empty($session_description)) {
        echo "Patient ID and session description are required.";
        exit;
    }

    // SQL query to insert medical record
    $sql = "INSERT INTO medical_records (patient_id, doctor_id, session_description, meds_prescribed, prescription)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $connection->prepare($sql);
    $stmt->bind_param('iisis', $patient_id, $doctor_id, $session_description, $meds_prescribed, $prescription);
    
    // Execute the query and check if the insertion was successful
    if ($stmt->execute()) {
        // Redirect to a confirmation page or display a success message
        echo "<p style='color: orange; font-weight: bold;'>Medical record saved successfully!</p>";
    } else {
        echo "Error saving record: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Patient - Afya Bora</title>
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
            padding: 20px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .expired-table {
            width: 80%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .expired-table th, .expired-table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        .expired-table th {
            background-color: #003300;
            color: white;
        }
        .form-container {
            width: 80%;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-container h2 {
            margin-bottom: 20px;
            color: #003300;
        }
        .form-group {
            margin-bottom: 15px;
        }
        .form-group label {
            display: block;
            font-weight: bold;
        }
        .form-group input[type="text"], .form-group textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        .form-group input[type="checkbox"] {
            margin-right: 5px;
        }
        .form-group .prescription-field {
            display: none;
            margin-top: 10px;
        }
        .form-group .prescription-field.active {
            display: block;
        }
        .form-group button {
            background-color: #003300;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .form-group button:hover {
            background-color: #005500;
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
        <a href="doctors.php.php">Back</a>
        <a href="login.php">Logout</a>
    </div>
    <div class="main">
        <h1>Submit Medical records for Patients</h1>
        <table class="expired-table">
            <thead>
                <tr>
                    <th>Appointment ID</th>
                    <th>Patient ID</th>
                    <th>Patient Name</th>
                    <th>Appointment Date</th>
                    <th>Appointment Time</th>
                    <th>Email</th>
                    <th>Phone Number</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($appointment = $result_expired->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($appointment['appointment_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['patient_id']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_date']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['appointment_time']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['email']); ?></td>
                        <td><?php echo htmlspecialchars($appointment['phone_number']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <div class="form-container">
            <h2>Update Medical Record</h2>
            <form action="" method="POST">
                <div class="form-group">
                    <label for="patient_id">Patient ID:</label>
                    <input type="text" id="patient_id" name="patient_id" required>
                </div>
                <div class="form-group">
                    <label for="session_description">Session Description:</label>
                    <textarea id="session_description" name="session_description" rows="4" required></textarea>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox" id="meds_prescribed" name="meds_prescribed" onchange="togglePrescriptionField()">
                        Medications Prescribed?
                    </label>
                    <textarea id="prescription" name="prescription" class="prescription-field" placeholder="Enter prescription details"></textarea>
                </div>
                <input type="hidden" name="doctor_id" value="<?php echo htmlspecialchars($doctor_id); ?>">
                <div class="form-group">
                    <button type="submit">Save Record</button>
                </div>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
    <script>
        function togglePrescriptionField() {
            const checkbox = document.getElementById('meds_prescribed');
            const prescriptionField = document.getElementById('prescription');
            prescriptionField.classList.toggle('active', checkbox.checked);
        }
    </script>
</body>
</html>
