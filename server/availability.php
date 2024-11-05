<?php 
// Database connection
include_once 'db_connect.php';
require_once 'db_connect.php';


session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $date = $data['date'] ?? null;
    $time = $data['time'] ?? null;
    $duration = $data['duration'] ?? null; // Duration from the form

    // Check if user is logged in
    if (!isset($_SESSION['id'])) {
        echo json_encode(['success' => false, 'message' => 'User not logged in.']);
        exit;
    }

    $user_id = $_SESSION['id']; // Get user ID from session

    // Validate the inputs
    if (!$date || !$time || !$duration) {
        echo json_encode(['success' => false, 'message' => 'Invalid input.']);
        exit;
    }

    // Check total reserved duration for the specific date and time
    $checkQuery = $conn->prepare("SELECT SUM(duration) as total_duration FROM Schedules WHERE booking_reservation_date = ? AND booking_reservation_time = ?");
    $checkQuery->bind_param("ss", $date, $time);
    $checkQuery->execute();
    $checkResult = $checkQuery->get_result();
    $checkRow = $checkResult->fetch_assoc();

    $total_duration = $checkRow['total_duration'] ?? 0;

    // Check if adding this duration exceeds the limit
    if (($total_duration + $duration) > 60) {
        echo json_encode(['success' => false, 'message' => 'Time slot is fully reserved for this hour.']);
    } else {
        // Prepare the INSERT statement with the duration
        $stmt = $conn->prepare("INSERT INTO Schedules (booking_reservation_date, booking_reservation_time, user_id, status, duration) VALUES (?, ?, ?, ?, ?)");

        // Determine the status based on total reserved duration
        $status = ($total_duration + $duration == 20) ? 'green' : (($total_duration + $duration == 40) ? 'orange' : 'red');
        
        $stmt->bind_param("ssisi", $date, $time, $user_id, $status, $duration);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'Reservation successful!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Reservation failed due to a database error.']);
        }

        $stmt->close();
    }
    
    $checkQuery->close();
    $conn->close();
    exit;
}
?>
