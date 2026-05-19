<?php
/**
 * ILLUME — Lead Submission Handler
 * Handles AJAX requests from the front-end.
 */

header('Content-Type: application/json');

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'illume_db';

$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit;
}

// Get POST data
$input = json_decode(file_get_contents('php://input'), true);

if (!$input) {
    echo json_encode(['success' => false, 'message' => 'No input provided']);
    exit;
}

$name     = $conn->real_escape_string($input['name'] ?? '');
$email    = $conn->real_escape_string($input['email'] ?? '');
$phone    = $conn->real_escape_string($input['phone'] ?? '');
$service  = $conn->real_escape_string($input['service'] ?? 'Unknown');
$source   = $conn->real_escape_string($input['source'] ?? 'Website Form');
$timeline = $conn->real_escape_string($input['timeline'] ?? '');
$notes    = $conn->real_escape_string($input['notes'] ?? '');
$keyword  = $conn->real_escape_string($input['keyword'] ?? '');

// Map contact string if it was combined (from my earlier JS logic)
if (isset($input['contact']) && empty($phone) && empty($email)) {
    // Basic attempt to split or just store in notes
    $notes .= "\nContact Info: " . $input['contact'];
}

$sql = "INSERT INTO leads (name, email, phone, service_interest, source, trigger_keyword, timeline, notes, status) 
        VALUES ('$name', '$email', '$phone', '$service', '$source', '$keyword', '$timeline', '$notes', 'New')";

if ($conn->query($sql)) {
    echo json_encode(['success' => true, 'id' => $conn->insert_id]);
} else {
    echo json_encode(['success' => false, 'message' => $conn->error]);
}

$conn->close();
