<?php
include_once 'db_connect.php';
session_start();

function checkUserType($email, $conn) {
    // Check Operators table
    $stmt = $conn->prepare("SELECT operator_type FROM Operators WHERE email_address = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            return ['type' => 'operator', 'role' => $row['operator_type']];
        }
        $stmt->close();
    }
    
    // Check Admin table
    $stmt = $conn->prepare("SELECT admin_id FROM Admins WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['type' => 'admin', 'role' => 'admin'];
        }
        $stmt->close();
    }
    
    // Check Users table
    $stmt = $conn->prepare("SELECT user_id FROM Users WHERE email_address = ?");
    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            return ['type' => 'user', 'role' => 'user'];
        }
        $stmt->close();
    }
    
    return null;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $conn->real_escape_string(trim($_POST['email_address']));
    $password = trim($_POST['password']);
    
    $userType = checkUserType($email, $conn);
    
    if (!$userType) {
        header("Location: ../login.php?error=" . urlencode("Account not found"));
        exit();
    }
    
    switch ($userType['type']) {
        case 'user':
            $stmt = $conn->prepare("SELECT user_id, first_name, last_name, password FROM Users WHERE email_address = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_type'] = 'user';
                        $_SESSION['user_id'] = $row['user_id'];
                        $_SESSION['name'] = $row['first_name'] . ' ' . $row['last_name'];
                        $_SESSION['email_address'] = $email;
                        
                        header("Location: protected_page.php");
                        exit();
                    }
                }
                $stmt->close();
            }
            break;
            
        case 'operator':
            $stmt = $conn->prepare("SELECT o.operator_id, o.password, o.operator_type, d.full_name 
                                  FROM Operators o 
                                  JOIN Doctors d ON o.email_address = d.email_address 
                                  WHERE o.email_address = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_type'] = 'operator';
                        $_SESSION['operator_id'] = $row['operator_id'];
                        $_SESSION['operator_type'] = $row['operator_type'];
                        $_SESSION['name'] = $row['full_name'];
                        $_SESSION['email_address'] = $email;
                        
                        header("Location: admin/operator_login.php");
                        exit();
                    }
                }
                $stmt->close();
            }
            break;
            
        case 'admin':
            $stmt = $conn->prepare("SELECT admin_id, password, name FROM Admins WHERE email = ?");
            if ($stmt) {
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();
                
                if ($row = $result->fetch_assoc()) {
                    if (password_verify($password, $row['password'])) {
                        $_SESSION['loggedin'] = true;
                        $_SESSION['user_type'] = 'admin';
                        $_SESSION['admin_id'] = $row['admin_id'];
                        $_SESSION['name'] = $row['name'];
                        $_SESSION['email'] = $email;
                        
                        header("Location: admin/admin_create_operator.php");
                        exit();
                    }
                }
                $stmt->close();
            }
            break;
    }
    
    header("Location: ../login.php?error=invalid");
    exit();
} else {
    header("Location: ../login.php");
    exit();
}

$conn->close();
?>