<?php
require_once 'database/db.php'; // Ensure this path points to your database connection file
use Database\Database;
session_start();

// Instantiate the Database class
$db = new Database();
$connection = $db->connection;

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Fetch all doctors from the database
$sql_doctors = "SELECT doctor_id, first_name, last_name, speciality, email, phone_number FROM doctors";
$result_doctors = $connection->query($sql_doctors);

// Handle form submission for deleting doctors
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_doctors'])) {
    $selected_ids = $_POST['selected_doctors'];
    $placeholders = implode(',', array_fill(0, count($selected_ids), '?'));

    // Confirmation message before deletion
    echo "<script>
        if (confirm('Are you sure you want to delete doctors with IDs: " . implode(', ', $selected_ids) . "?')) {
            document.location.href = 'dropdoctors.php?action=delete&ids=" . implode(',', $selected_ids) . "';
        }
    </script>";
}

// Handle deletion if confirmed
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['ids'])) {
    $ids_to_delete = explode(',', $_GET['ids']);
    $placeholders = implode(',', array_fill(0, count($ids_to_delete), '?'));
    $stmt_delete = $connection->prepare("DELETE FROM doctors WHERE doctor_id IN ($placeholders)");
    $stmt_delete->bind_param(str_repeat('i', count($ids_to_delete)), ...$ids_to_delete);

    if ($stmt_delete->execute()) {
        echo "<script>alert('Selected doctors have been deleted successfully!');</script>";
        header("Location: dropdoctors.php");
        exit;
    } else {
        echo "<script>alert('Error deleting records. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Drop Doctors - Afya Bora</title>
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
        .table-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 90%;
            max-width: 800px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #003300;
            color: white;
        }
        .submit-btn {
            background-color: #FF6600;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            margin-top: 20px;
        }
        .submit-btn:hover {
            background-color: #CC5200;
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
        <a href="admin.php">Back</a>
        <a href="login.php">Logout</a>
    </div>
    <div class="main">
        <div class="table-container">
            <h1>Drop Doctors</h1>
            <form method="POST" action="">
                <table>
                    <thead>
                        <tr>
                            <th>Select</th>
                            <th>Doctor ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Speciality</th>
                            <th>Email</th>
                            <th>Phone Number</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result_doctors->num_rows > 0): ?>
                            <?php while ($doctor = $result_doctors->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="selected_doctors[]" value="<?php echo htmlspecialchars($doctor['doctor_id']); ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($doctor['doctor_id']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['first_name']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['last_name']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['speciality']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['email']); ?></td>
                                    <td><?php echo htmlspecialchars($doctor['phone_number']); ?></td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7">No doctors found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                <button type="submit" class="submit-btn">Delete Selected</button>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
