<?php
// CRITICAL: Start the session for authentication and messages
session_start();

// ===============================================
// 1. DATABASE CONNECTION CONFIGURATION
// ===============================================

$servername = "localhost";
$username = "root"; // <<-- Ensure this is correct for your MySQL setup
$password = ""; // <<-- Ensure this is correct for your MySQL setup
$dbname = "baryotap"; // <<-- Your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error); 
}

// ===============================================
// 2. PROCESS LOGIN INPUT SECURELY
// ===============================================

// Get user input safely
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';

// Flag to track overall success
$login_successful = false; 
$redirect_page = ''; // Variable to hold the target page

// Basic check for empty fields
if (empty($email) || empty($pass)) {
    echo "<script>alert('Please enter both email and password.');</script>";
    exit();
}

// ===============================================
// 3. UNIVERSAL USER LOGIN CHECK (Using Database Role)
// 
// NOTE: The previous hardcoded admin check has been removed.
// All users (Admin and Regular) now authenticate via the database.
// ===============================================

// Prepare the SQL statement to fetch the user's ID, password hash, AND Role
$stmt = $conn->prepare("SELECT User_ID, Password_hash, Role FROM users WHERE Email = ?");

if ($stmt === false) {
    error_log("Login query prepare failed: " . $conn->error);
    echo "<script>alert('An internal server error occurred.');</script>";
    $conn->close();
    exit();
}

// Bind and execute
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

// Check if a user was found
if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $hashed = $row['Password_hash'];
    $user_id = $row['User_ID'];
    $user_role = $row['Role']; // <-- Role retrieved from the database

    // Verify the submitted password against the stored hash
    if (password_verify($pass, $hashed)) {
        
        // SUCCESSFUL LOGIN:
        $_SESSION['User_ID'] = $user_id;
        $_SESSION['logged_in'] = true;
        // Set admin flag based on the actual Role value (case-sensitive check)
        $_SESSION['is_admin'] = ($user_role === 'admin'); 
        $_SESSION['User_Role'] = $user_role;
        $_SESSION['login_success_message'] = "Login successful! Welcome back.";
        $login_successful = true; // Set flag to true

        // Determine the correct redirection page
        if ($_SESSION['is_admin']) {
            $redirect_page = "admin.php";
        } else {
            // Assuming all non-Admin roles (like 'User') redirect to main.php
            $redirect_page = "main.php"; 
        }
    }
}

// Close resources immediately
if (isset($stmt)) $stmt->close();
if (isset($conn)) $conn->close();

// FINAL REDIRECTION LOGIC
if ($login_successful) {
    // Redirect to the determined page (admin.php or main.php)
    header("Location: " . $redirect_page);
    exit();
} else {
    // If login failed for ANY reason (email not found, password mismatch, etc.)
    echo "<script>alert('Login failed: Incorrect email or password.');</script>";
    exit();
}
?>