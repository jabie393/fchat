<?php
session_start();
include '../config/config.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit();
}

$user_id = $_SESSION['user_id'];
$room_id = isset($_GET['room_id']) ? $_GET['room_id'] : '';

if (empty($room_id)) {
    http_response_code(400);
    echo json_encode(['error' => 'Room ID is required']);
    exit();
}

// Tandai pesan sebagai sudah dibaca untuk pesan yang diterima user
$updateStmt = $conn->prepare("UPDATE messages SET is_read = 1 WHERE room_id = ? AND receiver_id = ? AND is_read = 0");
$updateStmt->bind_param("si", $room_id, $user_id);
$updateStmt->execute();
$updateStmt->close();

// Ambil semua pesan setelah update is_read
$stmt = $conn->prepare("SELECT * FROM messages WHERE room_id = ? ORDER BY created_at ASC");
$stmt->bind_param("s", $room_id);
$stmt->execute();
$result = $stmt->get_result();

$messages = [];
while ($row = $result->fetch_assoc()) {
    $messages[] = $row;
}

$stmt->close();
$conn->close();

echo json_encode($messages);
?>