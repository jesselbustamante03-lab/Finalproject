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

// 2. Fetch User Data AND Validation Status
// Note the inclusion of 'Validation_Status'
$query = "SELECT First_Name, Last_Name, Email, Profile_Picture_URL, Validation_Status FROM users WHERE User_ID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

$userData = [
    'First_Name' => 'Guest',
    'Last_Name' => 'User',
    'Email' => 'guest@baryotap.com',
    'Profile_Picture_URL' => 'image/0a79fb93-911d-4960-81ab-23adab0223cb.jpg', // Default placeholder
    'Validation_Status' => 'Pending' // Default status
];

if ($result->num_rows > 0) {
    $userData = $result->fetch_assoc();
}

$stmt->close();
$conn->close();

$isVerified = ($userData['Validation_Status'] === 'Verified');
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
    /* KINI ANG DUGANG ARON MAWALA ANG HORIZONTAL SCROLL */
    width: 100vw; /* I-set ang width sa 100% viewport width */
    overflow-x: hidden; /* Itago ang horizontal scrollbar */
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
    /* Pwede gamay ra ang padding para dili mosobra ang width */
    padding: 40px 20px; 
    overflow-x: hidden; /* Siguraduhon nga naka-hidden gihapon */
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
            background-color: rgba(184, 247, 194, 0.9); /* Same as header background */
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
            height: 45px; /* Taller button */
            background-color: #187605; /* Solid green button */
            border-radius: 10px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            font-family: "Inter", sans-serif;
            font-weight: 700;
            color: #ffffff; /* White text for contrast */
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
            overflow-y: auto; /* FIX: ALLOWS THE OVERLAY TO SCROLL */
            padding: 20px 0; /* FIX: ADDS SPACE SO MODAL ISN'T FLUSH WITH EDGES */
        }
        .modal {
            background: white;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
            position: relative;
            flex-shrink: 0; /* FIX: PREVENTS MODAL FROM BEING SQUASHED */
            margin: auto; /* FIX: CENTERS MODAL VERTICALLY WHEN SMALL, ALLOWS FLOW WHEN TALL */
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

        <a href="dreport.php" class="menu-item" style="text-decoration: none;">
            <img src="image/report-issue.png" alt="">
            <span>Report Issue</span>
        </a>
        <a href="VegetablePrices.html" class="menu-item" style="text-decoration: none;">
            <img src="image/basket.png" alt="">
            <span>Vegetable Prices</span>
        </a>
        <a href="contacts.php" class="menu-item" style="text-decoration: none;">
            <img src="image/medical-call.png" alt="">
            <span>Emergency Contact</span>
        </a>
        <a href="Request docu.php" class="menu-item active" style="text-decoration: none;">
            <img src="image/quote-request.png" alt="">
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
        <h1 class="main-title">Request Barangay Documents</h1>
        
        <div id="validationBanner">
            <?php if ($userData['Validation_Status'] === 'Pending'): ?>
                ⚠️ Your account is currently **Pending Verification**. Please wait for the Barangay to approve your ID to submit a request.
            <?php elseif ($userData['Validation_Status'] === 'Denied'): ?>
                ❌ Your account verification was **Denied**. Please re-upload a valid ID on your <a href="Profile.php" style="color: #fff; text-decoration: underline;">Profile Page</a> to submit a request.
            <?php endif; ?>
        </div>
        
        <div class="search-container">
            <input type="text" placeholder="Search for a document type (e.g., Clearance, Indigency)" class="search-input">
            <img class="search-icon-img" src="image/search 1.png" alt="Search">
        </div>
        
        <div class="request-grid">
            
            <div class="request-card">
                <div class="card-header">
                    <img class="card-icon" src="image/certificate 1.png" alt="Barangay Clearance">
                    <span class="card-title">Barangay Clearance</span>
                </div>
                <p class="card-description">
                    Required for job applications, school enrollment, and various legal transactions. Must be a registered resident.
                </p>
                <form class="request-form" action="document_submission.php" method="POST" data-document-type="Barangay Clearance">
                    <input type="hidden" name="documentType" value="Barangay Clearance">
                    <button type="submit" class="request-button" <?php echo $isVerified ? '' : 'disabled'; ?>>
                        Request Clearance
                    </button>
                </form>
            </div>

            <div class="request-card">
                <div class="card-header">
                    <img class="card-icon" src="image/poor 1.png" alt="Certificate of Indigency">
                    <span class="card-title">Certificate of Indigency</span>
                </div>
                <p class="card-description">
                    Issued to financially challenged residents for specific purposes like medical assistance or waived fees.
                </p>
                <form class="request-form" action="document_submission.php" method="POST" data-document-type="Certificate of Indigency">
                    <input type="hidden" name="documentType" value="Certificate of Indigency">
                    <button type="submit" class="request-button" <?php echo $isVerified ? '' : 'disabled'; ?>>
                        Request Indigency
                    </button>
                </form>
            </div>

            <div class="request-card">
                <div class="card-header">
                    <img class="card-icon" src="image/business.png" alt="Business Permit">
                    <span class="card-title">Barangay Business Permit</span>
                </div>
                <p class="card-description">
                    Required for the operation of any commercial activity within the Barangay's jurisdiction.
                </p>
                <form class="request-form" action="document_submission.php" method="POST" data-document-type="Business Permit">
                    <input type="hidden" name="documentType" value="Business Permit">
                    <button type="submit" class="request-button" <?php echo $isVerified ? '' : 'disabled'; ?>>
                        Request Business Permit
                    </button>
                </form>
            </div>

            <div class="request-card">
                <div class="card-header">
                    <img class="card-icon" src="image/residence 1.png" alt="Certificate of Residency">
                    <span class="card-title">Certificate of Residency</span>
                </div>
                <p class="card-description">
                    Confirms that you are a bonafide resident of Mantalongon, Dalaguete, for various purposes.
                </p>
                <form class="request-form" action="document_submission.php" method="POST" data-document-type="Certificate of Residency">
                    <input type="hidden" name="documentType" value="Certificate of Residency">
                    <button type="submit" class="request-button" <?php echo $isVerified ? '' : 'disabled'; ?>>
                        Request Residency
                    </button>
                </form>
            </div>
            
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
        
        <div class="footer-col footer-branding-col">
            <div class="logo-area-footer">
                <img class="social-responsibility-2" src="image/social-responsibility 1 (1).png" alt="Logo" />
                <div class="baryo-tap2">BARYO TAP</div>
            </div>
            <div class="a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu">
                A digital barangay service portal designed requests, and market price monitoring faster, transparent, and more accessible to all residents of Mantalongon, Dalaguete, Cebu.
            </div>
        </div>

        <div class="footer-col footer-explore-col">
            <div class="explore">EXPLORE</div>
            <div class="submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk">
                Submit Report / Complaint<br>
                Request Barangay Documents<br>
                Market Price Analytics<br>
                Announcement and Updates<br>
                Resident Support Desk
            </div>
        </div>

        <div class="footer-col footer-services-col">
            <div class="services">SERVICES</div>
            <div class="indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking">
                Indigency Certificate Request<br>
                Barangay Clearance Request<br>
                Business Permit Assistance<br>
                Community Concern Tracking
            </div>
        </div>

        <div class="footer-col footer-contact-col">
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
        
    </div>
</footer>


<script>
document.addEventListener('DOMContentLoaded', () => {
    // --- 1. LOGOUT MODAL LOGIC (Copied from other pages) ---
    const logoutBtn = document.getElementById('logoutBtn');
    const logoutModal = document.getElementById('logoutModal');
    const cancelLogoutBtn = document.getElementById('cancelBtn'); 
    const confirmLogoutBtn = document.getElementById('confirmBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'block';
        });
    }

    if (cancelLogoutBtn) {
        cancelLogoutBtn.addEventListener('click', () => {
            logoutModal.style.display = 'none';
        });
    }

    if (confirmLogoutBtn) {
        confirmLogoutBtn.addEventListener('click', () => {
            // Redirect to login.html
            window.location.href = "login.html"; 
        });
    }

    // Close modal when clicking outside
    window.addEventListener('click', (e) => {
        if(e.target === logoutModal){
            logoutModal.style.display = 'none';
        }
    });

    // --- 2. FORM SUBMISSION LOGIC ---
    // This logic prevents submission and shows an alert if the button is disabled (user not verified).
    const requestForms = document.querySelectorAll('.request-form');

    requestForms.forEach(form => {
        const submitButton = form.querySelector('.request-button');
        
        // Only add this listener if the button is disabled (i.e., user is not verified)
        if (submitButton.disabled) {
            form.addEventListener('submit', function(e) {
                // Prevent the default form submission
                e.preventDefault(); 
                
                // Get the validation status message from the PHP-generated banner content
                const banner = document.getElementById('validationBanner');
                // Use innerHTML to preserve the <a> tag in the Denied message
                const statusMessage = banner ? banner.innerHTML.trim() : "Your account verification status prevents document requests.";

                // Display a custom alert with the status message
                alert(`Request cannot be submitted. ${statusMessage.replace(/(<([^>]+)>)/gi, "")}`); // Strip HTML for alert
            });
        }
    });

    // --- 3. REAL-TIME SEARCH FILTER LOGIC ---
    const searchInput = document.querySelector('.search-input');
    const requestCards = document.querySelectorAll('.request-card');

    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            requestCards.forEach(card => {
                const title = card.querySelector('.card-title')?.textContent.toLowerCase() || '';
                const description = card.querySelector('.card-description')?.textContent.toLowerCase() || '';
                
                // Check if search term is in title OR description
                card.style.display = (title.includes(searchTerm) || description.includes(searchTerm)) ? 'flex' : 'none';
            });
        });
    }

});
</script>
</body>
</html>