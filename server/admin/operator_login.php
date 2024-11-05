<?php
include_once 'db_connect.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email_address = $conn->real_escape_string(trim($_POST['email_address']));
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT o.operator_id, o.password, o.operator_type, d.full_name 
                           FROM Operators o 
                           JOIN Doctors d ON o.email_address = d.email_address 
                           WHERE o.email_address = ?");
    
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("s", $email_address);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($operator_id, $hashed_password, $operator_type, $full_name);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            $_SESSION['operator_loggedin'] = true;
            $_SESSION['operator_id'] = $operator_id;
            $_SESSION['operator_type'] = $operator_type;
            $_SESSION['operator_name'] = $full_name;
            $_SESSION['email_address'] = $email_address;

            header("Location: operator_dashboard.php");
            exit();
        }
    }

    header("Location: operator_login.php?error=invalid");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Operator Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        <h3 class="text-center">Operator Login</h3>
                    </div>
                    <div class="card-body">
                        <?php if (isset($_GET['error']) && $_GET['error'] == 'invalid'): ?>
                            <div class="alert alert-danger">Invalid email or password.</div>
                        <?php endif; ?>
                        
                        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                            <div class="mb-3">
                                <label for="email_address" class="form-label">Email Address</label>
                                <input type="email" class="form-control" name="email_address" required>
                            </div>
                            
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" name="password" required>
                            </div>
                            <a href="../login.php">log-out</a>
                            <div class="text-center">
                                <button type="submit" class="btn btn-primary">Login</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>