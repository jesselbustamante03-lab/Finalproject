<?php
// FIX: Commented out session_start() and login check to allow direct testing.
session_start(); // <-- UNCOMMENT THIS LINE

// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "baryotap";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// RESTORE THIS BLOCK - It will now work because session_start() is uncommented.
if (!isset($_SESSION['User_ID'])) {
    header("Location: dlogin.html");
    exit();
}


// FIX: Hardcoded User_ID for direct testing - REMOVE OR COMMENT OUT THIS LINE
// $userId = 1; // <-- CHANGE THIS NUMBER to test a different user

// *** USE THE LOGGED-IN USER ID FROM THE SESSION ***
$userId = $_SESSION['User_ID'];
// Initialize user data
$userData = [
    'First_Name' => '',
    'Last_Name' => '',
    'Email' => '',
    'Full_Address' => '',
    'Profile_Picture_URL' => 'image/0a79fb93-911d-4960-81ab-23adab0223cb.jpg',
    'Validation_Status' => 'Pending',
    'validation_pic' => 'image/id_placeholder.png'
];

// Fetch user data from database
$query = "SELECT First_Name, Last_Name, Email, Full_Address, Profile_Picture_URL, Validation_Status, validation_pic FROM users WHERE User_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $userData = array_merge($userData, $result->fetch_assoc());
} else {
    // If user is not found, you can set defaults or display a message
    // die("User not found"); 
    // For testing, let's just proceed with defaults if ID 1 isn't found
}

$stmt->close();

// Handle AJAX requests
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'updateProfile') {
    // Ensure this path is followed for AJAX calls
    header('Content-Type: application/json');

    $firstName = trim($_POST['firstName']);
    $lastName = trim($_POST['lastName']);
    $email = trim($_POST['email']);
    $fullAddress = trim($_POST['fullAddress']);
    $profilePicBase64 = isset($_POST['profilePic']) ? $_POST['profilePic'] : '';

    // Validate email
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !str_ends_with($email, '@gmail.com')) {
        echo json_encode(['success' => false, 'message' => 'Please use a valid Gmail address']);
        exit();
    }

    // Handle profile picture upload
    $picturePath = $userData['Profile_Picture_URL'];
    if (!empty($profilePicBase64)) {
        // Base64 decoding and path generation logic
        $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $profilePicBase64));
        // Use a unique filename
        $picturePath = 'uploads/profile_' . $userId . '_' . time() . '.png';
        
        if (!is_dir('uploads')) {
            mkdir('uploads', 0755, true);
        }
        
        if (file_put_contents($picturePath, $imageData)) {
            // Delete old picture if it exists and is not the default
            if (!empty($userData['Profile_Picture_URL']) && file_exists($userData['Profile_Picture_URL']) && $userData['Profile_Picture_URL'] !== 'image/0a79fb93-911d-4960-81ab-23adab0223cb.jpg') {
                unlink($userData['Profile_Picture_URL']);
            }
        } else {
            // Log error but proceed with old path if write fails
            error_log("Failed to write profile picture file: " . $picturePath);
            $picturePath = $userData['Profile_Picture_URL'];
        }
    }

    // Update database
    $updateQuery = "UPDATE users SET First_Name = ?, Last_Name = ?, Email = ?, Full_Address = ?, Profile_Picture_URL = ? WHERE User_ID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("sssssi", $firstName, $lastName, $email, $fullAddress, $picturePath, $userId);

    if ($updateStmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
    }

    $updateStmt->close();
    exit();
}

if ($action === 'uploadValidationID') {
    // Ensure this path is followed for AJAX calls
    header('Content-Type: application/json');

    $idPicBase64 = isset($_POST['idPic']) ? $_POST['idPic'] : '';

    if (empty($idPicBase64)) {
        echo json_encode(['success' => false, 'message' => 'No file provided']);
        exit();
    }

    $imageData = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $idPicBase64));
    $validationPicPath = 'uploads/validation_' . $userId . '_' . time() . '.png';

    if (!is_dir('uploads')) {
        mkdir('uploads', 0755, true);
    }

    if (file_put_contents($validationPicPath, $imageData)) {
        // Delete old validation picture if it exists and is not the default
        if (!empty($userData['validation_pic']) && file_exists($userData['validation_pic']) && $userData['validation_pic'] !== 'image/id_placeholder.png') {
             unlink($userData['validation_pic']);
        }

        // Update validation_pic in database
        $updateQuery = "UPDATE users SET validation_pic = ?, Validation_Status = 'Pending Review' WHERE User_ID = ?";
        $updateStmt = $conn->prepare($updateQuery);
        $updateStmt->bind_param("si", $validationPicPath, $userId);

        if ($updateStmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'ID submitted successfully']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Database update failed: ' . $conn->error]);
        }

        $updateStmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'File upload failed']);
    }

    exit();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile - Baryo Tap</title>
    <style>
        /* ----------------------------------- */
        /* --- 1. GENERAL & BASE STYLES --- */
        /* ----------------------------------- */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh;
            font-family: 'Inter', sans-serif;
            background: url(image/background.png)center/cover no-repeat fixed;
        }

        /* --- UNIFIED LAYOUT STRUCTURE --- */
        .layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* ----------------------------------- */
        /* --- 2. LEFT SIDEBAR STYLES --- (UPDATED) */
        /* ----------------------------------- */
        .sidebar {
            width: 432px;
            background: #f3fff5;
            padding: 25px 22px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            z-index: 10;
            box-shadow: 2px 0 5px rgba(0,0,0,0.1);
        }

        .logo-area {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 35px;
        }
        .logo-area img { width: 70px; height: 70px; }
        .logo-text { font-size: 28px; font-weight: 800; color: #000; }

        .menu-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 10px 14px;
            margin-bottom: 55px;
            border-radius: 16px;
            background: transparent;
            transition: all 0.3s ease;
            cursor: pointer;
            text-decoration: none; /* Already correctly applied here */
        }
        .menu-item:hover { background: rgba(24, 118, 5, 0.35); transform: translateX(5px); }
        .menu-item img { width: 46px; height: 46px; }
        .menu-item span {
            font-size: 22px;
            font-weight: 600;
            color: #000;
        }

        .divider {
            height: 2px;
            background: #187605;
            margin: 70px 0 20px 0;
            flex-shrink: 0;
        }
        
        /* New/Updated rule for the profile link */
        .profile-link {
            text-decoration: none; /* Removes underline from the profile link */
            display: block; /* Makes the entire block clickable */
            margin-top: auto; /* Pushes the profile link to the bottom */
            padding-bottom: 15px;
        }
        .profile {
            text-align: center;
        }
        .profile-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #187605;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.3), 0 4px 10px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }
        .profile-name {
            font-size: 24px;
            font-weight: 600;
            color: #000;
            margin-bottom: 4px;
        }
        .profile-email {
            font-size: 21px;
            color: #000;
        }

        .sidebar-footer {
            padding-top: 10px;
            font-size: 14px;
            text-align: center;
            color: #555;
            border-top: 1px solid #ddd;
            flex-shrink: 0;
        }
         /* FULL-WIDTH FOOTER STYLES */
        .rectangle-139 {
          background: rgba(7, 34, 1, 0.95);
          width: 100%; 
          height: 300px;
          position: relative; 
          padding: 30px 0;
        }
        .footer-content-wrapper {
            position: relative;
            height: 100%;
            width: 100%;
            min-width: 1400px; 
            margin: 0 auto;
        }
        
        /* General Column Positioning */
        .ellipse-20 { background: #ffffff; border-radius: 50%; width: 67px; height: 67px; position: absolute; left: 41px; top: 29px; }
        .social-responsibility-2 { width: 55px; height: 55px; position: absolute; left: 47px; top: 35px; object-fit: cover; }
        .baryo-tap2 { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 118px; top: 44px; }
        .a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 1.8; position: absolute; left: 47px; top: 119px; width: 320px;
        }
        .explore { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 472px; top: 44px; }
        .submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 2.4; position: absolute; left: 472px; top: 94px; width: 320px;
        }
        .services { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 845px; top: 44px; }
        .indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 2.4; position: absolute; left: 845px; top: 94px; width: 320px;
        }
        .contact-us { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 1220px; top: 44px; }
        
        /* Contact Column (Flexbox fix) */
        .contact-column {
            position: absolute;
            left: 1220px; 
            top: 94px; 
            width: 300px; 
            display: flex;
            flex-direction: column;
            gap: 15px; 
        }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-family: "Inter-Medium", sans-serif;
            font-size: 15px;
            position: static; 
            top: auto;
            text-decoration: none; /* Ensure links look correct */
        }
        .contact-item img { width: 20px; height: 20px; flex-shrink: 0; }
        
        /* Responsive adjustments for smaller screens */
        @media (max-width: 1400px) {
            .footer-content-wrapper {
                min-width: 100%;
                padding: 0 20px; /* Add horizontal padding for smaller screens */
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
            }
            .rectangle-139 {
                height: auto; /* Allow footer height to grow */
            }
            /* Reset absolute positioning for responsiveness */
            .ellipse-20, .social-responsibility-2, .baryo-tap2, .a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu, .explore, .submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk, .services, .indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking, .contact-us, .contact-column {
                position: static;
                width: auto;
                margin-bottom: 20px;
            }
            /* Fix the first column layout */
            .logo-footer-section {
                display: flex;
                flex-direction: column;
                max-width: 350px;
                margin-bottom: 30px;
            }
            .logo-footer-header {
                display: flex;
                align-items: center;
                gap: 14px;
                margin-bottom: 10px;
            }
            .logo-footer-header img { width: 67px; height: 67px; }
            .logo-footer-header .baryo-tap2 { margin: 0; }
        }


        .main-area {
            flex: 1;
            overflow-x: hidden;
            padding: 20px;
            background-image: url('desktop_background.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        .profile-content-wrapper {
            width: 100%;
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 30px;
            min-height: 980px;
        }
        
        /* Profile Header Card */
        .profile-header-card {
            background: rgba(24, 118, 5, 1);
            border-radius: 20px;
            padding: 15px 25px;
            color: #ffffff;
            font-weight: 700;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .hello-cheryl-jane-p-geoman {
            font-family: sans-serif; 
            font-size: 32px;
        }
        .validate-button {
            background: #072201;
            border-radius: 20px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-family: sans-serif;
            font-size: 17px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            padding: 0 25px;
            transition: background-color 0.3s;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
        }
        .validate-button:hover { background: #256208; }
        
        /* User Details Section */
        .user-details-section {
            display: flex;
            gap: 20px;
            padding: 10px;
            align-items: flex-start;
        }

        .rectangle-62 {
            border-radius: 40px;
            border: 3px solid #187605;
            width: 236px;
            height: 240px;
            object-fit: cover;
            box-shadow: inset 0px 4px 4.6px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            flex-shrink: 0;
            margin-top: 0; 
        }

        .rectangle-64 {
            background: #D9EAD3;
            border-radius: 40px;
            border: 2px solid #187605;
            flex-grow: 1;
            height: 240px;
            padding: 30px 40px;
            display: grid;
            grid-template-columns: 1fr max-content;
            grid-template-rows: repeat(3, minmax(60px, auto));
            gap: 5px 30px;
            align-items: center;
            box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
        }

        .detail-group {
            display: block;
            margin-bottom: 5px;
            grid-column: 1 / 2;
        }

        .detail-label {
            color: #256208;
            font-family: sans-serif;
            font-size: 20px;
            font-weight: 700;
            display: block;
        }

        .detail-value {
            color: #220901;
            font-family: sans-serif;
            font-size: 20px;
            font-weight: 500;
            word-break: break-word;
            display: block; 
        }

        .profile-action-buttons { 
            grid-column: 2 / 3;
            grid-row: 1 / span 3;
            display: flex;
            flex-direction: column;
            gap: 15px;
            justify-content: center; 
        }

        .action-button {
            background: #072201;
            border-radius: 20px;
            height: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-family: sans-serif;
            font-size: 15px;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            padding: 0 20px;
            min-width: 146px;
            transition: background-color 0.3s;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
            border: none;
        }
        .action-button:hover { background: #187605; }
        
        /* Bottom Sections */
        .bottom-sections-wrapper {
            display: flex;
            gap: 20px;
            align-items: stretch;
            margin-top: 10px;
        }

        .section-box {
            background: #D9EAD3;
            border-radius: 40px;
            border: 3px solid #187605;
            flex: 1;
            padding: 25px;
            box-shadow: inset 0px 4px 4.6px 4px rgba(0, 0, 0, 0.25), 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
            display: flex;
            flex-direction: column;
            gap: 15px;
            height: fit-content;
        }

        .section-title {
            color: #256208;
            text-align: center;
            font-family: sans-serif;
            font-size: 30px;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .request-header {
            background: rgba(24, 118, 5, 1);
            border-radius: 10px;
            color: #ffffff;
            padding: 10px;
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr;
            text-align: center;
            font-size: 17px;
            font-weight: 600;
        }

        .request-item {
            background: #B9F8C2;
            border-radius: 10px;
            padding: 10px;
            margin-top: 10px;
            display: grid;
            grid-template-columns: 2fr 1.5fr 1fr;
            align-items: center;
            color: #220901;
            font-size: 17px;
            font-weight: 500;
        }
        .request-item > div {
            text-align: center;
            word-break: break-word;
        }
        .request-item .item-type { text-align: left; padding-left: 5px; }
        .request-item .item-status { font-weight: 700; }

        .progress-buttons-container {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: auto;
            padding-top: 20px; 
        }

        .progress-button {
            background: #072201;
            border-radius: 20px;
            height: 44px;
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-family: sans-serif;
            font-size: 13px;
            font-weight: 700;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s;
            box-shadow: 0 4px 4px rgba(0, 0, 0, 0.25);
            border: none;
        }
        .progress-button:hover { background: #187605; }
        
        .notification-item {
            background: #B9F8C2;
            border-radius: 10px;
            padding: 15px;
            color: #220901;
            font-family: sans-serif;
            font-size: 17px;
            font-weight: 500;
            height: auto;
        }

        /* OVERLAY STYLES */
        .overlay {
            display: none;
            position: fixed;
            z-index: 1000; 
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.6);
            justify-content: center;
            align-items: center;
            padding: 20px;
        }

        .overlay-content {
            background-color: #f3fff5;
            padding: 30px;
            border-radius: 20px;
            border: 3px solid #187605;
            width: 90%; 
            max-width: 650px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.5);
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .overlay-content h2 {
            color: #072201;
            font-family: sans-serif;
            font-size: 30px;
            font-weight: 800;
            margin-bottom: 25px;
            text-align: center;
        }

        .close-button {
            color: #187605;
            font-size: 40px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
            line-height: 1;
            transition: color 0.3s;
        }

        .close-button:hover,
        .close-button:focus {
            color: #072201;
        }

        #updateProfileForm, #changePasswordForm {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px 25px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            grid-column: span 1; 
        }

        #updateProfileForm .form-group:has(#email),
        #updateProfileForm .form-group:has(#fullAddress) {
            grid-column: span 2;
        }

        .form-group label {
            font-weight: 700;
            color: #256208;
            margin-bottom: 5px;
            font-size: 18px;
        }

        .form-group input[type="text"],
        .form-group input[type="email"],
        .form-group input[type="password"] {
            padding: 10px 15px;
            border: 2px solid #187605;
            border-radius: 10px;
            font-size: 16px;
            background-color: #fff;
        }

        .error-message {
            color: red;
            font-size: 14px;
            margin-top: 5px;
            font-weight: 600;
        }
        
        .password-input-container {
            position: relative;
            width: 100%;
        }

        .password-input-container input {
            width: 100%;
            padding-right: 45px;
        }

        .toggle-password {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            width: 24px;
            height: 24px;
            transition: opacity 0.2s;
        }

        .profile-upload-area {
            grid-column: span 2;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            margin-bottom: 15px;
        }

        .profile-update-img {
            width: 150px;
            height: 150px;
            border-radius: 50%;
            border: 4px solid #072201;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .profile-update-img:hover {
            transform: scale(1.05);
        }

        .upload-tip {
            font-size: 14px;
            color: #555;
            margin-top: 5px;
        }

        #idPreviewPic {
            width: 250px; 
            height: 150px; 
            border-radius: 10px; 
            border: 4px solid #187605;
            object-fit: cover;
            cursor: pointer;
        }

        .button-group {
            grid-column: span 2;
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 20px;
        }

        .button-group button {
            padding: 12px 25px;
            border-radius: 20px;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: background-color 0.3s;
            border: none;
            min-width: 150px;
        }

        .cancel-button {
            background: #ccc;
            color: #333;
        }
        .cancel-button:hover {
            background: #aaa;
        }

        .update-info-button {
            background: #187605;
            color: #ffffff;
        }
        .update-info-button:hover {
            background: #072201;
        }

        .loading {
            display: none;
            color: #187605;
            font-weight: 600;
            margin-top: 10px;
            text-align: center;
        }
        @media (max-width: 1024px) {
            .sidebar { width: 300px; padding: 15px; }
            .menu-item span { font-size: 18px; }
            .profile-content-wrapper { padding: 15px; }
            .footer-content-wrapper { flex-wrap: wrap; justify-content: space-around; }
            .rectangle-139 { height: auto; padding: 30px 20px; }
            .logo-footer-section, .explore-section, .services-section, .contact-section {
                flex-basis: 45%;
                margin-bottom: 25px;
                min-width: unset;
            }
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .sidebar { width: 350px; }
            .user-details-section { flex-direction: column; }
            .rectangle-62 { margin: 0 auto; }
            .rectangle-64 { 
                grid-template-columns: 1fr;
                grid-template-rows: auto;
            }
            .detail-group { grid-column: 1 / 2; }
            .profile-action-buttons { 
                grid-column: 1 / 2; 
                grid-row: auto; 
                flex-direction: row; 
                margin-top: 20px; 
                justify-content: space-around; 
            }
            .action-button { min-width: unset; flex: 1; max-width: 180px;}
        }

        @media (max-width: 1024px) {
            .sidebar { width: 300px; padding: 15px; }
            .menu-item span { font-size: 18px; }
            .profile-content-wrapper { padding: 15px; }
        }
        
        @media (max-width: 768px) {
            .layout { flex-direction: column; }
            .sidebar { width: 100%; min-height: auto; position: relative; }
            .profile-header-card { flex-direction: column; gap: 10px; }
            .hello-cheryl-jane-p-geoman { font-size: 24px; text-align: center; }
            
            .user-details-section { padding: 0; gap: 10px; flex-direction: column; }
            .rectangle-62 { width: 100px; height: 100px; }
            .rectangle-64 { padding: 15px; } 
            .profile-action-buttons { flex-direction: column; align-items: stretch; margin-top: 15px; }
            .action-button { max-width: 100%; }
            
            .bottom-sections-wrapper { flex-direction: column; }
            
            .request-header, .request-item {
                grid-template-columns: 1fr;
                text-align: left;
                padding: 10px;
                display: block;
            }
            .request-item > div { margin-bottom: 5px; text-align: left !important; }
            .progress-buttons-container { flex-direction: column; }
            .footer-content-wrapper { flex-direction: column; align-items: flex-start; }
            .logo-footer-section, .explore-section, .services-section, .contact-section { flex-basis: 100%; }
        }

        @media (max-width: 650px) {
            #updateProfileForm, #changePasswordForm {
                grid-template-columns: 1fr;
                gap: 15px;
            }
            .form-group {
                grid-column: span 1 !important;
            }
            .button-group {
                flex-direction: column;
                gap: 10px;
            }
            .button-group button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="layout">
<div class="sidebar">
    <div class="logo-area">
        <a href="main.php" style="text-decoration: none; display: flex; align-items: center; gap: 14px;">
            <img src="image/social-responsibility 1 (1).png" alt="Logo">
            <div class="logo-text">Baryo Tap</div>
        </a>
    </div>

    <a href="dreport.php" class="menu-item active">
        <img src="image/report-issue.png" alt="">
        <span>Report Issue</span>
    </a>
    <a href="VegetablePrices.html" class="menu-item">
        <img src="image/basket.png" alt="Prices">
        <span>Vegetable Prices</span>
    </a>
    <a href="contacts.php" class="menu-item active"> 
        <img src="image/medical-call.png" alt="Emergency">
        <span>Emergency Contact</span>
    </a>
    <a href="RequestDocu.php" class="menu-item">
        <img src="image/quote-request.png" alt="Document">
        <span>Request Document</span>
    </a>
    <a href="dlogin.html" class="menu-item">
        <img src="image/logout (2).png" alt="Logout">
        <span>Logout</span>
    </a>

    <div class="divider"></div>

    <a href="Profile.php" class="profile-link">
        <div class="profile">
            <img src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" alt="Profile" class="profile-img">
            
            <div class="profile-name"><?php echo htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name']); ?></div>
            
            <div class="profile-email"><?php echo htmlspecialchars($userData['Email']); ?></div>
        </div>
    </a>

    <div class="sidebar-footer">
        &copy; 2025 Baryo Tap.
    </div>
</div>
    <div class="main-area">
        <div class="profile-content-wrapper">

            <div class="profile-header-card">
                <div class="hello-cheryl-jane-p-geoman">HELLO, <?php echo strtoupper(htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name'])); ?></div>
                <button class="action-button validate-button" id="validateAccountButton">Validate Account</button>
            </div>

           <div class="user-details-section">
    <img class="rectangle-62" src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" alt="Profile Picture" />
    
    <div class="rectangle-64">
        
        <div class="detail-group">
            <span class="detail-label">Name</span>
            <span class="detail-value" id="mainProfileName"><?php echo htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name']); ?></span> 
        </div>
        
        <div class="profile-action-buttons">
            <button class="action-button" id="updateInfoButton">Update Info</button>
            <button class="action-button" id="changePasswordButton">Change Password</button>
        </div>

        <div class="detail-group">
             <span class="detail-label">Gmail</span>
             <span class="detail-value" id="mainProfileEmail"><?php echo htmlspecialchars($userData['Email']); ?></span> 
        </div>

        <div class="detail-group">
            <span class="detail-label">Address</span>
            <span class="detail-value" id="mainProfileAddress"><?php echo htmlspecialchars($userData['Full_Address']); ?></span> 
        </div>
    </div>
</div>

            <div class="bottom-sections-wrapper">

                <div class="section-box">
                    <div class="section-title">Request Progress</div>

                    <div class="request-header">
                        <div class="report-type">Report type</div>
                        <div class="date">Date</div>
                        <div class="status">Status</div>
                    </div>

                    <div class="request-item">
                        <div class="item-type">Street lights</div>
                        <div class="item-date">09-18-2025</div>
                        <div class="item-status">Pending</div>
                    </div>

                    <div class="request-item">
                        <div class="item-type">Barangay Residency</div>
                        <div class="item-date">09-20-2025</div>
                        <div class="item-status">For Pickup</div>
                    </div>
                    
                    <div class="progress-buttons-container">
                        <a href="Request docu.html" class="progress-button">Request New Document</a>
                        <a href="dreport.html" class="progress-button">Report New Issue</a>
                    </div>
                </div>

                <div class="section-box">
                    <div class="section-title">Notifications</div>
                    <div class="notification-item">Your report has been marked as **Resolved**.</div>
                    <div class="notification-item">New price update for September 21, 2025.</div>
                    <div class="notification-item">Your report is now under **review**.</div>
                    <div class="notification-item">Your document is **ready for pick up**.</div>
                </div>

            </div>
            
        </div>
    </div>
</div>

<div id="updateInfoOverlay" class="overlay">
    <div class="overlay-content">
        <h2>Update User Information</h2>
        <form id="updateProfileForm">
            
            <div class="profile-upload-area">
                <label for="profilePictureInput">
                    <img id="currentProfilePic" class="profile-update-img" src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" alt="Profile Picture"/>
                    <input type="file" id="profilePictureInput" accept="image/*" style="display: none;">
                    <p class="upload-tip">(Click image to edit)</p>
                </label>
            </div>

            <div class="form-group">
                <label for="firstName">First Name:</label>
                <input type="text" id="firstName" name="firstName" value="<?php echo htmlspecialchars($userData['First_Name']); ?>" required>
            </div>
            
            <div class="form-group">
                <label for="lastName">Last Name:</label>
                <input type="text" id="lastName" name="lastName" value="<?php echo htmlspecialchars($userData['Last_Name']); ?>" required>
            </div>
            
            <div class="form-group" style="grid-column: span 2;">
                <label for="email">Gmail Account:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($userData['Email']); ?>" required>
                <p id="emailError" class="error-message" style="display: none;">You must input `@gmail.com`</p>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="fullAddress">Full Address:</label>
                <input type="text" id="fullAddress" name="fullAddress" value="<?php echo htmlspecialchars($userData['Full_Address']); ?>" required>
            </div>
            <div class="button-group">
                <button type="button" id="cancelUpdate" class="cancel-button">Cancel</button>
                <button type="submit" class="update-info-button">Update Info</button>
            </div>
            <div class="loading" id="updateLoading">Updating profile...</div>
        </form>
    </div>
</div>

<div id="changePasswordOverlay" class="overlay">
    <div class="overlay-content">
        <h2>Change Password</h2>
        <form id="changePasswordForm">

            <div class="form-group" style="grid-column: span 2;">
                <label for="currentPassword">Current Password:</label>
                <div class="password-input-container">
                    <input type="password" id="currentPassword" name="currentPassword" required>
                    <img src="image/hidden.png" alt="Show Password" class="toggle-password" data-target="currentPassword">
                </div>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="newPassword">New Password:</label>
                <div class="password-input-container">
                    <input type="password" id="newPassword" name="newPassword" required>
                    <img src="image/hidden.png" alt="Show Password" class="toggle-password" data-target="newPassword">
                </div>
                <p id="newPasswordError" class="password-error-message" style="display: none;">Minimum of 8 characters</p>
            </div>

            <div class="form-group" style="grid-column: span 2;">
                <label for="confirmNewPassword">Confirm New Password:</label>
                <div class="password-input-container">
                    <input type="password" id="confirmNewPassword" name="confirmNewPassword" required>
                    <img src="image/hidden.png" alt="Show Password" class="toggle-password" data-target="confirmNewPassword">
                </div>
                <p id="confirmPasswordError" class="password-error-message" style="display: none;">Password don't match</p>
            </div>

            <div class="button-group">
                <button type="button" id="cancelChangePassword" class="cancel-button">Cancel</button>
                <button type="submit" class="update-info-button">Change Password</button>
            </div>
        </form>
    </div>
</div>

<footer class="rectangle-139">
    <div class="footer-content-wrapper">

        <div class="logo-footer-section">
            <div class="logo-footer-header">
                <div class="ellipse-20">
                    <img class="social-responsibility-3" src="image/social-responsibility 1 (1).png" alt="Logo" />
                </div>
                <div class="baryo-tap2">BARYO TAP</div>
            </div>
            <div class="a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu">
            A digital barangay service portal designed requests, and market price monitoring faster, transparent, and more accessible to all residents of Mantalongon, Dalaguete, Cebu.
        </div>
        </div>

        <div class="explore-section">
            <div class="explore">EXPLORE</div>
            <div class="submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk">
                Submit Report / Complaint<br>
                Request Barangay Documents<br>
                Market Price Analytics<br>
                Announcement and Updates<br>
                Resident Support Desk
            </div>
        </div>

        <div class="services-section">
            <div class="services">SERVICES</div>
            <div class="indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking">
                Indigency Certificate Request<br>
                Barangay Clearance Request<br>
                Business Permit Assistance<br>
                Community Concern Tracking
            </div>
        </div>

        <div class="contact-section">
            <div class="contact-us">CONTACT US</div>
            <div class="contact-column">
                <a href="#" class="contact-item contact-location">
                    <img src="image/location (2) 1.png" alt="Location"> <span>Mantalongon, Dalaguete, Cebu</span>
                </a>
                <a href="mailto:baryotap@gmail.com" class="contact-item contact-email">
                    <img src="image/mail 1.png" alt="Email"> <span>baryotap@gmail.com</span>
                </a>
                <a href="tel:09511312976" class="contact-item contact-phone">
                    <img src="image/telephone (1) 7.png" alt="Phone"> <span>09511312976</span>
                </a>
            </div>
        </div>
    </div>

<div id="validateAccountOverlay" class="overlay">
    <div class="overlay-content">
        <h2>Account Validation</h2>
        <form id="validateAccountForm" style="display: block; width: 100%;"> 
            
            <div class="profile-upload-area" style="width: 100%; margin-bottom: 25px;">
                <label for="validIdInput">
                    <img id="idPreviewPic" src="<?php echo htmlspecialchars($userData['validation_pic']); ?>" alt="ID Picture"/>
                    <input type="file" id="validIdInput" name="validId" accept="image/*" style="display: none;" required>
                    <p class="upload-tip" style="color: #072201; font-size: 16px;">
                        (Upload your Valid ID Picture. Only JPG/PNG accepted.)
                    </p>
                    <p id="idError" class="error-message" style="display: none;">Please upload a valid ID picture to proceed.</p>
                </label>
            </div>

            <div class="button-group" style="justify-content: center;">
                <button type="button" id="cancelValidation" class="cancel-button">Cancel</button>
                <button type="submit" class="update-info-button">Submit Validation</button>
            </div>
            <div class="loading" id="validationLoading">Submitting ID...</div>
        </form>
    </div>
</div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- 1. UPDATE INFO LOGIC ---
        const updateButton = document.getElementById('updateInfoButton');
        const updateInfoOverlay = document.getElementById('updateInfoOverlay');
        const cancelButton = document.getElementById('cancelUpdate');
        const emailInput = document.getElementById('email');
        const emailError = document.getElementById('emailError');
        const profilePictureInput = document.getElementById('profilePictureInput');
        const currentProfilePic = document.getElementById('currentProfilePic');
        const updateProfileForm = document.getElementById('updateProfileForm');
        const firstNameInput = document.getElementById('firstName');
        const lastNameInput = document.getElementById('lastName');
        const fullAddressInput = document.getElementById('fullAddress');
        const updateLoading = document.getElementById('updateLoading');
        
        // Main Content Element selectors
        const sidebarProfileName = document.querySelector('.profile-name');
        const sidebarProfileEmail = document.querySelector('.profile-email');
        const headerFullName = document.querySelector('.hello-cheryl-jane-p-geoman');
        const mainProfileName = document.getElementById('mainProfileName');
        const mainProfileEmail = document.getElementById('mainProfileEmail');
        const mainProfileAddress = document.getElementById('mainProfileAddress');
        const sidebarProfilePic = document.querySelector('.profile-img');
        const mainProfilePic = document.querySelector('.rectangle-62');


        function closeUpdateOverlay(e) {
            if (e) e.preventDefault();
            updateInfoOverlay.style.display = 'none'; 
        }

        if (updateButton && updateInfoOverlay) {
            updateButton.addEventListener('click', function(e) {
                e.preventDefault(); 
                updateInfoOverlay.style.display = 'flex'; 
                validateEmail();
            });
        }
        
        if (cancelButton) cancelButton.addEventListener('click', closeUpdateOverlay);

        function validateEmail() {
            const emailValue = emailInput.value.trim();
            if (!emailValue.endsWith('@gmail.com') || emailValue === '') {
                emailError.style.display = 'block';
            } else {
                emailError.style.display = 'none';
            }
        }
        
        if (emailInput) {
            emailInput.addEventListener('input', validateEmail);
            emailInput.addEventListener('blur', validateEmail); 
        }

        if (currentProfilePic && profilePictureInput) {
            profilePictureInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        currentProfilePic.src = e.target.result;
                    }
                    reader.readAsDataURL(file);
                }
            });
            currentProfilePic.addEventListener('click', () => profilePictureInput.click());
        }
        
        if (updateProfileForm) {
            updateProfileForm.addEventListener('submit', function(e) {
                e.preventDefault();
                validateEmail();
                
                if (emailError.style.display === 'block') {
                    alert('Please correct your Gmail account (it must include @gmail.com)');
                    return;
                }
                
                updateLoading.style.display = 'block';
                
                const formData = new FormData();
                formData.append('action', 'updateProfile');
                formData.append('firstName', firstNameInput.value);
                formData.append('lastName', lastNameInput.value);
                formData.append('email', emailInput.value);
                formData.append('fullAddress', fullAddressInput.value);
                
                // Handling picture upload (Base64 conversion for POST)
                if (profilePictureInput.files.length > 0) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        formData.append('profilePic', e.target.result);
                        sendUpdateRequest(formData);
                    }
                    reader.onerror = function(error) {
                         updateLoading.style.display = 'none';
                         alert('Error reading profile picture file.');
                    };
                    reader.readAsDataURL(profilePictureInput.files[0]);
                } else {
                    sendUpdateRequest(formData);
                }
            });
        }

        function sendUpdateRequest(formData) {
            fetch('Profile.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                     return response.text().then(text => {throw new Error('Server status not ok. Response: ' + text)});
                }
                return response.json();
            })
            .then(data => {
                updateLoading.style.display = 'none';
                if (data.success) {
                    const fullName = `${firstNameInput.value} ${lastNameInput.value}`;
                    
                    // Update all displayed elements
                    sidebarProfileName.textContent = fullName;
                    sidebarProfileEmail.textContent = emailInput.value;
                    headerFullName.textContent = `HELLO, ${fullName.toUpperCase()}`;
                    mainProfileName.textContent = fullName;
                    mainProfileEmail.textContent = emailInput.value;
                    mainProfileAddress.textContent = fullAddressInput.value;
                    
                    // Update pictures
                    sidebarProfilePic.src = currentProfilePic.src;
                    mainProfilePic.src = currentProfilePic.src;
                    
                    alert('Profile updated successfully!');
                    closeUpdateOverlay();
                } else {
                    alert('Error: ' + (data.message || 'Unknown error during update.'));
                }
            })
            .catch(error => {
                updateLoading.style.display = 'none';
                console.error('Fetch error:', error);
                alert('An error occurred: ' + error.message);
            });
        }
        
        // --- 2. CHANGE PASSWORD LOGIC (Frontend-only validation) ---
        const changePasswordButton = document.getElementById('changePasswordButton');
        const changePasswordOverlay = document.getElementById('changePasswordOverlay');
        const cancelChangePassword = document.getElementById('cancelChangePassword');
        const changePasswordForm = document.getElementById('changePasswordForm');
        const newPasswordInput = document.getElementById('newPassword');
        const confirmNewPasswordInput = document.getElementById('confirmNewPassword');
        const newPasswordError = document.getElementById('newPasswordError');
        const confirmPasswordError = document.getElementById('confirmPasswordError');
        const toggleButtons = document.querySelectorAll('#changePasswordOverlay .toggle-password');

        function closePasswordOverlay(e) {
            if (e) e.preventDefault();
            changePasswordOverlay.style.display = 'none';
            changePasswordForm.reset();
            newPasswordError.style.display = 'none';
            confirmPasswordError.style.display = 'none';
            // Reset input types and icons
            document.getElementById('currentPassword').type = 'password';
            document.getElementById('newPassword').type = 'password';
            document.getElementById('confirmNewPassword').type = 'password';
            document.querySelectorAll('#changePasswordOverlay .toggle-password').forEach(icon => {
                icon.src = 'image/hidden.png';
                icon.alt = 'Show Password';
            });
        }

        if (changePasswordButton && changePasswordOverlay) {
            changePasswordButton.addEventListener('click', function(e) {
                e.preventDefault();
                changePasswordOverlay.style.display = 'flex';
            });
        }

        if (cancelChangePassword) cancelChangePassword.addEventListener('click', closePasswordOverlay);

        function togglePasswordVisibility(e) {
            const iconElement = e.target;
            const targetId = iconElement.getAttribute('data-target');
            const targetInput = document.getElementById(targetId);

            if (targetInput.type === 'password') {
                targetInput.type = 'text';
                iconElement.src = 'image/eye 1.png'; // Assuming you have an 'eye 1.png' for visible
                iconElement.alt = 'Hide Password';
            } else {
                targetInput.type = 'password';
                iconElement.src = 'image/hidden.png';
                iconElement.alt = 'Show Password';
            }
        }

        toggleButtons.forEach(button => {
            button.addEventListener('click', togglePasswordVisibility);
        });

        function validatePasswordLength() {
            const passwordValue = newPasswordInput.value;
            if (passwordValue.length >= 8 || passwordValue.length === 0) {
                newPasswordError.style.display = 'none';
            } else {
                newPasswordError.style.display = 'block';
            }
            validatePasswordMismatch();
        }

        if (newPasswordInput) {
            newPasswordInput.addEventListener('input', validatePasswordLength);
        }
        
        function validatePasswordMismatch() {
            if (confirmNewPasswordInput.value.length > 0 && newPasswordInput.value !== confirmNewPasswordInput.value) {
                 confirmPasswordError.style.display = 'block';
            } else {
                 confirmPasswordError.style.display = 'none';
            }
        }
        
        if (confirmNewPasswordInput) {
             confirmNewPasswordInput.addEventListener('input', validatePasswordMismatch);
        }

        if (changePasswordForm) {
            changePasswordForm.addEventListener('submit', function(e) {
                e.preventDefault();
                validatePasswordLength();
                validatePasswordMismatch();

                if (newPasswordInput.value.length < 8) {
                    alert('Please ensure that the New Password has a minimum of 8 characters.');
                    return;
                }
                
                if (confirmPasswordError.style.display === 'block') {
                    alert('New Password and Confirm New Password do not match. Please check again.');
                    return;
                }
                
                if (document.getElementById('currentPassword').value.length === 0) {
                     alert('Please input Current Password.');
                     return;
                }
                
                // TODO: Add Server-side password change logic here if available
                alert('Password successfully changed! (Note: Server-side password update is not implemented in this file)');
                closePasswordOverlay(); 
            });
        }
        
        // --- 3. VALIDATE ACCOUNT LOGIC ---
        const validateAccountButton = document.getElementById('validateAccountButton');
        const validateAccountOverlay = document.getElementById('validateAccountOverlay');
        const cancelValidationButton = document.getElementById('cancelValidation');
        const validIdInput = document.getElementById('validIdInput');
        const idPreviewPic = document.getElementById('idPreviewPic');
        const validateAccountForm = document.getElementById('validateAccountForm');
        const idError = document.getElementById('idError');
        const validationLoading = document.getElementById('validationLoading');
        
        // Get the initial validation pic URL from PHP
        const initialValidationPic = '<?php echo htmlspecialchars($userData["validation_pic"]); ?>';

        function closeValidationOverlay(e) {
            if (e) e.preventDefault();
            validateAccountOverlay.style.display = 'none';
            validateAccountForm.reset();
            idPreviewPic.src = initialValidationPic; // Reset preview pic
            idError.style.display = 'none';
        }

        if (validateAccountButton && validateAccountOverlay) {
            validateAccountButton.addEventListener('click', function(e) {
                e.preventDefault();
                validateAccountOverlay.style.display = 'flex';
            });
        }
        
        if (cancelValidationButton) cancelValidationButton.addEventListener('click', closeValidationOverlay);

        if (idPreviewPic && validIdInput) {
            validIdInput.addEventListener('change', function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        idPreviewPic.src = e.target.result;
                        idError.style.display = 'none';
                    }
                    reader.readAsDataURL(file);
                }
            });
             idPreviewPic.addEventListener('click', () => validIdInput.click());
        }

        if (validateAccountForm) {
            validateAccountForm.addEventListener('submit', function(e) {
                e.preventDefault();

                if (validIdInput.files.length === 0) {
                    idError.style.display = 'block';
                    alert('Please upload a valid ID picture to proceed.');
                    return;
                }
                
                validationLoading.style.display = 'block';
                
                const reader = new FileReader();
                reader.onload = function(e) {
                    const formData = new FormData();
                    formData.append('action', 'uploadValidationID');
                    formData.append('idPic', e.target.result);

                    fetch('Profile.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                             return response.text().then(text => {throw new Error('Server status not ok. Response: ' + text)});
                        }
                        return response.json();
                    })
                    .then(data => {
                        validationLoading.style.display = 'none';
                        if (data.success) {
                            alert('Valid ID submitted successfully! Please wait for approval.');
                            // The PHP updates validation_pic, so the next page load will show the new pic
                            closeValidationOverlay();
                        } else {
                            alert('Error: ' + (data.message || 'Unknown error during ID submission.'));
                        }
                    })
                    .catch(error => {
                        validationLoading.style.display = 'none';
                        console.error('Fetch error:', error);
                        alert('An error occurred: ' + error.message);
                    });
                }
                reader.readAsDataURL(validIdInput.files[0]);
            });
        }
    });
</script>

</body>
</html>