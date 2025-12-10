<?php
session_start(); // Start session if you store User_ID in session

// 1. Database connection
$host = "localhost";
$dbname = "baryotap";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 2. Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Use User_ID from session (assumes user is logged in)
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;

    // Sanitize inputs
    $category = $conn->real_escape_string($_POST['category']);
    $description = $conn->real_escape_string($_POST['description']);
    $location = $conn->real_escape_string($_POST['location']);
    
    // Handle file upload
    $photo_url = NULL;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
        $allowed_ext = array('jpg', 'jpeg', 'png');
        $file_name = $_FILES['photo']['name'];
        $file_tmp = $_FILES['photo']['tmp_name'];
        $file_size = $_FILES['photo']['size'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_ext)) {
            if ($file_size <= 100 * 1024 * 1024) { // Max 100MB
                $new_file_name = time() . '_' . uniqid() . '.' . $file_ext;
                $upload_dir = "uploads/";
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                $photo_url = $upload_dir . $new_file_name;
                move_uploaded_file($file_tmp, $photo_url);
            } else {
                die("File size exceeds 100MB.");
            }
        } else {
            die("Invalid file type. Only JPG, JPEG, PNG allowed.");
        }
    }

    // 3. Insert into database
    $stmt = $conn->prepare("INSERT INTO report (User_ID, Category, Description, Photo_URL, Location) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("issss", $user_id, $category, $description, $photo_url, $location);

    if ($stmt->execute()) {
        echo "<script>alert('Issue reported successfully!'); window.location.href='dreport.html';</script>";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>
