<?php  
include_once 'db_connect.php';
require_once 'db_connect.php';

// Set the header for JSON output
header('Content-Type: application/json');

// Fetch the reservation date, time, and duration
$query = "SELECT booking_reservation_date AS date, booking_reservation_time AS time, duration FROM Schedules";
$result = mysqli_query($conn, $query);

// Initialize an array to hold the results
$data = [];

const STATUS_GREEN = 'green';
const STATUS_ORANGE = 'orange';
const STATUS_RED = 'red';

while ($row = mysqli_fetch_assoc($result)) {
    $date = $row['date'];
    $time = $row['time'];
    $duration = $row['duration'];

    // Initialize the date entry if not already set
    if (!isset($data[$date])) {
        $data[$date] = ['date' => $date, 'reserved_times' => []];
    }

    // Initialize the time slot if it does not exist
    if (!isset($data[$date]['reserved_times'][$time])) {
        $data[$date]['reserved_times'][$time] = ['total_reserved_minutes' => 0, 'status' => STATUS_GREEN];
    }

    // Add the duration to the total reserved minutes for the time slot
    $data[$date]['reserved_times'][$time]['total_reserved_minutes'] += $duration;

    // Determine the status based on the total reserved minutes for this time slot
    $totalMinutes = $data[$date]['reserved_times'][$time]['total_reserved_minutes'];
    if ($totalMinutes >= 60) {
        $data[$date]['reserved_times'][$time]['status'] = STATUS_RED; // Fully booked (60 minutes)
    } elseif ($totalMinutes >= 40) {
        $data[$date]['reserved_times'][$time]['status'] = STATUS_ORANGE; // Partially booked (40 minutes)
    } elseif ($totalMinutes >= 20) {
        $data[$date]['reserved_times'][$time]['status'] = STATUS_GREEN; // Lightly booked (20 minutes)
    }
}

// Filter out dates that have no reserved times
$filteredData = [];
foreach ($data as $date => $info) {
    if (!empty($info['reserved_times'])) {
        $filteredData[] = [
            'date' => $date,
            'reserved_times' => $info['reserved_times'],
        ];
    }
}

// Output JSON response
echo json_encode($filteredData);

// Close the database connection
mysqli_close($conn);
?>
