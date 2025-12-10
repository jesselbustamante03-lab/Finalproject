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
    // You should log this error instead of exposing it to the user in a production environment
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
    // Provide a valid path to a default image if no profile picture is set
    'Profile_Picture_URL' => 'image/default_profile.png'
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
    <title>Report Issue - Baryo Tap</title>

    <style>
        /* (Your CSS code remains unchanged here) */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            min-height: 100vh; 
            font-family: 'Inter', sans-serif;
            background: url(image/background.png) center/cover no-repeat fixed; 
            overflow-x: hidden;
        }

        /* MAIN LAYOUT */
        .layout {
            display: flex;
            min-height: 100vh;
            width: 100%;
        }

        /* SIDEBAR */
        .sidebar {
            width: 432px;
            background: #f3fff5;
            padding: 25px 22px;
            display: flex;
            flex-direction: column;
            flex-shrink: 0;
            min-height: 100vh;
            position: sticky;
            top: 0;
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
            transition: all 0.3s ease;
            cursor: pointer;
        }
        .menu-item:hover { background: rgba(24, 118, 5, 0.35); transform: translateX(5px);text-decoration: none; }
        .menu-item.active { background: rgba(24, 118, 5, 0.35); }
        .menu-item img { width: 46px; height: 46px; }
        .menu-item span { font-size: 22px; font-weight: 600;color: #000; }

        .divider {
            height: 2px;
            background: #187605;
            margin: 50px 0 20px 0;
        }

        /* PROFILE */
        .profile { text-align: center; margin-top: auto; padding-bottom: 15px; }
        .profile-img {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            border: 3px solid #187605;
            box-shadow: inset 0 4px 8px rgba(0,0,0,0.3);
            margin-bottom: 10px;
        }
        .profile-name { font-size: 24px; font-weight: 600;color: #000; }
        .profile-email { font-size: 21px;color: #000; }

        .sidebar-footer {
            padding-top: 10px;
            text-align: center;
            font-size: 14px;
            color: #555;
            border-top: 1px solid #ddd;
        }

        /* MAIN AREA */
        .main-area {
            flex: 1;
            padding: 50px;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* FORM CARD */
        .form-card {
            max-width: 820px;
            margin: 0 auto;
            background: rgba(185, 248, 194, 0.90);
            border-radius: 30px;
            padding: 60px 80px;
            box-shadow: 0 12px 35px rgba(0,0,0,0.3);
            width: 100%;
            flex-grow: 1;
        }

        .form-title {
            font-size: 40px;
            font-weight: 800;
            text-align: center;
            margin-bottom: 50px;
        }

        .form-group { margin-bottom: 38px; }
        .label { font-size: 24px; margin-bottom: 12px; display: block; }

        .select-box, .textarea-box {
            width: 100%;
            padding: 20px 24px;
            border: 1.6px solid #000;
            border-radius: 10px;
            background: rgba(128, 187, 137, 0.37);
            font-size: 20px;
        }
        .textarea-box {
            height: 140px;
            resize: none;
        }

        /* UPLOAD */
        .upload-area {
            border: 2px dashed #187605;
            border-radius: 12px;
            padding: 35px;
            text-align: center;
            background: rgba(255,255,255,0.3);
            margin: 25px 0;
            cursor: pointer;
        }
        .upload-area img { width: 38px; margin-bottom: 12px; transform: scaleX(-1); }
        .upload-text { font-size: 16px; }
        #fileName { margin-top: 10px; font-size: 16px; font-weight: 500; }

        /* SUBMIT BUTTON */
        .submit-btn {
            display: block;
            width: 268px;
            height: 52px;
            margin: 40px auto 0;
            background: #072201;
            color: white;
            font-size: 24px;
            font-weight: 800;
            border-radius: 20px;
            border: none;
            cursor: pointer;
            transition: 0.3s;
        }
        .submit-btn:hover {
            transform: translateY(-6px);
            box-shadow: 0 15px 30px rgba(7,34,1,0.6);
        }

        /* FOOTER */
        .rectangle-139 {
            background: rgba(7, 34, 1, 0.95);
            width: 100%;
            padding: 40px 50px;
        }

        .footer-content-wrapper {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            max-width: 1400px;
            margin: 0 auto;
        }

        .footer-column {
            width: 280px;
            margin-bottom: 30px;
        }

        .footer-logo-section {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }
        .footer-logo-section img { width: 45px; height: 45px; }

        .baryo-tap-footer { font-size: 20px; font-weight: 800; color: white; }
        .footer-text, .footer-links span, .contact-item {
            color: white;
            font-size: 15px;
            line-height: 1.8;
        }

        .footer-title { font-size: 20px; font-weight: 800; margin-bottom: 15px; }

        .contact-item {
            display: flex;
            gap: 12px;
            margin-bottom: 10px;
        }
        .contact-item img { width: 20px; height: 20px; }

        /* ===== FULL RESPONSIVE FIX ===== */

        @media (max-width: 1100px) {
            .sidebar { width: 320px; }
        }

        @media (max-width: 900px) {
            .layout { flex-direction: column; }

            .sidebar {
                width: 100%;
                min-height: auto;
                position: relative;
                padding: 20px;
            }

            .main-area { padding: 25px; }

            .form-card { padding: 35px 30px; }

            .form-title { font-size: 32px; }

            .select-box, .textarea-box {
                font-size: 18px;
                padding: 14px;
            }

            .submit-btn {
                width: 100%;
                font-size: 20px;
            }
        }

        @media (max-width: 600px) {
            .logo-area img { width: 50px; height: 50px; }
            .logo-text { font-size: 22px; }

            .main-area { padding: 15px; }

            .form-card { padding: 22px 18px; }

            .form-title { font-size: 26px; }

            .label { font-size: 18px; }

            .select-box, .textarea-box { font-size: 16px; padding: 12px; }

            .submit-btn { font-size: 18px; border-radius: 16px; }

            .footer-content-wrapper {
                flex-direction: column;
                text-align: center;
                gap: 25px;
            }

            .footer-column { width: 100%; max-width: 350px; }

            .contact-item { justify-content: center; }
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

        <a href="dreport.php" class="menu-item active" style="text-decoration: none;">
            <img src="image/report-issue.png" alt="">
            <span>Report Issue</span>
        </a>
        <a href="VegetablePrices.html" class="menu-item "  style="text-decoration: none;">
            <img src="image/basket.png" alt="">
            <span>Vegetable Prices</span>
        </a>
        <a href="contacts.php" class="menu-item" style="text-decoration: none;">
            <img src="image/medical-call.png" alt="">
            <span>Emergency Contact</span>
        </a>
        <a href="RequestDocu.php" class="menu-item" style="text-decoration: none;">
            <img src="image/quote-request.png" alt="">
            <span>Request Document</span>
        </a>
        <a href="dlogin.html" class="menu-item" style="text-decoration: none;">
            <img src="image/logout (2).png" alt="">
            <span>Logout</span>
        </a>

        <div class="divider"></div>

        <a href="Profile.php" class="profile-link" style="text-decoration: none;">
            <div class="profile">
                <img 
                    src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" 
                    alt="Profile" 
                    class="profile-img"
                    id="sidebar-profile-pic"
                />
                
                <div class="profile-name" id="sidebar-profile-name">
                    <?php echo htmlspecialchars($userData['First_Name'] . ' ' . $userData['Last_Name']); ?>
                </div>
                
                <div class="profile-email" id="sidebar-profile-email">
                    <?php echo htmlspecialchars($userData['Email']); ?>
                </div>
            </div>
        </a>

        <div class="sidebar-footer">
            &copy; 2025 Baryo Tap.
        </div>
    </div>
    <div class="main-area">

        <form action="report_issue.php" method="POST" enctype="multipart/form-data">

            <div class="form-card">
            
            <h1 class="form-title">Report Issue</h1>

                <div class="form-group">
                    <label class="label">Select a Category *</label>
                    <select name="category" class="select-box" required>
                        <option>Select a Category</option>
                        <option>Streetlight</option>
                        <option>Garbage</option>
                        <option>Road</option>
                        <option>Water</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="label">Description *</label>
                    <textarea name="description" class="textarea-box" required></textarea>
                </div>

                <div class="form-group">
                    <label class="label">Upload a photo (Optional)</label>

                    <div class="upload-area" id="uploadArea">
                        <img src="image/upload 1.png">
                        <div class="upload-text" id="uploadText">
                            Choose images or drag and drop here.<br> JPG, PNG. Max 100mb.
                        </div>
                        <div id="fileName"></div>
                    </div>

                    <input type="file" id="fileInput" name="photo" accept="image/png, image/jpeg" style="display:none;">
                </div>

                <div class="form-group">
                    <label class="label">Confirm Location *</label>
                    <select name="location" class="select-box" required>
                        <option>Select a Sitio</option>
                        <option>Alang-Alang</option>
                        <option>Catambisan</option>
                        <option>Granchina</option>
                        <option>Lapa</option>
                        <option>Sampig</option>
                        <option>Mag-alambac</option>
                        <option>Upper Private</option>
                        <option>Lower Private</option>
                        <option>Upper Lahug</option>
                        <option>Lower Lahug</option>
                        <option>Sua</option>
                        <option>Redland</option>
                        <option>St. Niño</option>
                    </select>
                </div>

                <button class="submit-btn" type="submit">Submit</button>

            </div>

        </form>

    </div>
</div>

<footer class="rectangle-139">
    <div class="footer-content-wrapper">

        <div class="footer-column">
            <div class="footer-logo-section">
                <img src="image/social-responsibility 1 (1).png">
                <div class="baryo-tap-footer">BARYO TAP</div>
            </div>
            <div class="footer-text">
                A digital barangay service portal designed to make concerns, requests, and price monitoring accessible for all residents of Mantalongon.
            </div>
        </div>

        <div class="footer-column">
            <div class="footer-title">EXPLORE</div>
            <div class="footer-links">
                <span>Submit Report / Complaint</span>
                <span>Request Barangay Documents</span>
                <span>Market Price Analytics</span>
                <span>Announcements</span>
                <span>Resident Support Desk</span>
            </div>
        </div>

        <div class="footer-column">
            <div class="footer-title">SERVICES</div>
            <div class="footer-links">
                <span>Indigency Certificate</span>
                <span>Barangay Clearance</span>
                <span>Business Permit Assistance</span>
                <span>Community Concern Tracking</span>
            </div>
        </div>

        <div class="footer-column">
            <div class="footer-title">CONTACT US</div>
            <div class="contact-item">
                <img src="image/location (2) 1.png">
                Mantalongon, Dalaguete, Cebu
            </div>
            <div class="contact-item">
                <img src="image/mail 1.png">
                baryotap@gmail.com
            </div>
            <div class="contact-item">
                <img src="image/telephone (1) 7.png">
                09511312976
            </div>
        </div>

    </div>

</footer>

<script>
const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const uploadText = document.getElementById('uploadText');
const fileNameDisplay = document.getElementById('fileName');

uploadArea.onclick = () => fileInput.click();

fileInput.onchange = () => {
    const file = fileInput.files[0];
    if(file){
        uploadText.innerHTML = 'File selected:';
        fileNameDisplay.textContent = file.name;
    }
};
</script>

</body>
</html>