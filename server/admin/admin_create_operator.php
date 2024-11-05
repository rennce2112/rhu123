<?php
include_once 'db_connect.php';
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $conn->real_escape_string(trim($_POST['full_name']));
    $specialty = $conn->real_escape_string(trim($_POST['specialty']));
    $email_address = $conn->real_escape_string(trim($_POST['email_address']));
    $phone_number = $conn->real_escape_string(trim($_POST['phone_number']));
    $availability = $conn->real_escape_string(trim($_POST['availability']));
    $office_location = $conn->real_escape_string(trim($_POST['office_location']));
    $password = trim($_POST['password']);
    $operator_type = $conn->real_escape_string(trim($_POST['operator_type']));
    
    // Hash password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
    // Begin transaction
    $conn->begin_transaction();
    
    try {
        // Insert into Doctors table
        $stmt = $conn->prepare("INSERT INTO Doctors (full_name, specialty, email_address, phone_number, availability, office_location) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $full_name, $specialty, $email_address, $phone_number, $availability, $office_location);
        $stmt->execute();
        
        // Insert into Operators table
        $stmt2 = $conn->prepare("INSERT INTO Operators (email_address, password, operator_type) VALUES (?, ?, ?)");
        $stmt2->bind_param("sss", $email_address, $hashed_password, $operator_type);
        $stmt2->execute();
        
        $conn->commit();
        header("Location: admin_create_operator.php?success=1");
        exit();
    } catch (Exception $e) {
        $conn->rollback();
        header("Location: admin_create_operator.php?error=" . urlencode($e->getMessage()));
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Operator Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Create Operator Account</h3>
                    </div>
                    <div class="card-body">
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="needs-validation" novalidate>
                            <div class="mb-3">
                                <label for="operator_type" class="form-label">Operator Type</label>
                                <select class="form-select" name="operator_type" required>
                                    <option value="">Select Type</option>
                                    <option value="doctor">Doctor</option>
                                    <option value="nurse">Nurse</option>
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Full Name</label>
                                <input type="text" class="form-control" name="full_name" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="specialty" class="form-label">Specialty</label>
                                <input type="text" class="form-control" name="specialty" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="email_address" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email_address" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="phone_number" class="form-label">Phone Number</label>
                                <input type="tel" class="form-control" name="phone_number" pattern="[0-9]{11}" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="availability" class="form-label">Availability</label>
                                <input type="text" class="form-control" name="availability" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="office_location" class="form-label">Office Location</label>
                                <input type="text" class="form-control" name="office_location" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <a href="../login.php">log-out</a>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Create Account</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        (function () {
            'use strict'
            var forms = document.querySelectorAll('.needs-validation')
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>
</html>