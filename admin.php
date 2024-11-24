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

// Retrieve admin information from the database
$admin_id = $_SESSION['user_id'];
$sql_admin = "SELECT admin_id, name, email FROM systemadmin WHERE admin_id = ?";
$stmt_admin = $connection->prepare($sql_admin);
$stmt_admin->bind_param('i', $admin_id);
$stmt_admin->execute();
$result_admin = $stmt_admin->get_result();
$admin = $result_admin->fetch_assoc();

if (!$admin) {
    echo "Error: Admin not found.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile - Afya Bora</title>
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
            justify-content: center; /* Keeps the profile container centered */
            padding: 20px;
        }
        .content {
            display: flex;
            gap: 20px; /* Creates space between the profile and buttons */
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
            gap: 15px;
            align-items: flex-start; /* Aligns the buttons to the left */
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
            transition: background-color 0.3s;
        }
        .button-container a:hover {
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
        <img src="logo.png" alt="Afya Bora Logo">
        <a href="login.php">Logout</a>
    </div>
    <div class="main">
        <div class="content">
            <div class="button-container">
                <a href="droppatient.php">Drop Patient</a>
                <a href="dropdoctor.php">Drop Doctor</a>
            </div>
            <div class="profile-container">
                <h1>Admin Profile</h1>
                <div class="field">
                    <label>Admin ID:</label>
                    <p><?php echo htmlspecialchars($admin['admin_id']); ?></p>
                </div>
                <div class="field">
                    <label>Name:</label>
                    <p><?php echo htmlspecialchars($admin['name']); ?></p>
                </div>
                <div class="field">
                    <label>Email:</label>
                    <p><?php echo htmlspecialchars($admin['email']); ?></p>
                </div>
            </div>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
