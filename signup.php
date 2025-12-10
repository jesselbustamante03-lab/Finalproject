<?php
// ===============================================
// 1. DATABASE CONNECTION CONFIGURATION 
// ===============================================

$servername = "localhost";
$username = "root";     // <<-- UPDATE THIS with your actual DB username
$password = "";         // <<-- UPDATE THIS with your actual DB password
$dbname = "baryotap";   // <<-- UPDATE THIS with your actual DB name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// CRITICAL: Check the database connection immediately
if ($conn->connect_error) {
    die("Database Connection failed: " . $conn->connect_error);
}

// ===============================================
// 2. RECEIVE AND VALIDATE USER INPUT
// ===============================================

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    die("Access denied.");
}

$first = $_POST['first_name'] ?? '';
$last = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$pass = $_POST['password'] ?? '';
$confirm = $_POST['confirm_password'] ?? '';

// IMPROVEMENT: Use alert and redirect for user feedback
if (empty($first) || empty($last) || empty($email) || empty($pass) || empty($confirm)) {
    echo "<script>alert('Error: All required fields must be filled.'); window.location='dsignup.html';</script>";
    exit();
}

// Check password length (Server-side validation)
if (strlen($pass) < 8) {
    echo "<script>alert('Error: Password must be at least 8 characters long.'); window.location='dsignup.html';</script>";
    exit();
}

// Check password match
if ($pass !== $confirm) {
    // IMPROVEMENT: Use alert and redirect for user feedback
    echo "<script>alert('Error: Passwords do not match!'); window.location='dsignup.html';</script>";
    exit();
}

// ===============================================
// 3. CHECK FOR EXISTING EMAIL (Using Prepared Statement - SECURE)
// ===============================================

$stmt_check = $conn->prepare("SELECT Email FROM users WHERE Email = ?");
if ($stmt_check === false) {
    // Database error - internal server issue
    error_log('Email check prepare failed: ' . $conn->error);
    echo "<script>alert('An internal server error occurred (Code 500).'); window.location='dsignup.html';</script>";
    exit();
}

$stmt_check->bind_param("s", $email);
$stmt_check->execute();
$stmt_check->store_result();

if ($stmt_check->num_rows > 0) {
    $stmt_check->close();
    $conn->close();
    // IMPROVEMENT: Use alert and redirect for user feedback
    echo "<script>alert('Error: This email address is already registered. Please use a different email.'); window.location='dsignup.html';</script>";
    exit();
}
$stmt_check->close();


// ===============================================
// 4. SECURELY INSERT USER DATA INTO DATABASE (Using Password Hash - SECURE)
// ===============================================

$hashed = password_hash($pass, PASSWORD_DEFAULT); // CORRECTLY using password_hash()

$stmt_insert = $conn->prepare("INSERT INTO users (First_Name, Last_Name, Email, Password_hash) VALUES (?, ?, ?, ?)");
if ($stmt_insert === false) {
    // Database error - internal server issue
    error_log('User insert prepare failed: ' . $conn->error);
    echo "<script>alert('An internal server error occurred (Code 501).'); window.location='dsignup.html';</script>";
    exit();
}

$stmt_insert->bind_param("ssss", $first, $last, $email, $hashed);

if ($stmt_insert->execute()) {
    // SUCCESS: Redirect to login page and append the success parameter
    $stmt_insert->close();
    $conn->close();
    header("Location: dlogin.html?success=1"); // <<< CORRECT REDIRECT
    exit();
} else {
    // FINAL FAILURE
    $stmt_insert->close();
    $conn->close();
    echo "<script>alert('An unexpected error occurred during registration: " . htmlspecialchars($conn->error) . "'); window.location='dsignup.html';</script>";
    exit();
}
?>