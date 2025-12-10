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
    // Log this error instead of exposing it to the user in a production environment
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

// Default data in case the user is not found (though protected by session check)
$userData = [
    'First_Name' => 'Guest',
    'Last_Name' => 'User',
    'Email' => 'guest@baryotap.com',
    'Profile_Picture_URL' => 'image/default_profile.png' // Use a default image
];

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
}

$stmt->close();
$conn->close();

// Prepare variables for HTML output
$fullName = htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name']);
$email = htmlspecialchars($userData['Email']);
$profilePicUrl = htmlspecialchars($userData['Profile_Picture_URL']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        /* BASE & LAYOUT STYLES */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
            background: url(image/background.png) center/cover no-repeat fixed;
            width: 100vw; 
            overflow-x: hidden; 
        }
        /* The .layout container handles the side-by-side structure */
        .layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }
        
        /* ------------------------------------------- */
        /* SIDEBAR STYLES */
        /* ------------------------------------------- */
        .sidebar {
            width: 432px; 
            background: #f3fff5;
            padding: 25px 22px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            position: static; 
            z-index: 10;
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
            text-decoration: none; 
            color: #000; 
            background: transparent;
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .menu-item:hover { background: rgba(24, 118, 5, 0.35); transform: translateX(5px); }
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
            object-fit: cover; 
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
        
        /* ------------------------------------------- */
        /* MAIN CONTENT AREA STYLES */
        /* ------------------------------------------- */
        .main-area {
            padding: 40px 20px; 
            overflow-x: hidden; 
            background-color: transparent;
            width: 100%;
        }
        /* HEADER / SEARCH SECTION */
        .main-header-card {
            background-color: rgba(184, 247, 194, 0.9);
            border-radius: 20px; 
            box-shadow: 0px 4px 5px rgba(0, 0, 0, 0.2); 
            padding: 25px 30px; 
            margin-bottom: 30px;
            max-width: 750px; 
            margin-left: auto;
            margin-right: auto;
        }
        .header-title {
            font-family: "Inter", sans-serif;
            font-weight: 800;
            color: #000000;
            font-size: 26px; 
            text-align: center;
            margin-bottom: 15px;
        }
        .search-container {
            display: flex;
            justify-content: center;
            align-items: center; 
        }
        .search-input {
            width: 75%; 
            max-width: 500px;
            height: 40px; 
            background-color: #ffffff; 
            border-radius: 10px; 
            border: 1px solid #187605; 
            padding: 0 45px 0 20px;
            font-size: 17px; 
            outline: none;
        }
        .search-icon {
            width: 20px; 
            height: 20px;
            margin-left: -35px; 
            z-index: 5;
            pointer-events: none;
        }

        /* REQUEST CARD CONTAINER */
        .request-grid {
            display: flex;
            flex-direction: column;
            gap: 25px; 
            max-width: 750px; 
            margin: 0 auto;
        }

        /* INDIVIDUAL REQUEST CARD */
        .request-card {
            background-color: rgba(255, 255, 255, 0.95); 
            border-radius: 15px; 
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.1); 
            padding: 25px; 
            border: 2px solid #187605;
            display: flex;
            align-items: center; 
            position: relative;
        }

        .card-icon {
            width: 70px; 
            height: 70px;
            background-color: #187605; 
            border-radius: 50%; 
            display: flex;
            justify-content: center;
            align-items: center;
            flex-shrink: 0;
            margin-right: 20px;
        }
        .card-icon img {
            width: 35px; 
            height: 35px;
        }

        .card-details {
            flex-grow: 1;
            padding-right: 150px; 
        }
        .card-title {
            font-family: "Inter", sans-serif;
            font-weight: 700; 
            color: #072201; 
            font-size: 18px;
            margin-bottom: 5px;
        }
        .card-description {
            font-family: "Inter", sans-serif;
            font-weight: 500;
            color: #333;
            font-size: 13px;
        }
        
        .request-button {
            position: absolute;
            bottom: 25px;
            right: 25px;
            width: 120px; 
            height: 38px;
            background-color: #072201; 
            border-radius: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: "Inter", sans-serif;
            font-weight: 800;
            color: #ffffff;
            font-size: 14px;
            cursor: pointer;
            transition: background-color 0.2s, transform 0.2s;
            border: none; 
        }
        .request-button:hover {
            background-color: #187605; 
            transform: translateY(-2px);
        }

        /* SUPPORT CARD */
        .support-card {
            max-width: 750px;
            margin: 40px auto 0;
            background-color: rgba(184, 247, 194, 0.9); 
            border-radius: 20px; 
            box-shadow: 0px 4px 5px rgba(0, 0, 0, 0.2);
            padding: 30px 40px; 
            text-align: center;
        }
        .support-card .title {
            font-family: "Inter", sans-serif;
            font-weight: 700;
            color: #072201;
            font-size: 22px; 
            margin-bottom: 10px;
        }
        .support-card .description {
            font-family: "Inter", sans-serif;
            font-weight: 500;
            color: #333;
            font-size: 16px; 
            margin-bottom: 20px;
        }
        .contact-button {
            width: 200px; 
            height: 45px; 
            background-color: #187605; 
            border-radius: 10px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-family: "Inter", sans-serif;
            font-weight: 700;
            color: #ffffff; 
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        .contact-button:hover {
            background-color: #072201;
        }
        .contact-button img {
            width: 22px; 
            height: 22px;
        }

        /* ------------------------------------------- */
        /* FOOTER STYLES */
        /* ------------------------------------------- */
        .rectangle-139 {
            background: rgba(7, 34, 1, 0.95);
            width: 100%; 
            height: 300px;
            position: relative; 
            margin-top: 0; 
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
        }
        .contact-item img { width: 20px; height: 20px; flex-shrink: 0; }
        
        /* ------------------------------------------- */
        /* MODAL/OVERLAY STYLES (FIXED FOR SCROLLING) */
        /* ------------------------------------------- */
        .overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: none; /* Hidden by default */
            justify-content: center;
            z-index: 1000;
            overflow-y: auto; 
            padding: 20px 0; 
        }
        .modal {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            flex-shrink: 0; 
            margin: auto; 
        }
        .modal h2 {
            color: #187605;
            margin-bottom: 20px;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
            font-size: 26px;
            text-align: center;
        }
        .modal-body {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .form-group {
            display: flex;
            flex-direction: column;
        }
        .form-group label {
            font-size: 15px;
            color: #555;
            margin-bottom: 5px;
            font-weight: 600;
        }
        .form-group input, .form-group textarea {
            padding: 10px 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            width: 100%;
            resize: vertical;
        }
        .modal-footer {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
            margin-top: 25px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
        .modal-footer button {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.2s;
            font-size: 16px;
        }
        .cancel-btn { background: #ddd; color: #333; }
        .cancel-btn:hover { background: #ccc; }
        .submit-btn { background: #187605; color: white; }
        .submit-btn:hover { background: #0f4f03; }

        /* HIDDEN FIELD CONTAINER - Used for dynamic form fields */
        .hidden-field {
            display: none;
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
        
        <a href="dreport.php" class="menu-item">
            <img src="image/report-issue.png" alt="">
            <span>Report Issue</span>
        </a>
        <a href="VegetablePrices.html" class="menu-item">
            <img src="image/basket.png" alt="">
            <span>Vegetable Prices</span>
        </a>
        <a href="contacts.php" class="menu-item">
            <img src="image/medical-call.png" alt="">
            <span>Emergency Contact</span>
        </a>
        <a href="RequestDocu.php" class="menu-item active">
            <img src="image/quote-request.png" alt="">
            <span>Request Document</span>
        </a>
         <a href="dlogin.html" class="menu-item">
            <img src="image/logout (2).png" alt="">
            <span>Logout</span>
        </a>


        <div class="divider"></div>

        <a href="Profile.php" class="profile-link" style="text-decoration: none;">
            <div class="profile">
                <img 
                    src="<?php echo $profilePicUrl; ?>" 
                    alt="Profile" 
                    class="profile-img"
                    id="sidebar-profile-pic"
                >
                <div class="profile-name" id="sidebar-profile-name"><?php echo $fullName; ?></div>
                <div class="profile-email" id="sidebar-profile-email"><?php echo $email; ?></div>
            </div>
        </a>

        <div class="sidebar-footer">
            &copy; 2025 Baryo Tap.
        </div>
    </div>
    
    <div class="main-area">
        
        <div class="main-header-card">
            <div class="header-title">Request Document</div>
            <div class="search-container">
                <input type="text" class="search-input" placeholder="Search Documents">
                <img class="search-icon" src="image/search 1.png" alt="Search">
            </div>
        </div>
        
        <div class="request-grid">

            <div class="request-card">
                <div class="card-icon">
                    <img src="image/google-docs 1.png" alt="Doc Icon">
                </div>
                <div class="card-details">
                    <div class="card-title">Barangay Clearance</div>
                    <p class="card-description">Official clearance certificate from the barangay.</p>
                </div>
                <button class="request-button" data-document="Barangay Clearance">Request</button>
            </div>

            <div class="request-card">
                <div class="card-icon">
                    <img src="image/google-docs 1.png" alt="Doc Icon">
                </div>
                <div class="card-details">
                    <div class="card-title">Barangay Indigency</div>
                    <p class="card-description">Official certificate of indigency from the barangay.</p>
                </div>
                <button class="request-button" data-document="Barangay Indigency">Request</button>
            </div>
            
            <div class="request-card">
                <div class="card-icon">
                    <img src="image/google-docs 1.png" alt="Doc Icon">
                </div>
                <div class="card-details">
                    <div class="card-title">Certificate of Residency</div>
                    <p class="card-description">Proof of residence in the barangay.</p>
                </div>
                <button class="request-button" data-document="Certificate of Residency">Request</button>
            </div>

            <div class="request-card">
                <div class="card-icon">
                    <img src="image/google-docs 1.png" alt="Doc Icon">
                </div>
                <div class="card-details">
                    <div class="card-title">Barangay Business Permit</div>
                    <p class="card-description">Permit to operate a business in the barangay.</p>
                </div>
                <button class="request-button" data-document="Barangay Business Permit">Request</button>
            </div>
        </div>
        
        
    <div class="support-card">
            <div class="title">Need help?</div>
            <p class="description">
                Our team is here to assist you with your document requests. Contact us for any questions or concerns about the request process.
            </p>

            <div class="contact-button">
                <img src="image/telephone (1).png" alt="Phone">
                Contact Support
            </div>
        </div>
    </div>
    </div>
<footer class="rectangle-139">
    <div class="footer-content-wrapper">
      
      <div class="ellipse-20"></div>
      <img class="social-responsibility-2" src="image/social-responsibility 1 (1).png" />
      <div class="baryo-tap2">BARYO TAP</div>
      <div class="a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu">
          A digital barangay service portal designed requests, and market price monitoring faster, transparent, and more accessible to all residents of Mantalongon, Dalaguete, Cebu.
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
          <div class="contact-item contact-location">
              <img src="image/location (2) 1.png" alt="Location"> Mantalongon, Dalaguete, Cebu
          </div>
          <div class="contact-item contact-email">
              <img src="image/mail 1.png" alt="Email"> baryotap@gmail.com
          </div>
          <div class="contact-item contact-phone">
              <img src="image/telephone (1) 7.png" alt="Phone"> 09511312976
          </div>
      </div>
    </div>
</footer>

<div class="overlay" id="requestDocumentOverlay">
    <div class="modal">
        <h2 id="modalTitle">Request Document</h2>
        <form id="requestDocumentForm" action="request_progress.php" method="POST">
            <div class="modal-body">
                <div class="form-group">
                    <label for="fullName">Full Name</label>
                    <input type="text" id="fullName" name="fullName" required>
                </div>
                
                <div class="form-group" data-group-type="Address">
                    <label for="address">Complete Address</label>
                    <input type="text" id="address" name="address" required>
                </div>

                <div class="form-group hidden-field" data-group-type="Clearance_Purpose">
                    <label for="purpose">Purpose of Request</label>
                    <textarea id="purpose" name="purpose" rows="3" required></textarea>
                </div>
                
                <div class="form-group hidden-field" data-group-type="Indigency_Reason">
                    <label for="reason">Reason/Purpose (Medical, School, Assistance, etc.)</label>
                    <textarea id="reason" name="reason" rows="3" required></textarea>
                </div>

                <div class="form-group hidden-field" data-group-type="Residency_Years">
                    <label for="years">Years of Residency in Mantalongon</label>
                    <input type="number" id="years" name="years" min="1" max="100" required>
                </div>

                <div class="form-group hidden-field" data-group-type="Business_Name">
                    <label for="businessName">Business Name</label>
                    <input type="text" id="businessName" name="businessName" required>
                </div>

                <div class="form-group hidden-field" data-group-type="Business_Address">
                    <label for="businessAddress">Business Address</label>
                    <input type="text" id="businessAddress" name="businessAddress" required>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" class="cancel-btn" id="cancelRequest">Cancel</button>
                <button type="submit" class="submit-btn">Submit Request</button>
            </div>
            <input type="hidden" id="documentTypeInput" name="documentType">

        </form>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {

    // --- 1. PROFILE DATA PREFILL (No more localStorage needed as PHP handles it) ---
    const currentName = document.getElementById('sidebar-profile-name')?.textContent;
    const fullNameInput = document.getElementById('fullName'); 
    if (fullNameInput && currentName) {
        fullNameInput.value = currentName; // prefill full name from PHP
    }
    // --- END PROFILE PREFILL ---

    // --- 2. DOCUMENT REQUEST MODAL LOGIC ---
    const requestButtons = document.querySelectorAll('.request-button');
    const overlay = document.getElementById('requestDocumentOverlay');
    const modalTitle = document.getElementById('modalTitle');
    const requestForm = document.getElementById('requestDocumentForm');
    const cancelButton = document.getElementById('cancelRequest');
    const allFormGroups = requestForm.querySelectorAll('.form-group');
    const fullNameLabel = requestForm.querySelector('.form-group:first-child label');

    function closeModal() {
        overlay.style.display = 'none';
        requestForm.reset(); 
        // Re-prefill name after reset
        if (fullNameInput && currentName) {
            fullNameInput.value = currentName;
        }
        if (fullNameLabel) fullNameLabel.textContent = "Full Name";
    }

    requestButtons.forEach(button => {
        button.addEventListener('click', function() {
            const documentType = this.getAttribute('data-document');
            if (!documentType) return;

            modalTitle.textContent = `Request: ${documentType}`;

            // Hide all fields first
            allFormGroups.forEach(group => {
                group.classList.add('hidden-field');
                group.querySelector('input, textarea')?.removeAttribute('required');
            });

            fullNameLabel.textContent = "Full Name";

            const fullNameField = requestForm.querySelector('.form-group:first-child');
            const addressField = requestForm.querySelector('[data-group-type="Address"]');

            // Show full name (always required)
            fullNameField.classList.remove('hidden-field');
            fullNameField.querySelector('input, textarea')?.setAttribute('required', 'required');

            // Show general address
            addressField.classList.remove('hidden-field');
            addressField.querySelector('input, textarea')?.setAttribute('required', 'required');
            addressField.querySelector('label').textContent = 'Complete Address';

            let specificFields = [];

            if (documentType === 'Barangay Clearance') {
                specificFields = [
                    requestForm.querySelector('[data-group-type="Clearance_Purpose"]')
                ];
            } else if (documentType === 'Barangay Indigency') {
                specificFields = [
                    requestForm.querySelector('[data-group-type="Indigency_Reason"]')
                ];
                addressField.querySelector('label').textContent = 'Address';
            } else if (documentType === 'Certificate of Residency') {
                specificFields = [
                    requestForm.querySelector('[data-group-type="Residency_Years"]')
                ];
            } else if (documentType === 'Barangay Business Permit') {
                addressField.classList.add('hidden-field');
                addressField.querySelector('input, textarea')?.removeAttribute('required');
                fullNameLabel.textContent = "Owner's Full Name";

                specificFields = [
                    requestForm.querySelector('[data-group-type="Business_Name"]'),
                    requestForm.querySelector('[data-group-type="Business_Address"]')
                ];
            }

            specificFields.forEach(field => {
                if (field) {
                    field.classList.remove('hidden-field');
                    field.querySelector('input, textarea')?.setAttribute('required', 'required');
                }
            });

            requestForm.setAttribute('data-document-type', documentType);
            overlay.style.display = 'flex';
            overlay.scrollTop = 0; 
        });
    });

    if (cancelButton) {
        cancelButton.addEventListener('click', closeModal);
    }

    overlay.addEventListener('click', function(e) {
        if (e.target === overlay) closeModal();
    });

    // --- FORM SUBMISSION FOR PHP ---
    if (requestForm) {
        requestForm.addEventListener('submit', function(e) {
            // REMOVE e.preventDefault() so form submits normally

            // Optionally, you can set a hidden field for document type before submission
            const documentType = this.getAttribute('data-document-type');
            let docTypeInput = document.getElementById('documentTypeInput');
            if (!docTypeInput) {
                docTypeInput = document.createElement('input');
                docTypeInput.type = 'hidden';
                docTypeInput.name = 'documentType';
                docTypeInput.id = 'documentTypeInput';
                this.appendChild(docTypeInput);
            }
            docTypeInput.value = documentType;
        });
    }

    // --- 3. REAL-TIME SEARCH FILTER LOGIC ---
    const searchInput = document.querySelector('.search-input');
    const requestCards = document.querySelectorAll('.request-card');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            requestCards.forEach(card => {
                const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.card-description')?.textContent.toLowerCase() || '';
                card.style.display = (title.includes(searchTerm) || description.includes(searchTerm)) ? 'flex' : 'none';
            });
        });
    }

});
</script>
</body>
</html>