<?php
session_start();

// --- Database Connection ---
$host = "localhost";
$dbname = "baryotap";
$username = "root";
$password = "";

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// -------------------------------------------------------------
// --- TEMPORARY LOGIN/USER ID ASSIGNMENT (For Testing) ---
// ⚠️ Set $user_id to a known ID that exists in your 'users' table.
$user_id = 1; 
// -------------------------------------------------------------

// --- Get POST data ---
$document_type = $_POST['documentType'] ?? '';

// --- Read ALL form fields ---
// 1. Name and Address fields entered by the user in the modal
$request_full_name = $_POST['fullName'] ?? ''; 
$request_general_address = $_POST['address'] ?? ''; 

// 2. Specific Document Fields
$purpose_of_request = $_POST['purpose'] ?? NULL;       // From Barangay Clearance
$indigency_reason = $_POST['reason'] ?? NULL;         // From Barangay Indigency
$years_of_residency = $_POST['years'] ?? NULL;         // From Certificate of Residency
$business_name = $_POST['businessName'] ?? NULL;       // From Business Permit
$business_address = $_POST['businessAddress'] ?? NULL; // From Business Permit

// --- Use the user-typed values for insertion ---
// The $request_general_address will be NULL/empty for Business Permit, 
// and the $business_address will be NULL/empty for other types.
$full_name_for_db = $request_full_name;
$resident_address_for_db = $request_general_address;

// --- Insert into document_request (Full Structure with 9 placeholders) ---
// NOTE: We assume the 'users' table lookup for name/address is NOT needed 
// since the user types it in the form.

$sql = "INSERT INTO document_request (
    User_ID, 
    Document_Type, 
    Date_Requested, 
    Status, 
    Fullname, 
    Resident_Address, 
    Purpose_of_Request, 
    Indigency_Reason, 
    Years_of_Residency, 
    Business_Name, 
    Business_Address
)
VALUES (?, ?, CURDATE(), 'Pending', ?, ?, ?, ?, ?, ?, ?)"; // 9 placeholders

// 'issssssss' defines the data types for the 9 variables (1 int + 8 strings)
$stmt = $conn->prepare($sql);
$stmt->bind_param(
    "issssssss", 
    $user_id, 
    $document_type, 
    $full_name_for_db,            // ⬅️ NOW uses name from form
    $resident_address_for_db,     // ⬅️ NOW uses address from form
    $purpose_of_request, 
    $indigency_reason, 
    $years_of_residency, 
    $business_name, 
    $business_address
);

if ($stmt->execute()) {
    echo "<script>alert('Document request submitted successfully!'); window.location.href='Request docu.html';</script>";
} else {
    echo "Error executing statement: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>