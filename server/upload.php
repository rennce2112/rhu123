<?php
// Database configuration
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "reservationrhu1";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "Form submitted successfully!";
    error_log(print_r($_POST, true));

    // Retrieve and sanitize input
    $first_name = $conn->real_escape_string(trim($_POST['first_name']));
    $middle_name = $conn->real_escape_string(trim($_POST['middle_name']));
    $last_name = $conn->real_escape_string(trim($_POST['last_name']));
    $birthday = $conn->real_escape_string(trim($_POST['birthday']));
    $gender = $conn->real_escape_string(trim($_POST['gender']));
    $contact_number = $conn->real_escape_string(trim($_POST['contact_number']));
    $email = $conn->real_escape_string(trim($_POST['email']));
    $password = trim($_POST['password']);

    // Retrieve address components
    $region = $conn->real_escape_string(trim($_POST['region_text']));
    $province = $conn->real_escape_string(trim($_POST['province_text']));
    $city = $conn->real_escape_string(trim($_POST['city_text']));
    $barangay = $conn->real_escape_string(trim($_POST['barangay_text']));
    $street = $conn->real_escape_string(trim($_POST['street_text']));

    // Basic input validation
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format.");
    }

    if (strlen($password) < 8) {
        die("Password should be at least 8 characters.");
    }

    // Check if user already exists based on email
    $stmt = $conn->prepare("SELECT * FROM Users WHERE email_address = ?");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: http://localhost/navigation%20bar/html.php");
    } else {
        // Calculate age
        $dob = new DateTime($birthday);
        $now = new DateTime();
        $age = $now->diff($dob)->y;

        // Hash the password for secure storage
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert the user data into the database
        $stmt = $conn->prepare("INSERT INTO Users (first_name, middle_name, last_name, age, birthday, email_address, password, phone_number, gender, address, province, municipality, barangay, street) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        if (!$stmt) {
            die("Prepare failed: " . $conn->error);
        }

        $stmt->bind_param("sssissssssssss", $first_name, $middle_name, $last_name, $age, $birthday, $email, $hashed_password, $contact_number, $gender, $region, $province, $city, $barangay, $street);

        if ($stmt->execute()) {
            header("Location: html.php");
            exit();
        } else {
            echo "Error: " . $stmt->error;
        }
    }

    // Close the prepared statement and connection
    $stmt->close();
} else {
    // Optional: Handle GET requests or direct access
    echo "No data received. Please submit the form.";
}

$conn->close();
?>
