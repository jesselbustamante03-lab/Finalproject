<?php
session_start(); 

// --- Database Connection Configuration ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "baryotap";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 1. Ensure the user is logged in
if (!isset($_SESSION['User_ID'])) {
    // Redirect to login page if session is not set
    header("Location: dlogin.html");
    exit();
}

$userId = $_SESSION['User_ID']; 

// 2. Fetch User Data
// Ensure your 'users' table has the columns: First_Name, Last_Name, Email, Profile_Picture_URL
$query = "SELECT First_Name, Last_Name, Email, Profile_Picture_URL FROM users WHERE User_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$userData = [
    'First_Name' => 'Guest',
    'Last_Name' => 'User',
    'Email' => 'guest@baryotap.com',
    'Profile_Picture_URL' => 'image/0a79fb93-911d-4960-81ab-23adab0223cb.jpg' // Default placeholder
];

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
}

$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency Contact - Baryo Tap</title>
    <style>
        /* (Your existing CSS styles remain here) */
        /* General styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
            /* Using a generic placeholder image URL for background, replace with your actual path */
            background: url(image/background.png) center/cover no-repeat fixed;
        }

        /* The .layout container handles the side-by-side structure */
        .layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* LEFT SIDEBAR - Stays fixed to the left and runs full height */
        .sidebar {
            width: 432px;
            background: #f3fff5;
            padding: 25px 22px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            position: static; 
            top: auto;
            left: auto;
            z-index: auto; 
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
            text-decoration: none; /* Added for anchor tags */
            color: inherit; /* Added for anchor tags */
        }
        .menu-item:hover { background: rgba(24, 118, 5, 0.35); transform: translateX(5px); }
        /* Active menu item for Emergency Contact */
        .menu-item.active { 
            background: rgba(24, 118, 5, 0.35); 
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
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

        /* PROFILE */
        .profile {
            text-align: center;
            margin-top: auto; 
            padding-bottom: 15px; 
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

        /* RIGHT AREA */
        .main-area {
            flex: 1;
            margin-left: 0; 
            overflow-x: hidden;
            padding: 50px;
        }
        
        /* CONTACTS specific styles for the main content card */
        .contacts-card {
            width: 100%;
            max-width: 600px; 
            margin: 0 auto;
            /* Background and radius from the image structure */
            background: rgba(135, 203, 142, 0.9); 
            border-radius: 30px;
            padding: 30px 20px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
        }
        
        .contacts-title {
            font-size: 28px;
            font-weight: 700;
            text-align: center;
            margin-bottom: 25px;
            color: #072201; 
        }
        
        /* Search Bar Styles */
        .search-container {
            position: relative;
            margin-bottom: 25px;
            padding: 10px; /* Padding for the entire search area */
            background-color: #fff; /* White background for the search box container */
            border-radius: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: flex;
            align-items: center;
        }
        .search-input {
            flex-grow: 1;
            padding: 0;
            border: none;
            background: none;
            font-size: 18px;
            color: #333;
            /* Remove default focus outline */
            outline: none; 
            margin-left: 10px;
        }
        /* Style the placeholder text */
        .search-input::placeholder {
            color: #999;
        }

        .search-icon-img {
            width: 24px;
            height: 24px;
            cursor: pointer;
            flex-shrink: 0;
        }
        /* Contact List Item Styles */
        .contact-list-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 15px;
            margin-bottom: 8px; 
            border-radius: 12px;
            background: rgba(220, 255, 220, 0.95); 
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: background 0.2s;
        }
        .contact-list-item:hover {
            background: rgba(200, 255, 200, 1);
        }
        
        .contact-details {
            display: flex;
            align-items: center;
            gap: 15px;
            flex-grow: 1;
        }

        .contact-icon-container {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            overflow: hidden;
            flex-shrink: 0;
            background-color: #fff; 
            border: 1px solid #ddd;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .contact-icon {
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 5px; 
        }

        .contact-info {
            display: flex;
            flex-direction: column;
        }

        .contact-name {
            font-size: 18px;
            font-weight: 600;
            color: #072201; 
            line-height: 1.2;
        }
        
        .contact-number-link { 
            font-size: 16px;
            font-weight: 700;
            color: #187605; 
            text-decoration: none;
            line-height: 1.2;
            transition: color 0.3s;
        }
        .contact-number-link:hover {
            color: #000;
        }
        
        .contact-actions {
            display: flex;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .action-icon {
            width: 28px; 
            height: 28px;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .action-icon:hover {
            opacity: 0.7;
        }
        .search-icon { display: none; }


        /* FULL-WIDTH FOOTER STYLES (Keep existing) */
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
            text-decoration: none; 
        }
        .contact-item img { width: 20px; height: 20px; flex-shrink: 0; }
        
        /* Responsive adjustments for smaller screens */
        @media (max-width: 1400px) {
            .footer-content-wrapper {
                min-width: 100%;
                padding: 0 20px; 
                display: flex;
                flex-wrap: wrap;
                justify-content: space-around;
            }
            .rectangle-139 {
                height: auto; 
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

        @media (max-width: 1024px) {
            .sidebar {
                width: 300px;
                padding: 15px;
            }
            .menu-item span {
                font-size: 18px;
            }
            .main-area {
                padding: 20px;
            }
            .contacts-card {
                padding: 40px;
            }
            .contacts-title {
                font-size: 32px;
                margin-bottom: 30px;
            }
            .contact-name, .contact-number-link {
                font-size: 20px;
            }
        }
        @media (max-width: 768px) {
            .layout {
                flex-direction: column;
            }
            .sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
                padding: 20px;
            }
            .menu-item {
                margin-bottom: 15px;
            }
            .divider {
                margin: 20px 0;
            }
            .profile {
                margin-top: 20px;
                padding-bottom: 0;
            }
            .main-area {
                width: 100%;
                padding: 15px;
            }
            .contacts-card {
                padding: 25px;
                max-width: 100%; 
            }
            .contact-name {
                font-size: 16px; 
            }
            .contact-number-link {
                font-size: 14px;
            }
            .contact-actions {
                gap: 5px;
            }
            .action-icon {
                width: 20px;
                height: 20px;
            }
        }
        /* --- Custom CSS for the Call and Messaging Overlays --- */
        .overlay {
            /* This is for Call/Message overlays */
            display: none; 
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000; 
        }

        .box {
            /* This is for Call/Message overlays */
            background: white;
            border-radius: 10px;
            width: 80%;
            max-width: 500px;
            padding: 20px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .box h3 {
            margin-bottom: 20px;
            font-family: "Outfit-Medium", sans-serif;
        }

        /* Messaging Specific Styles */
        .message-box textarea {
            width: 100%;
            height: 150px;
            margin-bottom: 15px;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: "Inter-Regular", sans-serif;
            font-size: 16px;
            resize: none;
        }

        /* Call Specific Styles */
        .call-status {
            font-size: 1.5em;
            font-weight: bold;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        .controls {
            display: flex;
            justify-content: center;
        }

        .controls button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            transition: background-color 0.3s;
            margin: 0 10px;
        }

        #cancelMessage, #cancelCall {
            background-color: #f44336; /* Red */
            color: white;
        }

        #sendMessage {
            background-color: #4CAF50; /* Green */
            color: white;
        }

        #startCall {
            background-color: #007bff; /* Blue for Call initiation */
            color: white;
        }

        /* Styling for disabled state */
        #startCall:disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }
        
        /* NEW: LOGOUT MODAL STYLES (MATCHING report.html) */
        .modal { 
            display: none; 
            position: fixed; 
            z-index: 1000; 
            left: 0; 
            top: 0; 
            width: 100%; 
            height: 100%; 
            background-color: rgba(0,0,0,0.5); 
        }
        .modal-content { 
            background-color: #fff; 
            /* Same as report.html */
            margin: 15% auto; 
            padding: 30px; 
            border-radius: 20px; 
            max-width: 400px; 
            text-align: center; 
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .modal-content p { 
            font-size: 20px; 
            margin-bottom: 30px; 
            font-family: "Inter-Regular", sans-serif; /* Added font-family for consistency */
        }
        .modal-buttons {
            display: flex; 
            justify-content: center;
        }
        .modal-buttons button { 
            padding: 10px 25px; 
            margin: 0 10px; 
            font-size: 18px; 
            font-weight: 600; 
            border-radius: 12px; 
            border: none; 
            cursor: pointer;
        }
        /* Color matching report.html */
        #cancelBtn { background-color: #bbb; color: #000; } 
        #confirmBtn { background-color: #072201; color: #fff; }
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

    <a href="dreport.php" class="menu-item" style="text-decoration: none;">
        <img src="image/report-issue.png"  alt="">
        <span>Report Issue</span>
    </a>
            <a href="VegetablePrices.html" class="menu-item" style="text-decoration: none;">
        <img src="image/basket.png"   alt="">
        <span>Vegetable Prices</span>
    </a>
        <a href="contacts.php" class="menu-item active" style="text-decoration: none;">
        <img src="image/medical-call.png"  alt="">
        <span>Emergency Contact</span>
    </a>
     <a href="RequestDocu.php" class="menu-item" style="text-decoration: none;">
        <img src="image/quote-request.png"  alt="">
        <span>Request Document</span>
    </a>
       
        <a href="dlogin.html" class="menu-item">
        <img src="image/logout (2).png" alt="Logout">
        <span>Logout</span>
    </a>

        <div class="divider"></div>

       <div class="profile">
            <a href="Profile.php" style="text-decoration: none; color: inherit;">
                <img src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" 
                    alt="Profile" 
                    class="profile-img"
                >
                
                <div class="profile-name">
                    <?php echo htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name']); ?>
                </div>
                
                <div class="profile-email">
                    <?php echo htmlspecialchars($userData['Email']); ?>
                </div>
            </a>
        </div>


        <div class="sidebar-footer">
            &copy; 2025 Baryo Tap.
        </div>
    </div>

    <div class="main-area">
        <div class="contacts-card">
            <h1 class="contacts-title">Contacts</h1>
            
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search" class="search-input">
                <img class="search-icon-img" src="image/search 1.png" alt="Search">
            </div>

            <div class="contact-list-container" id="contactListContainer">
                
                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/cebeco.png" alt="CEBECO 1 Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">CEBECO 1</span>
                            <a href="tel:09399135136" class="contact-number-link">09399135136</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09399135136" data-recipient="CEBECO 1" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09399135136" data-recipient="CEBECO 1" alt="Message">
                    </div>
                </div>

                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/Philippine_Coast_Guard_(PCG).svg.png" alt="PCG Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">PCG</span>
                            <a href="tel:09603268566" class="contact-number-link">09603268566</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09603268566" data-recipient="PCG" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09603268566" data-recipient="PCG" alt="Message">
                    </div>
                </div>

                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/police.png" alt="PNP Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">PNP Dalaguete Desk Officer</span>
                            <a href="tel:09238704534" class="contact-number-link">09238704534</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09238704534" data-recipient="PNP Dalaguete Desk Officer" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09238704534" data-recipient="PNP Dalaguete Desk Officer" alt="Message">
                    </div>
                </div>

                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/drrmo.png" alt="DDRMMO Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">DDRMMO</span>
                            <a href="tel:09317032223" class="contact-number-link">09317032223</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09317032223" data-recipient="DDRMMO" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09317032223" data-recipient="DDRMMO" alt="Message">
                    </div>
                </div>

                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/dalaslogo.png" alt="LGU Tourism Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">LGU Tourism</span>
                            <a href="tel:09477076048" class="contact-number-link">09477076048</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09477076048" data-recipient="LGU Tourism" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09477076048" data-recipient="LGU Tourism" alt="Message">
                    </div>
                </div>

                <div class="contact-list-item">
                    <div class="contact-details">
                        <div class="contact-icon-container">
                            <img class="contact-icon" src="image/Bureau_of_Fire_Protection.png" alt="BFP Logo">
                        </div>
                        <div class="contact-info">
                            <span class="contact-name">BFP</span>
                            <a href="tel:09618189728" class="contact-number-link">09618189728</a>
                        </div>
                    </div>
                    <div class="contact-actions">
                        <img class="action-icon" src="image/telephone (1).png" data-call-target="true" data-number="09618189728" data-recipient="BFP" alt="Call">
                        <img class="action-icon" src="image/chat.png" data-chat-target="true" data-number="09618189728" data-recipient="BFP" alt="Message">
                    </div>
                </div>

            </div>

        </div>

    </div>
</div>

<div id="messagingOverlay" class="overlay">
    <div id="messageBox" class="box message-box">
        <h3 id="messageRecipient">Send Message</h3>
        <textarea id="messageInput" placeholder="Enter your message here..."></textarea>
        <div class="controls">
            <button id="cancelMessage">Cancel</button>
            <button id="sendMessage" data-number="">Send</button>
        </div>
    </div>
</div>

<div id="callOverlay" class="overlay">
    <div id="callBox" class="box call-box">
        <h3 id="callRecipient">Call Contact</h3>
        <p id="callStatus" class="call-status">Ready to Call</p>
        <div class="controls">
            <button id="cancelCall">Cancel</button>
            <button id="startCall" data-number="">Call</button>
        </div>
    </div>
</div>

<div id="logoutModal" class="modal">
  <div class="modal-content">
    <p>Are you sure you want to log out?</p>
    <div class="modal-buttons">
      <button id="cancelBtn">Cancel</button>
      <button id="confirmBtn">Logout</button>
    </div>
  </div>
</div>
<footer class="rectangle-139">
  <div class="footer-content-wrapper">
    
    <div class="logo-footer-section">
        <div class="logo-footer-header">
            <div class="ellipse-20"></div>
            <img class="social-responsibility-2" src="image/social-responsibility 1 (1).png" alt="Logo" />
            <div class="baryo-tap2">BARYO TAP</div>
        </div>
        <div class="a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu">
            A digital barangay service portal designed requests, and market price monitoring faster, transparent, and more accessible to all residents of Mantalongon, Dalaguete, Cebu.
        </div>
    </div>
    
    <div class="explore">EXPLORE</div>
    <div class="submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk">
        Submit Report / Complaint<br>
        Request Barangay Documents<br>
        Market Price Analytics<br>
        Announcement and Updates<br>
        Resident Support Desk
    </div>

    <div class="services">SERVICES</div>
    <div class="indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking">
        Indigency Certificate Request<br>
        Barangay Clearance Request<br>
        Business Permit Assistance<br>
        Community Concern Tracking
    </div>

    <div class="contact-us">CONTACT US</div>

    <div class="contact-column">
        <a href="#" class="contact-item contact-location">
            <img src="image/location (2) 1.png" alt="Location"> Mantalongon, Dalaguete, Cebu
        </a>
        <a href="mailto:baryotap@gmail.com" class="contact-item contact-email">
            <img src="image/mail 1.png" alt="Email"> baryotap@gmail.com
        </a>
        <a href="tel:09511312976" class="contact-item contact-phone">
            <img src="image/telephone (1) 7.png" alt="Phone"> 09511312976
        </a>
    </div>
  </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', () => {
    // --- Search Variables ---
    const searchInput = document.getElementById('searchInput');
    const contactItems = document.querySelectorAll('.contact-list-item');

    // --- Messaging Variables ---
    const messagingOverlay = document.getElementById('messagingOverlay');
    const messageRecipient = document.getElementById('messageRecipient');
    const messageInput = document.getElementById('messageInput');
    const cancelButton = document.getElementById('cancelMessage');
    const sendButton = document.getElementById('sendMessage');
    const chatIcons = document.querySelectorAll('[data-chat-target]');

    // --- Call Variables ---
    const callOverlay = document.getElementById('callOverlay');
    const callRecipient = document.getElementById('callRecipient');
    const callStatus = document.getElementById('callStatus');
    const cancelCallButton = document.getElementById('cancelCall');
    const startCallButton = document.getElementById('startCall');
    const callIcons = document.querySelectorAll('[data-call-target]');

    let callTimer;

    // --- Logout Variables (UPDATED IDs to match report.html) ---
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    // Using the new IDs from the HTML modal structure
    const cancelLogoutBtn = document.getElementById('cancelBtn'); 
    const confirmLogoutBtn = document.getElementById('confirmBtn');


    // --- Utility Functions ---
    const closeMessagingOverlay = () => {
        messagingOverlay.style.display = 'none';
        messageInput.value = '';
        sendButton.dataset.number = '';
    };

    const closeCallOverlay = () => {
        callOverlay.style.display = 'none';
        callRecipient.textContent = 'Call Contact';
        callStatus.textContent = 'Ready to Call';
        callStatus.style.color = '#4CAF50';
        startCallButton.dataset.number = '';
        startCallButton.disabled = false;
        startCallButton.textContent = 'Call';
        clearTimeout(callTimer);
    };

    // --- Search Filter ---
    const filterContacts = () => {
        const searchTerm = searchInput.value.toLowerCase();

        contactItems.forEach(item => {
            const nameElement = item.querySelector('.contact-name');
            const numberElement = item.querySelector('.contact-number-link');

            if (nameElement && numberElement) {
                const name = nameElement.textContent.toLowerCase();
                const number = numberElement.textContent.toLowerCase();

                if (name.includes(searchTerm) || number.includes(searchTerm)) {
                    item.style.display = 'flex';
                } else {
                    item.style.display = 'none';
                }
            }
        });
    };

    searchInput.addEventListener('keyup', filterContacts);
    searchInput.addEventListener('change', filterContacts);

    // --- Call Icon Handlers ---
    callIcons.forEach(icon => {
        icon.addEventListener('click', (e) => {
            const number = e.currentTarget.dataset.number;
            const recipient = e.currentTarget.dataset.recipient;

            if (number && recipient) {
                closeCallOverlay();
                callRecipient.textContent = `Call ${recipient} (${number})`;
                startCallButton.dataset.number = number;
                callOverlay.style.display = 'block';
            } else {
                alert('Contact information is incomplete.');
            }
        });
    });

    cancelCallButton.addEventListener('click', closeCallOverlay);

    startCallButton.addEventListener('click', () => {
        if (startCallButton.disabled) return;

        const recipientText = callRecipient.textContent;
        const recipientMatch = recipientText.match(/Call (.*?) \(/);
        const recipientName = recipientMatch ? recipientMatch[1] : 'Contact';

        startCallButton.disabled = true;
        startCallButton.textContent = 'Dialing...';

        callStatus.textContent = `Calling ${recipientName}... (Ringing)`;
        callStatus.style.color = '#FFA500';

        callTimer = setTimeout(() => {
            callStatus.textContent = `Call to ${recipientName} ended.`;
            callStatus.style.color = '#F44336';
            startCallButton.textContent = 'Call Again';
            startCallButton.disabled = false;
        }, 3000);
    });

    // --- Messaging Handlers ---
    cancelButton.addEventListener('click', closeMessagingOverlay);

    chatIcons.forEach(icon => {
        icon.addEventListener('click', (e) => {
            const number = e.currentTarget.dataset.number;
            const recipient = e.currentTarget.dataset.recipient;

            if (number && recipient) {
                messageRecipient.textContent = `Send Message to ${recipient}`;
                sendButton.dataset.number = number;
                messagingOverlay.style.display = 'block';
                messageInput.value = '';
                messageInput.focus();
            } else {
                alert('Contact information is incomplete.');
            }
        });
    });

    sendButton.addEventListener('click', () => {
        const messageText = messageInput.value.trim();
        const recipient = messageRecipient.textContent.replace('Send Message to ', '');

        if (!messageText) {
            alert('Please enter a message before sending.');
            return;
        }

        alert(`Message sent to ${recipient} successfully! (Simulated)`);
        closeMessagingOverlay();
    });

    // --- Logout Confirmation (UPDATED TO USE report.html LOGIC AND IDs) ---
    logoutBtn.addEventListener('click', () => {
        logoutModal.style.display = 'block';
    });

    cancelLogoutBtn.addEventListener('click', () => {
        logoutModal.style.display = 'none';
    });

    confirmLogoutBtn.addEventListener('click', () => {
        // Redirect to login.html, same as report.html
        window.location.href = "login.html"; 
    });

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if(e.target === logoutModal){
            logoutModal.style.display = 'none';
        }
    });

});

</script>
</body>
</html>