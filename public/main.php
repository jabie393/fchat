<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Ambil daftar user lain
$users = [];
$result = $conn->query("SELECT id, username FROM users WHERE id != $user_id");
while ($row = $result->fetch_assoc()) {
    $users[] = $row;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Chat - PHP Chat App</title>

    <!-- bootstrap -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">

    <!-- box icons -->
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <script src="https://unpkg.com/boxicons@2.1.4/dist/boxicons.js"></script>

    <!-- select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- custom styles -->
    <link rel="stylesheet" href="../assets/css/main.css">
</head>

<body>
    <div class="app-container">
        <div class="header">
            <div class="header-content">
                <div class="greeting">Hi <b><?= htmlspecialchars($_SESSION['username'] ?? '') ?></b> 👋</div>
                <select id="receiverSelect" class="form-control user-select">
                    <option value="">Select User</option>
                    <?php foreach ($users as $u): ?>
                        <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="chat-container">
            <div class="messages" id="messages">
                <div class="no-chat-selected" id="noChatSelected">
                    <i class='bx bx-conversation no-chat-icon'></i>
                    <h4>Select a user to start chatting</h4>
                    <p>Choose someone from the dropdown above</p>
                </div>
            </div>
        </div>

        <div class="input-container">
            <input type="text" id="messageInput" class="message-input" placeholder="Type a message..."
                onkeydown="if(event.key==='Enter'){sendMessage();}">
            <button class="send-button" onclick="sendMessage()">
                <i class='bx bx-send'></i>
            </button>
        </div>
    </div>

    <script>
        const user_id = <?= json_encode($user_id) ?>;
    </script>
    <script src="../assets/js/main.js"></script>

</body>

</html>