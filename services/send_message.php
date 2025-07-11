<?php
session_start();
include '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sender_id = isset($_POST['sender_id']) ? intval($_POST['sender_id']) : 0;
    $receiver_id = isset($_POST['receiver_id']) ? intval($_POST['receiver_id']) : 0;
    $room_id = isset($_POST['room_id']) ? $_POST['room_id'] : '';
    $message = isset($_POST['message']) ? trim($_POST['message']) : '';

    if ($sender_id && $receiver_id && $room_id && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, room_id, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iiss", $sender_id, $receiver_id, $room_id, $message);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to send message.']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>