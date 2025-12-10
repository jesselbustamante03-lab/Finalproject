<?php
// ==========================================================
// DB Connection and Fetching Functions
// NOTE: CONFIGURE THESE DETAILS FOR YOUR ENVIRONMENT
// ==========================================================
$servername = "localhost"; 
$username = "root";      // <-- Your MySQL Username
$password = "";          // <-- Your MySQL Password
$dbname = "baryotap";  // <-- Your Database Name (Make sure this matches your DB name)
$newsTableName = "news_and_updates";

/**
 * Establishes a connection to the MySQL database.
 * @return mysqli|null The database connection object or null on failure.
 */
function connectDB() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        // Log the error instead of dying in a user-facing script
        error_log("Database Connection failed: " . $conn->connect_error);
        return null; // Return null on failure
    }
    return $conn;
}

/**
 * Fetches all published news items, including title and content.
 * @return array An array of all news records or static fallback data.
 */
function fetchNewsUpdates() {
    $conn = connectDB();
    
    // --- STATIC FALLBACK DATA (Used if DB connection fails or table is empty) ---
    // Keep this for reliable display even without DB connection
    $staticFallback = [
        ['Title' => 'Free medical mission', 'Content' => 'Medical mission will take place on December 3, 2025. Please register at the Barangay Hall.', 'Date_Published' => '2025-12-03'],
        ['Title' => 'Road maintenance', 'Content' => 'Road maintenance in Sitio Sua starts on November 28, 2025. Expect slight delays in traffic until completed.', 'Date_Published' => '2025-11-28'],
        ['Title' => 'Power interruption', 'Content' => 'CEBECO advises a temporary power interruption on November 30, 2025 from 8:00 AM to 5:00 PM.', 'Date_Published' => '2025-11-30'],
    ];

    if (!$conn) {
        return $staticFallback; 
    }
    
    global $newsTableName;
    
    // SQL Query to fetch all news, ordered by most recent date
    $sql = "SELECT Title, Content, Date_Published 
            FROM $newsTableName 
            ORDER BY Date_Published DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    $result = $stmt->get_result();
    $news = [];
    
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }

    $stmt->close();
    $conn->close();
    
    // Returns DB data if available, otherwise returns static fallback
    return !empty($news) ? $news : $staticFallback;
}

// Fetch the data before the HTML structure begins
$newsUpdates = fetchNewsUpdates();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home Page</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body {
            height: auto;
            min-height: 100vh;
            background: #b9f8c2;
            font-family: sans-serif;
            overflow-x: hidden;
        }

        /* Main container */
        .desktop-24 {
            width: 100%;
            height: 1380px;
            max-width: 100%;
            margin: 0 auto;
            position: relative;
            background: #b9f8c2;
        }

        /* Original Styles */
        .rectangle-135 { width: 100%; height: 792px; position: absolute; left: 0; top: 44px; object-fit: cover; }
        .rectangle-61 { background: #b9f8c2; border-radius: 20px; width: 430px; height: 167px; position: absolute; left: 261px; top: 370px; }
        .rectangle-66 { background: #b9f8c2; border-radius: 20px; width: 430px; height: 167px; position: absolute; left: 817px; top: 370px; }
        .rectangle-67 { background: #b9f8c2; border-radius: 20px; width: 430px; height: 167px; position: absolute; left: 817px; top: 577px; }
        .rectangle-65 { background: #b9f8c2; border-radius: 20px; width: 430px; height: 167px; position: absolute; left: 261px; top: 577px; }

        .report-issue { color: #144d09; text-align: center; font-family: "Outfit-SemiBold", sans-serif; font-size: 32px; font-weight: 600; position: absolute; left: 356px; top: 476px; }
        .emergency-contact { 
            color: #144d09; 
            text-align: center; 
            font-family: "Outfit-SemiBold", sans-serif; 
            font-size: 32px; 
            font-weight: 600; 
            position: absolute; 
            left: 845px; 
            top: 476px; 
            text-decoration: none;
        }

        .request-document { color: #144d09; text-align: center; font-family: "Outfit-SemiBold", sans-serif; font-size: 32px; font-weight: 600; position: absolute; left: 855px; top: 681px; }
        .vegetables-prices { color: #144d09; text-align: center; font-family: "Outfit-SemiBold", sans-serif; font-size: 32px; font-weight: 600; position: absolute; left: 303px; top: 681px; }

        .basket-1 { width: 80px; height: 80px; position: absolute; left: 425px; top: 597px; object-fit: cover; }
        .medical-call-1 { width: 80px; height: 80px; position: absolute; left: 984px; top: 390px; object-fit: cover; }
        .report-issue-1 { width: 85px; height: 85px; position: absolute; left: 425px; top: 390px; object-fit: cover; }
        .quote-request-1 { width: 75px; height: 75px; position: absolute; left: 984px; top: 597px; object-fit: cover; }

        .rectangle-60 { background: #b9f8c2; border-radius: 400px; width: 100%; height: 559px; position: absolute; left: 0; top: -237px; }
        .in-baryo-tap-communicating-has-never-been-easier { color: #000; font-family: "SfPro-Medium", sans-serif; font-size: 32px; position: absolute; left: 179px; top: 226px; width: 488px; }
        .baryo-tap { color: #000; font-family: "SfPro-Bold", sans-serif; font-size: 30px; font-weight: 700; position: absolute; left: 88px; top: 28px; }
        .welcome-to-baryo-tap { color: #000; font-family: "SfPro-Bold", sans-serif; font-size: 60px; font-weight: 700; position: absolute; left: 179px; top: 83px; width: 488px; }
        .adult-talking-cell-phone-amico-1 { width: 300px; height: 300px; position: absolute; left: 870px; top: 16px; }
        .rectangle-64 { 
            border-radius: 40px; 
            border: 3px solid #187605; 
            width: 53px; 
            height: 53px; 
            position: absolute; 
            left: 1394px; 
            top: 16px; 
            box-shadow: inset 0px 4px 4.6px 4px rgba(0,0,0,0.25), 0px 4px 4px rgba(0,0,0,0.25); 
            cursor: pointer; 
        }

        .rectangle-136 { width: 100%; height: 649px; position: absolute; left: 0; top: 836px; box-shadow: 0px 4px 4px rgba(0,0,0,0.25); }
        /* MODIFIED: Add padding-top and overflow hidden for the list inside */
        .rectangle-68 { 
            background: #b9f8c2; 
            border-radius: 30px; 
            width: 1151px; 
            height: 536px; 
            position: absolute; 
            left: 184px; 
            top: 890px; 
            box-shadow: 0px 4px 3px rgba(0,0,0,0.3);
            padding-top: 130px; /* Space for the header overlay */
            overflow: hidden; /* Prevent the scrollbar from showing on the outer box */
        }
        
        /* NEW STYLES for dynamic news list */
        .news-list-container {
            height: 100%;
            width: 100%;
            overflow-y: auto; /* Enable scrolling for the news list */
            padding: 0 40px;
            padding-bottom: 20px;
            box-sizing: border-box;
        }

        .dynamic-news-item {
            background: rgba(128,187,137,0.37);
            border-radius: 10px;
            border: 1px solid #000;
            width: 100%;
            /* Fixed height removed to allow multiple lines */
            min-height: 72px; 
            margin-bottom: 20px;
            padding: 15px 20px;
            display: flex;
            align-items: center; /* Vertically center content */
            font-size: 18px; /* Slightly smaller font for better fit */
            line-height: 1.4;
        }
        .dynamic-news-item p {
            margin: 0;
            color: #000;
        }
        .date-info {
            color: #4a4a4a; 
            font-size: 18px; 
            font-weight: 600;
            white-space: nowrap; /* Keep date and initial bracket on one line */
        }
        .news-title {
            font-weight: 700;
        }


        /* The original static news elements are removed from the HTML, 
           but their styles are commented out below if you need them. */
        /*
        .rectangle-69, .rectangle-70, .rectangle-71 { background: rgba(128,187,137,0.37); border-radius: 10px; border: 1px solid #000; width: 878px; height: 72px; position: absolute; left: 317px; }
        .rectangle-69 { top: 1058px; }
        .rectangle-70 { top: 1180px; }
        .rectangle-71 { top: 1302px; }

        .cebeco-advises-a-temporary-power-interruption-on-november-30-2025 { color: #000; font-size: 29px; position: absolute; left: 320px; top: 1312px; }
        .free-medical-mission-will-take-place-on-december-3-2025 { color: #000; font-size: 30px; text-align: center; position: absolute; left: 320px; top: 1078px; }
        .road-maintenance-in-sitio-sua-starts-on-november-28-2025 { color: #000; font-size: 30px; text-align: center; position: absolute; left: 320px; top: 1200px; }
        */

        .social-responsibility-2 { width: 74px; height: 74px; position: absolute; left: 14px; top: 9px; }
        .rectangle-139 { background: rgba(7,34,1,0.95); border-radius: 30px; width: 1151px; height: 122px; position: absolute; left: 184px; top: 890px; }
        .news-and-updates { color: #fff; font-family: "Outfit-SemiBold", sans-serif; font-size: 40px; position: absolute; left: 522px; top: 935px; }
        .sport-news-1-1 { width: 145px; height: 145px; position: absolute; left: 378px; top: 851px; }
        .announcement-1 { width: 138px; height: 138px; position: absolute; left: 955px; top: 851px; }

        /* Footer */
        .footer {
            width: 100%;
            height: 300px;
            background: rgba(7, 34, 1, 0.95);
            position: relative;
            margin-top: 100px;
        }

        .ellipse-20 { background: #ffffff; border-radius: 50%; width: 67px; height: 67px; position: absolute; left: 41px; top: 30px; }
        .social-responsibility-2.footer-logo { width: 55px; height: 55px; position: absolute; left: 47px; top: 36px; object-fit: cover; }
        .baryo-tap2 { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 118px; top: 45px; }

        .a-digit-barangay-service-portal-designed-requests-and-market-price-monitoring-faster-transparent-and-more-accessible-to-all-residents-of-mantalongon-dalaguete-cebu {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 1.8; position: absolute; left: 47px; top: 120px; width: 320px;
        }

        .explore { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 472px; top: 45px; }
        .submit-report-complaint-request-barangay-documents-market-price-analytics-announcement-and-updates-resident-support-desk {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 2.4; position: absolute; left: 472px; top: 95px; width: 320px;
        }

        .services { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 845px; top: 45px; }
        .indigency-certificate-request-barangay-clearance-request-business-permit-assistance-community-concern-tracking {
            color: #ffffff; font-family: "Inter-Medium", sans-serif; font-size: 15px; line-height: 2.4; position: absolute; left: 845px; top: 95px; width: 320px;
        }

        .contact-us { color: #ffffff; font-family: "Poppins-ExtraBold", sans-serif; font-size: 20px; font-weight: 800; position: absolute; left: 1250px; top: 45px; }

        .contact-item {
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
            font-family: "Inter-Medium", sans-serif;
            font-size: 15px;
            position: absolute;
            left: 1250px;
        }
        .contact-item img { width: 20px; height: 20px; }
        .contact-location   { top: 95px; }
        .contact-email      { top: 135px; }
        .contact-phone      { top: 175px; }
    </style>
</head>
<body>

    <div class="desktop-24">
        <img class="rectangle-135" src="image/background.png" />
        <div class="rectangle-61"></div>
        <div class="rectangle-66"></div>
        <div class="rectangle-67"></div>
        <div class="rectangle-65"></div>

        <a href="dreport.php" style="text-decoration: none;">
            <img class="report-issue-1" src="image/report-issue.png" />
            <div class="report-issue">REPORT ISSUE</div>
        </a>

        <a href="contacts.php" class="emergency-contact">EMERGENCY CONTACT</a>

        <a href="RequestDocu.php" style="text-decoration: none; color: inherit;">
            <div class="request-document">REQUEST DOCUMENT</div>
            <img class="quote-request-1" src="image/quote-request.png" />
        </a>

        <a href="VegetablePrices.html" style="text-decoration: none; color: inherit;">
            <div class="vegetables-prices">VEGETABLES PRICES</div>
            <img class="basket-1" src="image/basket.png" />
        </a>

        <img class="medical-call-1" src="image/medical-call.png" />
        <img class="report-issue-1" src="image/report-issue.png" />
        
        <div class="rectangle-60"></div>
        <div class="in-baryo-tap-communicating-has-never-been-easier">
            In Baryo Tap, communicating has never been easier.
        </div>
        <div class="baryo-tap">Baryo Tap</div>
        <div class="welcome-to-baryo-tap">
            Welcome To<br>Baryo Tap!
        </div>
        <img class="adult-talking-cell-phone-amico-1" src="image/Adult talking cell phone-amico 1.png" />

        
      
            <a href="Profile.php" style="text-decoration: none; color: inherit;">
                <img src="<?php echo htmlspecialchars($userData['Profile_Picture_URL']); ?>" 
                    alt="Profile" 
                    class="rectangle-64"
                >
            </a>
  

        <img class="rectangle-136" src="image/Blue Modern Business Facebook Cover (11).png" />
        <div class="rectangle-68">
            <div class="news-list-container">
            <?php
            if (empty($newsUpdates)) {
                // Display if no news is found
                echo '<div class="dynamic-news-item" style="justify-content: center; text-align: center; color: #555;">No recent news or updates available.</div>';
            } else {
                foreach ($newsUpdates as $item) {
                    
                    // Truncate content for a cleaner display
                    $max_content_length = 100; 
                    $content = htmlspecialchars($item['Content']);
                    if (strlen($content) > $max_content_length) {
                        $content = substr($content, 0, $max_content_length) . '...';
                    }
                    
                    // Format the date
                    $dateFormatted = date('M d, Y', strtotime($item['Date_Published']));

                    // Combine elements in a paragraph for multi-line support
                    $fullText = '<p>' .
                                '<span class="date-info">[' . $dateFormatted . ']</span>' . 
                                ' - <strong class="news-title">' . htmlspecialchars($item['Title']) . '</strong>: ' . 
                                $content . 
                                '</p>';

                    echo '<div class="dynamic-news-item">';
                    echo $fullText;
                    echo '</div>';
                }
            }
            ?>
            </div>
            </div>
        
        <div class="rectangle-139"></div>
        <div class="news-and-updates">NEWS AND UPDATES</div>
        <img class="sport-news-1-1" src="image/sport-news (1).png" />
        <img class="announcement-1" src="image/announcement.png" />
    </div>

    <div class="footer">
        <div class="ellipse-20"></div>
        <img class="social-responsibility-2 footer-logo" src="image/social-responsibility 1 (1).png" />
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

</body>
</html>