<?php 
require_once 'database/db.php';
use Database\Database;
session_start();

// Ensure the user is logged in as either a doctor or patient
if (!isset($_SESSION['user_id']) || ($_SESSION['user_role'] !== 'patient' && $_SESSION['user_role'] !== 'doctor')) {
    header("Location: login.php");
    exit;
}

// Determine the role and IDs for the chat
$user_role = $_SESSION['user_role']; // 'patient' or 'doctor'
$user_id = $_SESSION['user_id']; // Logged-in user ID

if ($user_role === 'doctor') {
    $chat_with_id = isset($_GET['patient_id']) ? intval($_GET['patient_id']) : 0; // Doctor chatting with a patient
} else {
    $chat_with_id = isset($_GET['doctor_id']) ? intval($_GET['doctor_id']) : 0; // Patient chatting with a doctor
}

// Initialize database connection
$db = new Database();
$connection = $db->connection;

// Fetch existing messages between the doctor and the patient
$sql_fetch_messages = "
    SELECT * FROM messages 
    WHERE 
        (patient_id = ? AND doctor_id = ?) 
    ORDER BY timestamp
";
$stmt_messages = $connection->prepare($sql_fetch_messages);

// Bind parameters based on role
if ($user_role === 'doctor') {
    $stmt_messages->bind_param('ii', $chat_with_id, $user_id); // Doctor's perspective
} else {
    $stmt_messages->bind_param('ii', $user_id, $chat_with_id); // Patient's perspective
}

$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();

$messages = [];
while ($row = $result_messages->fetch_assoc()) {
    $messages[] = $row;
}

// Handle new message submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);

    if (!empty($message)) {
        $sql_send_message = "INSERT INTO messages (patient_id, doctor_id, content, sender_role) VALUES (?, ?, ?, ?)";
        $stmt_send = $connection->prepare($sql_send_message);

        // Bind parameters based on role
        if ($user_role === 'doctor') {
            $stmt_send->bind_param('iiss', $chat_with_id, $user_id, $message, $user_role); // Doctor sends
        } else {
            $stmt_send->bind_param('iiss', $user_id, $chat_with_id, $message, $user_role); // Patient sends
        }

        $stmt_send->execute();
    }
    // Redirect back to the chat to prevent resubmission
    $redirect_to = $user_role === 'doctor' ? "chat.php?patient_id=$chat_with_id" : "chat.php?doctor_id=$chat_with_id";
    header("Location: $redirect_to");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
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
        .chat-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            width: 100%;
        }
        .chat-container h1 {
            margin-bottom: 20px;
            color: #003300;
            text-align: center;
        }
        .messages {
            height: 300px;
            overflow-y: auto;
            margin-bottom: 20px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            background: #f9f9f9;
            position: relative;
        }
        .placeholder {
            text-align: center;
            color: #666;
            font-style: italic;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }
        .placeholder img {
            max-width: 100px;
            opacity: 0.4;
        }
        .message {
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 8px;
        }
        .message.patient {
            text-align: right;
            background: #d9edf7;
        }
        .message.doctor {
            text-align: left;
            background: #ffe6e6;
        }
        form {
            display: flex;
            width: 100%;
        }
        form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px 0 0 5px;
            font-size: 16px;
        }
        form button {
            padding: 10px 20px;
            border: none;
            background: #FF6600;
            color: white;
            cursor: pointer;
            border-radius: 0 5px 5px 0;
            font-size: 16px;
        }
        form button:hover {
            background: #e65c00;
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
        <a href="<?php echo $user_role === 'doctor' ? 'viewmessages.php' : 'patients.php'; ?>">Back</a>
    </div>
    <div class="main">
        <div class="chat-container">
            <h1>Chat</h1>
            <div class="messages">
                <?php if (empty($messages)): ?>
                    <div class="placeholder">
                        <img src="chat.jpg" alt="Chat Icon">
                        <p>Start a conversation!</p>
                    </div>
                <?php else: ?>
                    <?php foreach ($messages as $msg): ?>
                        <div class="message <?php echo htmlspecialchars($msg['sender_role']); ?>">
                            <p><?php echo htmlspecialchars($msg['content']); ?></p>
                            <small><?php echo htmlspecialchars($msg['timestamp']); ?></small>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <form method="POST" action="">
                <input type="text" name="message" placeholder="Type your message here..." required>
                <button type="submit">Send</button>
            </form>
        </div>
    </div>
    <footer>
        <p>Afya Bora, Copyright &copy; <?php echo date('Y'); ?></p>
    </footer>
</body>
</html>
