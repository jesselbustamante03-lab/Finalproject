<?php
// ==========================================================
// DB Connection and Fetching Functions
// NOTE: CONFIGURE THESE DETAILS FOR YOUR ENVIRONMENT
// ==========================================================
$servername = "localhost"; 
$username = "root";      // <-- Your MySQL Username
$password = "";          // <-- Your MySQL Password
$dbname = "baryotap";  // <-- Your Database Name
$newsTableName = "news_and_updates";

/**
 * Establishes a connection to the MySQL database.
 * @return mysqli|null The database connection object or null on failure.
 */
function connectDB() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        error_log("Database Connection failed: " . $conn->connect_error);
        return null; // Return null on failure
    }
    return $conn;
}

/**
 * Fetches all published news items, including title and content.
 * The function no longer uses a limit to fetch all available records.
 * @return array An array of all news records or static fallback data.
 */
function fetchNewsUpdates() {
    $conn = connectDB();
    
    // --- STATIC FALLBACK DATA ---
    $staticFallback = [
        ['Title' => 'Free medical mission', 'Content' => 'Medical mission will take place on December 3, 2025. Please register at the Barangay Hall.', 'Date_Published' => '2025-12-03'],
        ['Title' => 'Road maintenance', 'Content' => 'Road maintenance in Sitio Sua starts on November 28, 2025. Expect slight delays in traffic until completed.', 'Date_Published' => '2025-11-28'],
        ['Title' => 'Power interruption', 'Content' => 'CEBECO advises a temporary power interruption on November 30, 2025 from 8:00 AM to 5:00 PM.', 'Date_Published' => '2025-11-30'],
        ['Title' => 'Emergency Meeting', 'Content' => 'The Barangay Council will hold an emergency meeting today at 2 PM to discuss the new waste management plan.', 'Date_Published' => '2025-11-28'],
        ['Title' => 'Clean-up Drive', 'Content' => 'A community clean-up drive is scheduled for Saturday, December 7th. All residents are encouraged to participate.', 'Date_Published' => '2025-11-27'],
    ];

    if (!$conn) {
        // Return full static fallback if DB connection fails
        return $staticFallback; 
    }
    
    global $newsTableName;
    
    // UPDATED SQL: Removed LIMIT clause to fetch all
    $sql = "SELECT Title, Content, Date_Published 
            FROM $newsTableName 
            -- WHERE Is_Active = 1 -- Uncomment if you use an 'Is_Active' column
            ORDER BY Date_Published DESC";
    
    $stmt = $conn->prepare($sql);
    // Removed bind_param for $limit
    $stmt->execute();
    
    $result = $stmt->get_result();
    $news = [];
    
    while ($row = $result->fetch_assoc()) {
        $news[] = $row;
    }

    $stmt->close();
    $conn->close();
    
    // If database returned data, return it. Otherwise, return fallback.
    return !empty($news) ? $news : $staticFallback;
}

// Fetch the data before the HTML structure begins
$newsUpdates = fetchNewsUpdates(); // Fetches all news
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <style>
/* ---------------------------------------------------------------------- */
/* DYNAMIC NEWS CSS - Modified to allow stacking and scrolling */
/* ---------------------------------------------------------------------- */
/* New container for scrolling news items, positioned relative to desktop-18 */
.news-scroller {
    position: absolute;
    left: 174px; /* 10px inside rectangle-140's left (164px) */
    top: 2450px; /* Calculated to be below the dark green header (rectangle-141) */
    width: 1131px; /* 1151px (rectangle-140 width) - 20px (padding) */
    height: 385px; /* Calculated scrollable height within rectangle-140 */
    overflow-y: auto; /* CRITICAL: Enables scrolling */
    padding-right: 15px; /* Space for the scrollbar */
}

.dynamic-news-item {
    background: rgba(128, 187, 137, 0.37);
    border-radius: 10px;
    border-style: solid;
    border-color: #000000;
    border-width: 1px;
    width: 100%; /* Relative to the scroller width */
    height: auto; 
    min-height: 72px; 
    position: relative; /* CRITICAL: Change from absolute to relative for stacking */
    margin-bottom: 10px; /* Space between news items */
    left: 0; /* Remove absolute offset */
    top: auto; /* Remove absolute offset */
    
    color: #000000;
    text-align: left; 
    font-family: "Inter-Regular", sans-serif;
    font-size: 20px; 
    font-weight: 400;
    line-height: 1.4; /* Better line height for text wrapping */
    padding: 15px 10px; 
    white-space: normal; /* Allow text wrapping */
    word-wrap: break-word;
}

/* Style for the title part */
.dynamic-news-item strong {
    font-family: "Inter-Bold", sans-serif; 
    font-weight: 700;
}

/* Original CSS Starts Here */
.desktop-18,
.desktop-18 * {
    box-sizing: border-box;
}
.desktop-18 {
    background: rgba(233, 254, 212, 0.7);
    height: 3161px;
    position: relative;
    overflow: hidden;
}
.rectangle-126 {
    width: 100;
    height: 923px;
    position: absolute;
    left: 0px;
    top: 101px;
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
    object-fit: cover;
}
.baryo-tap-your-smart-barangay-companion {
    color: #000000;
    text-align: left;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 60px;
    font-weight: 800;
    position: absolute;
    left: 28px;
    top: 316px;
    width: 774px;
    height: 196px;
}
.connecting-communities-one-tap-at-a-time {
    color: #263238;
    text-align: left;
    font-family: "Poppins-Medium", sans-serif;
    font-size: 32px;
    font-weight: 500;
    position: absolute;
    left: 28px;
    top: 528px;
}
.rectangle-68 {
    opacity: 0.8;
    width: 100%;
    height: 525px;
    position: absolute;
    left: 0px; 
    top: 1024px;
    box-shadow: 0px 4px 3px 0px rgba(0, 0, 0, 0.3);
    object-fit: cover;
}
.ellipse-19 {
    border-radius: 50%;
    width: 417px;
    height: 421px;
    position: absolute;
    left: 30px;
    top: 1076px;
    object-fit: cover;
}
.known-as-the-vegetable-basket-of-cebu-barangay-mantalongon-is-a-thriving-highland-community-blessed-with-cool-climate-fertile-farmland-and-hardworking-people-as-one-of-dalaguete-s-most-productive-barangays-mantalongon-plays-a-vital-role-in-supplying-fresh-vegetables-across-the-province {
    color: #263238;
    text-align: justify;
    font-family: "Lora", serif;
    font-size: 32px;
    font-weight: 500;
    position: absolute;
    left: 491px;
    top: 1168px;
    width: 871px;
}
.rectangle-127 {
    border-style: solid;
    border-color: transparent;
    border-width: 1px;
    width: 100%;
    height: 678px;
    position: absolute;
    left: -1px;
    top: 1549px;
    box-shadow: 0px 4px 4px 0px rgba(0, 0, 0, 0.25);
    object-fit: cover;
}
.rectangle-61 {
    background: #b9f8c2;
    border-radius: 20px;
    width: 561px;
    height: 216px;
    position: absolute;
    left: 124px;
    top: 1696px;
}
.rectangle-129 {
    background: #b9f8c2;
    border-radius: 20px;
    width: 561px;
    height: 216px;
    position: absolute;
    left: 122px;
    top: 1964px;
}
.rectangle-128 {
    background: #b9f8c2;
    border-radius: 20px;
    width: 561px;
    height: 216px;
    position: absolute;
    left: 743px;
    top: 1700px;
}
.rectangle-130 {
    background: #b9f8c2;
    border-radius: 20px;
    width: 561px;
    height: 216px;
    position: absolute;
    left: 743px;
    top: 1964px;
}
.submit-community-concerns-like-damaged-roads-garbage-problems-or-safety-issues-with-just-a-tap {
    color: #263238;
    text-align: center;
    font-family: "Lora", serif;
    font-size: 18px;
    font-weight: 400;
    position: absolute;
    left: 158px;
    top: 1842px;
}
.view-the-latest-verified-vegetable-prices-to-help-you-shop-wisely-and-avoid-overpricing {
    color: #263238;
    text-align: center;
    font-family: "Lora", serif;
    font-size: 18px;
    font-weight: 400;
    position: absolute;
    left: 159px;
    top: 2118px;
}
.request-barangay-documents-online-and-get-notified-once-they-re-ready-for-pickup {
    color: #263238;
    text-align: center;
    font-family: "Lora", serif;
    font-size: 18px;
    font-weight: 400;
    position: absolute;
    left: 792px;
    top: 2118px;
}
.quickly-access-verified-emergency-hotlines-for-police-fire-medical-and-barangay-assistance {
    color: #263238;
    text-align: center;
    font-family: "Lora", serif;
    font-size: 18px;
    font-weight: 400;
    position: absolute;
    left: 759px;
    top: 1847px;
}
.report-issue {
    color: #144d09;
    text-align: center;
    font-family: "Outfit-SemiBold", sans-serif;
    font-size: 32px;
    font-weight: 600;
    position: absolute;
    left: 302px;
    top: 1788px;
}
.our-services {
    color: #000000;
    text-align: center;
    font-family: "Poppins-Bold", sans-serif;
    font-size: 64px;
    font-weight: 700;
    position: absolute;
    left: 512px;
    top: 1575px;
}
.emergency-contact {
    color: #144d09;
    text-align: center;
    font-family: "Outfit-SemiBold", sans-serif;
    font-size: 32px;
    font-weight: 600;
    position: absolute;
    left: 845px;
    top: 1788px;
}
.request-document {
    color: #144d09;
    text-align: center;
    font-family: "Outfit-SemiBold", sans-serif;
    font-size: 32px;
    font-weight: 600;
    position: absolute;
    left: 863px;
    top: 2061px;
}
.vegetables-prices {
    color: #144d09;
    text-align: center;
    font-family: "Outfit-SemiBold", sans-serif;
    font-size: 32px;
    font-weight: 600;
    position: absolute;
    left: 244px;
    top: 2061px;
}
.basket-1 {
    width: 80px;
    height: 80px;
    position: absolute;
    left: 356px;
    top: 1974px;
    object-fit: cover;
    aspect-ratio: 1;
}
.medical-call-1 {
    width: 80px;
    height: 80px;
    position: absolute;
    left: 980px;
    top: 1709px;
    object-fit: cover;
    aspect-ratio: 1;
}
.report-issue-1 {
    width: 85px;
    height: 85px;
    position: absolute;
    left: 357px;
    top: 1709px;
    object-fit: cover;
    aspect-ratio: 1;
}
.quote-request-1 {
    width: 75px;
    height: 75px;
    position: absolute;
    left: 980px;
    top: 1974px;
    object-fit: cover;
    aspect-ratio: 1;
}
.rectangle-137 {
    background: #204a20;
    border-radius: 50px;
    width: 380px;
    height: 90px;
    position: absolute;
    left: 128px;
    top: 625px;
}
.get-started {
    color: #fff7f7;
    text-align: center;
    font-family: "Inter-Bold", sans-serif;
    font-size: 36px;
    font-weight: 700;
    position: absolute;
    right: 69.31%;
    left: 14%;
    width: 14.93%;
    bottom: 76.75%;
    top: 20.6%;
    height: 1.46%;
}
.rectangle-139 {
    background: rgba(7, 34, 1, 0.95);
    width: 100%; 
    height: 212px;
    position: absolute;
    left: 0px; 
    top: 2949px;
    display: flex; 
    justify-content: space-around; 
    align-items: flex-start; 
    padding: 20px 0; 
}

/* New CSS for footer sections */
.footer-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    color: #ffffff;
    font-family: "Inter-Medium", sans-serif;
    font-size: 13px;
    font-weight: 500;
}

.footer-section h3 {
    color: #ffffff;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 16px;
    font-weight: 800;
    margin-bottom: 15px;
    white-space: nowrap; 
}

.footer-section ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.footer-section ul li {
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.footer-section ul li img {
    margin-right: 8px;
    width: 16px; 
    height: 16px; 
    object-fit: contain;
}

.footer-logo-section {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 250px; 
    padding-left: 10px; 
}

.footer-logo {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.footer-logo .ellipse-20 {
    background: #ffffff;
    border-radius: 50%;
    width: 40px; 
    height: 40px; 
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 10px;
}

.footer-logo .social-responsibility-2 {
    width: 30px; 
    height: 30px; 
}

.footer-logo .baryo-tap {
    color: #ffffff;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 16px;
    font-weight: 800;
    margin: 0;
}

.footer-description {
    color: #ffffff;
    text-align: justified;
    font-family: "Inter-Medium", sans-serif;
    font-size: 13px;
    font-weight: 500;
    top: 20px;
}

.ellipse-20 {
    background: #ffffff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
    margin-right: 10px;
}

.baryo-tap {
    color: #ffffff;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 16px;
    font-weight: 800;
    margin: 0;
}

.social-responsibility-2 {
    width: 30px;
    height: 30px;
    object-fit: cover;
    aspect-ratio: 1;
}

.rectangle-140 {
    background: #b9f8c2;
    border-radius: 30px;
    width: 1151px;
    height: 536px;
    position: absolute;
    left: 164px;
    top: 2319px;
    box-shadow: 0px 4px 3px 0px rgba(0, 0, 0, 0.3);
}

.rectangle-141 {
    background: rgba(7, 34, 1, 0.95);
    border-radius: 30px;
    width: 1151px;
    height: 122px;
    position: absolute;
    left: 164px;
    top: 2318px;
}
.news-and-updates {
    color: #ffffff;
    text-align: center;
    font-family: "Outfit-SemiBold", sans-serif;
    font-size: 40px;
    font-weight: 600;
    position: absolute;
    left: 542px;
    top: 2354px;
}
.sport-news-1-1 {
    width: 145px;
    height: 145px;
    position: absolute;
    left: 398px;
    top: 2285px;
    object-fit: cover;
    aspect-ratio: 1;
}
.announcement-1 {
    width: 138px;
    height: 138px;
    position: absolute;
    left: 975px;
    top: 2285px;
    object-fit: cover;
    aspect-ratio: 1;
}
.rectangle-157 {
    background: #072201;
    width: 100%;
    height: 101px;
    position: absolute;
    left: 0px;
    top: 0px;
}
.baryo-tap2 {
    color: #fff8f8;
    text-align: center;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 28px;
    font-weight: 800;
    position: absolute;
    left: 109px;
    top: 25px;
}
.sign-in {
    color: #fff8f8;
    text-align: center;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 24px;
    font-weight: 800;
    position: absolute;
    left: 1120px;
    top: 32px;
}
.sign-up {
    color: #fff8f8;
    text-align: center;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 24px;
    font-weight: 800;
    position: absolute;
    left: 1291px;
    top: 32px;
}
.about {
    color: #fff8f8;
    text-align: center;
    font-family: "Poppins-ExtraBold", sans-serif;
    font-size: 24px;
    font-weight: 800;
    position: absolute;
    left: 953px;
    top: 32px;
}
.ellipse-26 {
    background: #ffffff;
    border-radius: 50%;
    width: 67px;
    height: 67px;
    position: absolute;
    left: 22px;
    top: 14px;
}
.social-responsibility-3 {
    width: 74px;
    height: 74px;
    position: absolute;
    left: 18px;
    top: 11px;
    object-fit: cover;
    aspect-ratio: 1;
}

.rectangle-137 {
    transition: all 0.3s ease; 
}

.rectangle-137:hover {
    background: #3ca63c; 
    transform: scale(1.05); 
}

.get-started {
    transition: color 0.3s ease; 
}

.get-started:hover {
    color: #ffffff; 
}


    </style>
</head>
<body>
<div class="desktop-18">
    <img class="rectangle-126" src="image/landing_bg.png" />
    <div class="baryo-tap-your-smart-barangay-companion">
        Baryo Tap-Your Smart
        <br />
        Barangay Companion
    </div>
    <div class="connecting-communities-one-tap-at-a-time">
        Connecting communities, one tap at a time.
    </div>
    <img class="rectangle-68" src="image/Blue Modern Business Facebook Cover (11).png" />
    <img class="ellipse-19" src="image/466380270_1788776135200873_3279101111227380698_n.jpg" />
    <div
        class="known-as-the-vegetable-basket-of-cebu-barangay-mantalongon-is-a-thriving-highland-community-blessed-with-cool-climate-fertile-farmland-and-hardworking-people-as-one-of-dalaguete-s-most-productive-barangays-mantalongon-plays-a-vital-role-in-supplying-fresh-vegetables-across-the-province"
    >
        Known as the Vegetable Basket of Cebu, Barangay
        <br />
        Mantalongon is a thriving highland community blessed with cool climate,
        fertile farmland, and hardworking people. As one of Dalaguete’s most
        productive barangays, Mantalongon plays a vital role in supplying fresh
        vegetables across the province.
    </div>
    <img class="rectangle-127" src="image/Blue Modern Business Facebook Cover (4).png" />
    <div class="rectangle-61"></div>
    <div class="rectangle-129"></div>
    <div class="rectangle-128"></div>
    <div class="rectangle-130"></div>
    <div
        class="submit-community-concerns-like-damaged-roads-garbage-problems-or-safety-issues-with-just-a-tap"
    >
        Submit community concerns like damaged roads, garbage problems,
        <br />
        or safety issues with just a tap.
    </div>
    <div
        class="view-the-latest-verified-vegetable-prices-to-help-you-shop-wisely-and-avoid-overpricing"
    >
        View the latest verified vegetable prices to help you shop wisely and
        <br />
        avoid overpricing.
    </div>
    <div
        class="request-barangay-documents-online-and-get-notified-once-they-re-ready-for-pickup"
    >
        Request barangay documents online and get notified once they&#039;re
        <br />
        ready for pickup.
    </div>
    <div
        class="quickly-access-verified-emergency-hotlines-for-police-fire-medical-and-barangay-assistance"
    >
        Quickly access verified emergency hotlines for police, fire, medical, and
        <br />
        barangay assistance.
    </div>
    <div class="report-issue">REPORT ISSUE</div>
    <div class="our-services">Our Services</div>
    <div class="emergency-contact">EMERGENCY CONTACT</div>
    <div class="request-document">REQUEST DOCUMENT</div>
    <div class="vegetables-prices">VEGETABLES PRICES</div>
    <img class="basket-1" src="image/basket.png" />
    <img class="medical-call-1" src="image/medical-call.png" />
    <img class="report-issue-1" src="image/report-issue.png" />
    <img class="quote-request-1" src="image/quote-request.png" />
    <a href="dlogin.html" style="text-decoration:none; color:inherit;">
    <div class="rectangle-137"></div>
    <div class="get-started">Get Started</div>
</a>


    <div class="rectangle-139">
        <div class="footer-logo-section">
            <div class="footer-logo">
                <div class="ellipse-20">
                    <img class="social-responsibility-2" src="image/social-responsibility 1 (1).png" />
                </div>
                <h3 class="baryo-tap">BARYO TAP</h3>
            </div>
            <p class="footer-description">
                A digital barangay service portal designed requests, and market price
                monitoring faster, transparent, and more accessible to all residents of
                Mantalongon, Dalaguete, Cebu.
            </p>
        </div>

        <div class="footer-section">
            <h3>EXPLORE</h3>
            <ul>
                <li>Submit Report / Complaint</li>
                <li>Request Barangay Documents</li>
                <li>Market Price Analytics</li>
                <li>Announcement and Updates</li>
                <li>Resident Support Desk</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>SERVICES</h3>
            <ul>
                <li>Indigency Certificate Request</li>
                <li>Barangay Clearance Request</li>
                <li>Business Permit Assistance</li>
                <li>Community Concern Tracking</li>
            </ul>
        </div>

        <div class="footer-section">
            <h3>CONTACT US</h3>
            <ul>
                <li><img src="image/location (2) 1.png" alt="Location icon" />Mantalongon, Dalaguete, Cebu</li>
                <li><img src="image/mail 1.png" alt="Email icon" />baryotap@gmail.com</li>
                <li><img src="image/telephone (1) 7.png" alt="Phone icon" />09511312976</li>
            </ul>
        </div>
    </div>

    <div class="rectangle-140"></div> 
    <div class="rectangle-141"></div>
    <div class="news-and-updates">NEWS AND UPDATES</div>
    <img class="sport-news-1-1" src="image/sport-news (1).png" />
    <img class="announcement-1" src="image/announcement.png" />

    <div class="news-scroller">
    <?php
    // The previous $starting_top and $increment variables are no longer needed 
    // as items are now stacked using relative positioning within the scroller.

    if (empty($newsUpdates)) {
        echo '<div class="dynamic-news-item" style="text-align: center; color: #555;">No recent news or updates available.</div>';
    } else {
        foreach ($newsUpdates as $index => $item) {
            
            // Truncate content for a clean display (allows for a longer preview)
            $max_content_length = 150; 
            $content = htmlspecialchars($item['Content']);
            if (strlen($content) > $max_content_length) {
                $content = substr($content, 0, $max_content_length) . '...';
            }
            
            // Format the date
            $dateFormatted = date('M d, Y', strtotime($item['Date_Published']));

            // Combine Title and Content: "[Date] - Title: Content..." with Title in bold
            $fullText = '<span style="color: #4a4a4a; font-size: 16px; font-weight: 600;">[' . $dateFormatted . ']</span>' . 
                        ' - <strong>' . htmlspecialchars($item['Title']) . '</strong>: ' . $content;

            // The dynamic-news-item CSS handles the stacking
            echo '<div class="dynamic-news-item">';
            echo $fullText;
            echo '</div>';
        }
    }
    ?>
    </div>
    <div class="rectangle-157"></div>
    <div class="baryo-tap2">Baryo Tap</div>
   <div class="sign-in"><a href="dlogin.html" style="text-decoration: none; color: inherit;">Sign in</a></div>
<div class="sign-up"><a href="dsignup.html" style="text-decoration: none; color: inherit;">Sign up</a></div>
<div class="about"><a href="about.html" style="text-decoration: none; color: inherit;">About</a></div>

    <div class="ellipse-26"></div>
    <img class="social-responsibility-3" src="image/social-responsibility 1 (1).png" />
</div>

</body>
</html>