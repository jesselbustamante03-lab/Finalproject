<?php
// Configuration for the database connection
$servername = "localhost"; // Usually 'localhost' for XAMPP
$username = "root";      // Default XAMPP username
$password = "";          // Default XAMPP password (often empty)
$dbname = "baryotap";  // **Ensure this is the correct database name**
$tableName = "vegetable_price"; // Existing table for prices
$newsTableName = "news_and_updates"; // **NEW table for news**
$reportTableName = "report"; // Table name for reported issues
// **NEW Table Name for Document Requests**
$documentRequestTable = "document_request"; 

// --- PHP Database Functions ---

/**
 * Establishes a connection to the MySQL database.
 * @return mysqli|null The database connection object or null on failure.
 */
function connectDB() {
    global $servername, $username, $password, $dbname;
    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        // In a production environment, you would log this error, not display it.
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// --- VEGETABLE PRICE FUNCTIONS (omitted for brevity, assume unchanged) ---

/**
 * Fetches the LATEST price for each unique vegetable name from the database.
 * Uses a subquery to find the row with the maximum Price_ID (most recent update) for each name.
 * @return array An array of vegetable price records (latest only).
 */
function fetchVegetablePrices() {
    $conn = connectDB();
    // SQL query to select the row with the highest Price_ID (latest entry) for each unique Vegetable_Name.
    $sql = "SELECT t1.Price_ID, t1.Vegetable_Name, t1.Price, t1.Date_Updated 
            FROM " . $GLOBALS['tableName'] . " t1
            INNER JOIN (
                SELECT Vegetable_Name, MAX(Price_ID) AS Max_Price_ID
                FROM " . $GLOBALS['tableName'] . "
                GROUP BY Vegetable_Name
            ) t2 ON t1.Vegetable_Name = t2.Vegetable_Name AND t1.Price_ID = t2.Max_Price_ID
            ORDER BY t1.Vegetable_Name ASC"; // Always sort by name for consistent display

    $result = $conn->query($sql);
    $prices = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $prices[] = [
                'id' => $row['Price_ID'], // This is the ID of the *latest* entry
                'name' => $row['Vegetable_Name'],
                'price' => (float) $row['Price'], 
                'date_updated' => $row['Date_Updated'] 
            ];
        }
    }
    $conn->close();
    return $prices;
}

/**
 * Inserts a new record for the given vegetable, creating a price history entry (as requested).
 * This performs an INSERT instead of an UPDATE.
 * @param string $name The name of the vegetable.
 * @param float $price The new price value.
 * @return int The ID of the inserted record or 0 on failure.
 */
function insertNewPriceHistory($name, $price) {
    $conn = connectDB();
    // Sanitize inputs
    $name = $conn->real_escape_string($name);
    $price = $conn->real_escape_string($price);
    
    // Price_ID is auto-incremented, Date_Updated is set automatically
    $sql = "INSERT INTO " . $GLOBALS['tableName'] . " (Vegetable_Name, Price, Date_Updated) VALUES (?, ?, CURDATE())";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sd", $name, $price); // 's' for string, 'd' for double/decimal
    
    $success = $stmt->execute();
    $newId = $success ? $conn->insert_id : 0;
    
    $stmt->close();
    $conn->close();
    return $newId;
}


/**
 * Deletes a vegetable price record by Price_ID.
 * @param int $id The Price_ID to delete.
 * @return bool True on success, false on failure.
 */
function deletePriceDB($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    $sql = "DELETE FROM " . $GLOBALS['tableName'] . " WHERE Price_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); 
    
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

// --- NEWS/UPDATE FUNCTIONS (omitted for brevity, assume unchanged) ---

/**
 * Fetches ALL news or update records from the news_and_updates table for admin view.
 * Includes the primary key (News_ID) which is required for editing and deleting.
 * @return array An array of all news records.
 */
function fetchNewsUpdatesAdmin() {
    $conn = connectDB();
    global $newsTableName;
    
    // Select all required fields, including News_ID
    $sql = "SELECT News_ID, Title, Category, Content, Posted_By_Role, Date_Published 
            FROM $newsTableName 
            ORDER BY Date_Published DESC, News_ID DESC"; 
    
    $result = $conn->query($sql);
    $news = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $news[] = $row;
        }
    }
    $conn->close();
    return $news;
}

/**
 * Deletes a news post by News_ID.
 * @param int $id The News_ID to delete.
 * @return bool True on success, false on failure.
 */
function deleteNewsDB($id) {
    $conn = connectDB();
    $id = $conn->real_escape_string($id);
    
    global $newsTableName;
    $sql = "DELETE FROM $newsTableName WHERE News_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id); 
    
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

/**
 * Inserts a new news or update record into the news_and_updates table.
 * @param string $title The title of the news.
 * @param string $category The category of the news.
 * @param string $content The detailed content of the news.
 * @return int The ID of the inserted record or 0 on failure.
 */
function insertNewNews($title, $category, $content) {
    $conn = connectDB();
    
    // Sanitize inputs
    $title = $conn->real_escape_string($title);
    $category = $conn->real_escape_string($category);
    $content = $conn->real_escape_string($content);

    // Using a placeholder for Posted_By_ID (e.g., 1 for Admin) and setting the date to today.
    $postedById = 1; 
    $postedByRole = 'Admin';

    $sql = "INSERT INTO " . $GLOBALS['newsTableName'] . " 
            (Title, Category, Content, Posted_By_ID, Posted_By_Role, Date_Published) 
            VALUES (?, ?, ?, ?, ?, CURDATE())";
    
    $stmt = $conn->prepare($sql);
    // 'sssis' -> string (title), string (category), string (content), integer (id), string (role)
    $stmt->bind_param("sssis", $title, $category, $content, $postedById, $postedByRole); 
    
    $success = $stmt->execute();
    $newId = $success ? $conn->insert_id : 0;
    
    $stmt->close();
    $conn->close();
    return $newId;
}

// --- REPORTED ISSUE FUNCTIONS (omitted for brevity, assume unchanged) ---

/**
 * Fetches all reported issues from the 'report' table.
 * @return array An array of all reported issues records.
 */
function fetchReportedIssues() {
    $conn = connectDB();
    global $reportTableName;
    
    // Selecting all columns, including the newly added 'Status'
    $sql = "SELECT Report_ID, User_ID, Category, Description, Photo_URL, Location, Reported_Date, Status 
            FROM $reportTableName 
            ORDER BY Reported_Date DESC, Report_ID DESC"; 
    
    $result = $conn->query($sql);
    $issues = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            // Standardize the date/time field name for the front-end
            $date_field = isset($row['Reported_Date']) ? 'Reported_Date' : 'Report_Timestamp'; // Check for the actual column name
            
            if (isset($row[$date_field])) {
                $row['Report_Time'] = $row[$date_field]; 
                unset($row[$date_field]); // Remove the original name
            } else {
                $row['Report_Time'] = 'N/A'; // Fallback
            }
            $issues[] = $row;
        }
    }
    $conn->close();
    return $issues;
}

/**
 * Updates the status of a reported issue in the database.
 * @param int $id The Report_ID of the issue.
 * @param string $newStatus The new status ('Pending', 'In Progress', 'Resolved', etc.).
 * @return bool True on success, false on failure.
 */
function updateIssueStatusDB($id, $newStatus) {
    $conn = connectDB();
    global $reportTableName;
    
    // Ensure inputs are safe
    $id = (int) $id;
    
    $sql = "UPDATE $reportTableName SET Status = ? WHERE Report_ID = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $newStatus, $id); // 's' for string, 'i' for integer
    
    $success = $stmt->execute();
    
    $stmt->close();
    $conn->close();
    return $success;
}

// --- NEW DOCUMENT REQUEST FUNCTIONS ---

/**
 * Fetches all document requests from the 'document_request' table.
 * NOTE: The provided table only has Document_Type, Request_ID, Date_Requested, Status, Fullname, and Purpose_of_Request 
 * that are relevant for the initial view.
 * @return array An array of all document requests records.
 */
function fetchDocumentRequests() {
    $conn = connectDB();
    global $documentRequestTable;
    
    // Select the fields relevant for the document request card view
    $sql = "SELECT Request_ID, Document_Type, Date_Requested, Status, Fullname, Purpose_of_Request 
            FROM $documentRequestTable 
            ORDER BY Date_Requested DESC, Request_ID DESC"; 
    
    $result = $conn->query($sql);
    $requests = [];
    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $requests[] = $row;
        }
    }
    $conn->close();
    return $requests;
}


// --- API Endpoint Simulation (The PHP file itself acts as an API handler) ---
if (isset($_POST['action']) && !empty($_POST['action'])) {
    $action = $_POST['action'];
    $response = ['success' => false, 'message' => 'Invalid action'];

    // ... (Price, News, Issue Action Handlers are here) ...

    // ACTION: Handles the price edit by inserting a new history record
    if ($action === 'insert_new_price' && isset($_POST['name']) && isset($_POST['price'])) {
        $name = $_POST['name'];
        $price = (float) $_POST['price'];

        $newId = insertNewPriceHistory($name, $price); 
        
        if ($newId > 0) {
            $response = [
                'success' => true, 
                'message' => 'Price updated successfully and history recorded.',
                'new_id' => $newId,
                'name' => $name,
                'price' => $price,
                'date' => date('Y-m-d')
            ];
        } else {
            $response['message'] = 'Failed to insert new price history into database.';
        }
    } 
    // ACTION: Deleting a price
    elseif ($action === 'delete_price' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        
        if (deletePriceDB($id)) {
            $response = ['success' => true, 'message' => 'Price entry deleted successfully.'];
        } else {
            $response['message'] = 'Failed to delete price from database.';
        }
    }
    // ACTION: Inserting a NEW News/Update post
    elseif ($action === 'insert_new_news' && isset($_POST['title']) && isset($_POST['category']) && isset($_POST['content'])) {
        $title = $_POST['title'];
        $category = $_POST['category'];
        $content = $_POST['content'];
        
        $newId = insertNewNews($title, $category, $content);

        if ($newId > 0) {
            $response = [
                'success' => true, 
                'message' => 'News post published successfully.',
                'new_id' => $newId,
                'title' => $title,
                'category' => $category,
                'date' => date('Y-m-d')
            ];
        } else {
            $response['message'] = 'Failed to publish news post to database.';
        }
    }
    // ACTION: Deleting a news post (NEW HANDLER)
    elseif ($action === 'delete_news' && isset($_POST['id'])) {
        $id = (int) $_POST['id'];
        
        if (deleteNewsDB($id)) {
            $response = ['success' => true, 'message' => 'News post deleted successfully.'];
        } else {
            $response['message'] = 'Failed to delete news post from database.';
        }
    }
    // ACTION: Handles the status update for a reported issue (NEW HANDLER)
    elseif ($action === 'update_issue_status' && isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int) $_POST['id'];
        $status = $_POST['status'];

        if (updateIssueStatusDB($id, $status)) {
            $response = [
                'success' => true, 
                'message' => "Issue #{$id} status updated to '{$status}' successfully."
            ];
        } else {
            $response['message'] = "Failed to update status for Issue #{$id}.";
        }
    }
    // ACTION: Update Document Status (NEW HANDLER - Placeholder for the client-side update)
    elseif ($action === 'update_document_status' && isset($_POST['id']) && isset($_POST['status'])) {
        $id = (int) $_POST['id'];
        $status = $_POST['status'];
        global $documentRequestTable;
        
        // This is a placeholder for the actual DB update logic
        // For a full solution, you would need an updateDocumentStatusDB function, similar to updateIssueStatusDB
        $conn = connectDB();
        $sql = "UPDATE $documentRequestTable SET Status = ? WHERE Request_ID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        $success = $stmt->execute();
        $stmt->close();
        $conn->close();

        if ($success) {
            $response = [
                'success' => true, 
                'message' => "Document Request #{$id} status updated to '{$status}' successfully."
            ];
        } else {
            $response['message'] = "Failed to update status for Document Request #{$id}.";
        }
    }
    
    header('Content-Type: application/json');
    echo json_encode($response);
    exit; // Stop execution after the API call
}

// Fetch initial data to be injected into JavaScript
$vegetablePrices = fetchVegetablePrices();
$vegetablePricesJson = json_encode($vegetablePrices);

// NEW: Fetch all news updates for the admin section
$allNews = fetchNewsUpdatesAdmin(); 
$allNewsJson = json_encode($allNews);

// NEW: Fetch all reported issues
$reportedIssues = fetchReportedIssues(); // <-- CALL THE NEW FUNCTION
$reportedIssuesJson = json_encode($reportedIssues); // <-- ENCODE FOR JS

// **NEW: Fetch all document requests**
$documentRequests = fetchDocumentRequests(); // <-- CALL THE NEW FUNCTION
$documentRequestsJson = json_encode($documentRequests); // <-- ENCODE FOR JS

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        /* ... (CSS Styles remain unchanged) ... */
        .desktop-25,
        .desktop-25 * {
            box-sizing: border-box;
            font-family: 'Poppins', sans-serif;
        }
        .desktop-25 {
            background: #f3fff5;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        .header-container {
            position: fixed; 
            top: 0;
            left: 0;
            width: 100%;
            z-index: 20;
        }
        .rectangle-144 {
            background: #072201; 
            width: 100%;
            height: 158px;
            position: relative;
        }
        .ellipse-20 {
            background: #ffffff;
            border-radius: 50%;
            width: 67px;
            height: 67px;
            position: absolute;
            left: 34px;
            top: 45px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }
        .ellipse-20 img {
            max-width: 100%;
            height: auto;
            display: block;
        }
        .baryo-tap {
            color: #ffffff;
            font-family: "Poppins-ExtraBold", sans-serif;
            font-size: 20px;
            font-weight: 800;
            position: absolute;
            left: 113px;
            top: 84px;
        }
        .super-admin-dashboard {
            color: #ffffff;
            font-family: "Poppins-ExtraBold", sans-serif;
            font-size: 36px;
            font-weight: 800;
            position: absolute;
            left: 112px;
            top: 34px;
        }
        /* NEW CSS for Logout Button */
        .logout-button {
            position: absolute;
            right: 48px; /* Same padding as main-content-wrapper */
            top: 66px;
            background: transparent;
            border: 2px solid #ffffff;
            border-radius: 20px;
            color: #ffffff;
            font-family: "Poppins-Medium", sans-serif;
            font-size: 16px;
            font-weight: 500;
            padding: 8px 20px;
            cursor: pointer;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .logout-button:hover {
            background: #ffffff;
            color: #072201;
        }
        /* END NEW CSS */
        .main-content-wrapper {
            padding-top: 188px; 
            padding-left: 48px;
            padding-right: 48px;
            position: relative;
            z-index: 1;
        }
        .rectangle-145 {
            background: #e8f5e9;
            border-radius: 20px;
            border-style: solid;
            border-color: transparent;
            border-width: 1px;
            width: 100%;
            min-height: calc(100vh - 188px - 30px);
            box-shadow: 0px 4px 6px 0px rgba(0, 0, 0, 0.15);
            padding-top: 52px;
            position: relative;
        }
        .tabs-header-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            padding: 0 15px;
            z-index: 20;
            background: #e8f5e9; 
        }
        .tab-list {
            display: flex;
            justify-content: flex-start;
            gap: 55px;
            padding: 10px 0 0 15px;
            position: relative;
            z-index: 11;
        }
        .tab-button {
            color: #000000;
            text-align: left;
            font-family: "Poppins-Regular", sans-serif;
            font-size: 20px;
            font-weight: 400;
            cursor: pointer;
            padding: 10px 15px;
            transition: color 0.3s ease;
            white-space: nowrap;
            z-index: 12;
            position: relative;
        }
        .tab-button:hover {
            color: #072201;
        }
        .active-text {
            color: #ffffff !important;
            font-weight: 500;
        }
        .line-3 {
            border-top: 0.5px solid #000000;
            width: 100%;
            height: 0px;
            position: absolute;
            top: 51px;
            left: 0;
            z-index: 10;
        }
        .rectangle-146 {
            background: #072201;
            border-radius: 10px;
            height: 51px;
            position: absolute;
            top: 0px;
            left: 0px;
            transition: left 0.3s ease, width 0.3s ease, background 0.3s ease;
            z-index: 9;
        }
        .section-title {
            color: #144d09;
            font-family: "Poppins-Bold", sans-serif;
            font-size: 32px;
            font-weight: 700;
            padding-left: 15px; 
            margin-bottom: 20px; 
            padding-top: 10px;
        }
        .content-scroller {
            height: calc(100% - 52px); 
            padding-top: 70px; 
            position: absolute; 
            top: 0;
            left: 0;
            width: 100%;
            overflow-y: auto;
            padding-left: 48px; 
            padding-right: 48px;
            padding-bottom: 30px;
        }
        .content-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 0;
            padding-bottom: 20px; 
            padding-left: 15px; 
            padding-right: 15px;
            width: 100%; 
        }
        .content-container > .content-card,
        .content-container > .price-list-container,
        .content-container > .add-news-form {
            width: 100%;
            max-width: 1250px;
            position: static;
            margin-bottom: 15px;
        }
        .content-card {
            background: #f3fff5;
            border-radius: 10px;
            box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.25);
            padding: 15px;
            min-height: 139px;
        }
        .card-title {
            color: #000000;
            font-family: "Poppins-Bold", sans-serif;
            font-size: 20px;
            font-weight: 700;
            margin-bottom: 5px;
        }
        .btn-approve, .btn-deny, .btn-view, .btn-edit, .btn-delete, .btn-resolve, .btn-post, .btn-update, .btn-pickup, .btn-add-news {
            border-radius: 20px;
            width: 72px;
            height: 25px;
            border: none;
            color: #ffffff;
            font-family: "Poppins-Medium", sans-serif;
            font-size: 13px;
            font-weight: 500;
            cursor: pointer;
            margin-right: 10px;
            transition: background 0.3s ease, transform 0.1s ease;
            white-space: nowrap;
        }
        /* New/Modified Buttons */
        .btn-update { width: 90px; background: #2e7d32; }
        .btn-update:hover { background: #235e27; transform: scale(1.05); }
        .btn-pickup { width: 90px; background: #ff8f00; }
        .btn-pickup:hover { background: #cc7200; transform: scale(1.05); }
        .btn-add-news { width: 150px; height: 35px; margin-bottom: 15px; background: #072201; }
        .btn-add-news:hover { background: #144d09; transform: scale(1.05); }

        .btn-approve { background: #187605; }
        .btn-approve:hover { background: #115704; transform: scale(1.05); }
        .btn-deny { background: #db130d; }
        .btn-deny:hover { background: #b00f0b; transform: scale(1.05); }
        .btn-view { background: #1565c0; }
        .btn-view:hover { background: #0e4c9c; transform: scale(1.05); }
        .btn-edit { background: #ff8f00; }
        .btn-edit:hover { background: #cc7200; transform: scale(1.05); }
        .btn-delete { background: #c62828; }
        .btn-delete:hover { background: #9d1f1f; transform: scale(1.05); }
        .btn-resolve { background: #2e7d32; }
        .btn-resolve:hover { background: #235e27; transform: scale(1.05); }
        .btn-post { background: #187605; }
        .btn-post:hover { background: #115704; transform: scale(1.05); }

        .hidden {
            display: none !important;
        }
        .price-list-container {
            width: 100%;
            max-width: 1250px;
            background: #f3fff5;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.1);
            position: relative;
        }
        .price-table {
            width: 100%;
            border-collapse: collapse;
        }
        .price-table th, .price-table td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #e0e0e0;
            vertical-align: middle;
        }
        .price-table th {
            background-color: #c8e6c9; 
            color: #072201;
            font-family: "Poppins-Bold", sans-serif;
            font-size: 16px;
        }
        .price-table tr:nth-child(even) { background-color: #f5f5f5; }
        .price-table tr:hover { background-color: #e3f2e4; }
        
        .price-item-commodity { 
            font-weight: 500; 
            color: #072201; 
        }
        
        .price-value { font-weight: 600; color: #144d09; width: 150px; }
        .price-actions { width: 180px; text-align: left; display: flex; align-items: center; }
        .editable-price { border: 1px solid #ccc; padding: 5px; width: 100px; text-align: right; border-radius: 4px; font-weight: 500; }
        .price-status { font-size: 12px; color: #2e7d32; margin-top: 5px; font-style: italic; }

        .action-row {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        .status-dropdown {
            padding: 5px;
            margin-right: 10px;
            border-radius: 5px;
            border: 1px solid #ccc;
            font-family: "Poppins-Medium", sans-serif;
            font-size: 13px;
        }
        .add-news-form {
            background: #f3fff5;
            border-radius: 10px;
            box-shadow: 0px 4px 8px 0px rgba(0, 0, 0, 0.25);
            padding: 20px;
            margin-bottom: 25px;
        }
        .add-news-form input[type="text"],
        .add-news-form select,
        .add-news-form textarea,
        .add-news-form input[type="number"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-family: 'Poppins', sans-serif;
            font-size: 16px;
        }
        .add-news-form textarea {
            resize: vertical;
            min-height: 100px;
        }

        @media (max-width: 800px) {
            .price-list-container { padding: 10px; }
            .price-table thead { display: none; }
            .price-table, .price-table tbody, .price-table tr, .price-table td { display: block; width: 100%; }
            .price-table tr { 
                margin-bottom: 10px; 
                border: 1px solid #e0e0e0; 
                border-radius: 5px; 
            }
            .price-table td { 
                text-align: right; 
                padding-left: 50%; 
                position: relative; 
                border-bottom: none; 
            }
            .price-table td::before { 
                content: attr(data-label); 
                position: absolute; 
                left: 0; 
                width: 50%; 
                padding-left: 15px; 
                font-weight: bold; 
                text-align: left; 
            }
            .price-actions { text-align: right; padding-left: 50%; } 
            /* New: Ensure input and status fit */
            .price-table td:nth-child(2) > div { display: flex; flex-direction: column; align-items: flex-end;}
        }
    </style>
</head>
<body>
    <div class="desktop-25">
        <div class="header-container">
            <div class="rectangle-144">
                <div class="ellipse-20">
                    <img src="image/social-responsibility 1 (1).png" alt="Admin" />
                </div>
                <div class="baryo-tap">Baryo Tap</div>
                <div class="super-admin-dashboard">SUPER ADMIN DASHBOARD</div>
                <button class="logout-button" onclick="logout()">Logout</button>
                </div>
        </div>

        <div class="main-content-wrapper">
            <div class="rectangle-145">
                
                <div class="tabs-header-area">
                    <div class="tab-list">
                        <div class="rectangle-146" id="activeTabBg"></div>
                        <div class="tab-button active-text" data-section="validation" onclick="showSection('validation', this)">User Validation</div>
                        <div class="tab-button" data-section="issue" onclick="showSection('issue', this)">Reported Issues</div>
                        <div class="tab-button" data-section="document" onclick="showSection('document', this)">Document Requests</div>
                        <div class="tab-button" data-section="price" onclick="showSection('price', this)">Vegetable Prices</div>
                        <div class="tab-button" data-section="news" onclick="showSection('news', this)">News Posts</div>
                        <div class="tab-button" data-section="notification" onclick="showSection('notification', this)">Notifications</div>
                    </div>
                    <div class="line-3"></div>
                </div>

                <div class="content-scroller">
                    <div class="section-title" id="sectionTitle">User Validation</div>

                    <div id="validationContent" class="content-container">
                        <div class="content-card">
                            <div class="card-title">John Michael Santos</div>
                            <div class="card-subtitle">User Id: 10002</div>
                            <div class="card-text">📧 jmsantos@gmail.com</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-approve" onclick="handleAction('approve', 'John Michael Santos')">Approved</button>
                                <button class="btn-deny" onclick="handleAction('deny', 'John Michael Santos')">Deny</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">Maria Clara Reyes</div>
                            <div class="card-subtitle">User Id: 10003</div>
                            <div class="card-text">📧 mcreyes@gmail.com</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-approve" onclick="handleAction('approve', 'Maria Clara Reyes')">Approved</button>
                                <button class="btn-deny" onclick="handleAction('deny', 'Maria Clara Reyes')">Deny</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">Test User 4</div>
                            <div class="card-subtitle">User Id: 10004</div>
                            <div class="card-text">📧 test4@gmail.com</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-approve" onclick="handleAction('approve', 'Test User 4')">Approved</button>
                                <button class="btn-deny" onclick="handleAction('deny', 'Test User 4')">Deny</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">Test User 5 - Scroll Padding Test</div>
                            <div class="card-subtitle">User Id: 10005</div>
                            <div class="card-text">📧 test5@gmail.com</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-approve" onclick="handleAction('approve', 'Test User 5')">Approved</button>
                                <button class="btn-deny" onclick="handleAction('deny', 'Test User 5')">Deny</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">Test User 6 - Scroll Padding Test</div>
                            <div class="card-subtitle">User Id: 10006</div>
                            <div class="card-text">📧 test6@gmail.com</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-approve" onclick="handleAction('approve', 'Test User 6')">Approved</button>
                                <button class="btn-deny" onclick="handleAction('deny', 'Test User 6')">Deny</button>
                            </div>
                        </div>
                    </div> 

                    <div id="issueContent" class="content-container hidden">
                        <div class="content-card" style="text-align: center; color: #555; padding: 20px;">
                            Loading reported issues...
                        </div>
                    </div> 

                    <div id="documentContent" class="content-container hidden">
                        <div class="content-card" style="text-align: center; color: #555; padding: 20px;">
                            Loading document requests...
                        </div>
                    </div> 

                    <div id="priceContent" class="content-container hidden">
                        </div> 

                    <div id="newsContent" class="content-container hidden">
                        <div class="add-news-form">
                            <div class="card-title">Publish New News/Update Post</div>
                            <input type="text" id="newsTitle" placeholder="Title of the News/Update" required>
                            <select id="newsCategory" required>
                                <option value="" disabled selected>Select Category</option>
                                <option value="Announcement">Announcement</option>
                                <option value="Advisory">Advisory</option>
                                <option value="Event">Event</option>
                                <option value="Warning">Warning</option>
                                <option value="Other">Other</option>
                            </select>
                            <textarea id="newsContentText" placeholder="Detailed content of the news post..." required></textarea>
                            <div style="text-align: right;">
                                <button class="btn-add-news" onclick="addNewNews()">Publish Post</button>
                            </div>
                        </div>
                        </div>

                    <div id="notificationContent" class="content-container hidden">
                        <div class="add-news-form">
                            <div class="card-title">Send User Notification</div>
                            <input type="text" id="notificationRecipientId" placeholder="Recipient User ID (e.g., 10001)" required>
                            <input type="text" id="notificationTitle" placeholder="Notification Title" required>
                            <textarea id="notificationContentText" placeholder="Detailed content of the notification..." required></textarea>
                            <div style="text-align: right;">
                                <button class="btn-add-news" onclick="sendUserNotification()">Send Notification</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">System Update Completed</div>
                            <div class="card-subtitle">Notification ID: #6001 | Priority: Medium</div>
                            <div class="card-text">📅 Nov 28, 2025 - 8:00 AM</div>
                            <div class="card-text">The admin dashboard has been successfully updated with new features and security patches.</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-view" onclick="handleAction('view', 'Notification #6001')">View</button>
                                <button class="btn-delete" onclick="handleAction('delete', 'Notification #6001')">Delete</button>
                            </div>
                        </div>
                        <div class="content-card">
                            <div class="card-title">5 New User Registrations</div>
                            <div class="card-subtitle">Notification ID: #6002 | Priority: High</div>
                            <div class="card-text">📅 Nov 28, 2025 - 10:30 AM</div>
                            <div class="card-text">5 new users registered and awaiting validation. Please review and approve.</div>
                            <div style="margin-top: 10px;">
                                <button class="btn-view" onclick="handleAction('view', 'Notification #6002')">View</button>
                                <button class="btn-delete" onclick="handleAction('delete', 'Notification #6002')">Delete</button>
                            </div>
                        </div>
                    </div> 
                </div>
            </div>
        </div>
    </div>
    <script>
        // Inject PHP data into JavaScript
        let vegetablePrices = <?php echo $vegetablePricesJson; ?>;
        // NEW: Inject all news data
        let newsUpdatesAdmin = <?php echo $allNewsJson; ?>;
        // NEW: Inject all reported issues data
        let reportedIssues = <?php echo $reportedIssuesJson; ?>;
        // **NEW: Inject all document requests data**
        let documentRequests = <?php echo $documentRequestsJson; ?>;


        /**
         * Renders the table based on the local vegetablePrices array.
         * The list shows the latest price for each unique vegetable name.
         */
        function renderPriceTable() {
            const priceContentDiv = document.getElementById('priceContent');
            // Clear existing content 
            priceContentDiv.innerHTML = ''; 

            const tableHTML = `
                <div class="price-list-container">
                    <table class="price-table">
                        <thead>
                            <tr>
                                <th>Commodity</th>
                                <th>Latest Price (Php)</th>
                                <th>Date Updated</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${vegetablePrices.map(item => {
                                const nameIdentifier = item.name.replace(/\s/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
                                return `
                                    <tr data-id="${item.id}" data-name="${item.name}">
                                        <td class="price-item-commodity" data-label="Commodity">${item.name}</td>
                                        <td data-label="Latest Price (Php)">
                                            <div style="display: flex; flex-direction: column; align-items: flex-end;">
                                                <input type="number" step="0.01" value="${item.price.toFixed(2)}" id="price-input-${nameIdentifier}" class="editable-price" style="text-align: right; margin-bottom: 5px;" />
                                                <div class="price-status" id="price-status-${nameIdentifier}">Last updated: ${item.date_updated}</div>
                                            </div>
                                        </td>
                                        <td data-label="Date Updated" style="white-space: nowrap;">${item.date_updated}</td>
                                        <td class="price-actions" data-label="Action">
                                            <button class="btn-update" onclick="savePrice('${item.name}')">Save</button>
                                            <button class="btn-delete" onclick="deletePrice(${item.id}, '${item.name}')">Delete</button>
                                        </td>
                                    </tr>
                                `;
                            }).join('')}
                        </tbody>
                    </table>
                </div>
            `;
            priceContentDiv.innerHTML = tableHTML;
        }

        /**
         * Sends price update to the backend. Reloads the page on success to guarantee the table displays the newest data and ID.
         * @param {string} name - The name of the vegetable to save the price for.
         */
        async function savePrice(name) {
            const nameIdentifier = name.replace(/\s/g, '_').replace(/[^a-zA-Z0-9_]/g, '');
            const input = document.getElementById(`price-input-${nameIdentifier}`);
            const statusDiv = document.getElementById(`price-status-${nameIdentifier}`);
            const newPrice = parseFloat(input.value);

            if (isNaN(newPrice) || newPrice < 0) {
                alert('Please enter a valid price.');
                return;
            }

            // 1. Optimistic UI Update
            statusDiv.textContent = `Saving...`;
            statusDiv.style.color = '#ff8f00'; // Orange for 'pending'

            // 2. Send data to PHP endpoint (admin.php)
            try {
                const formData = new FormData();
                formData.append('action', 'insert_new_price'); // Action to perform INSERT
                formData.append('name', name); // Use the name for insertion
                formData.append('price', newPrice);
                
                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success! Price for ${name} saved successfully at Php ${newPrice.toFixed(2)}. New price history recorded.\n\nRefreshing the list now...`);
                    handleAction('New Price History', `${name} at Php ${newPrice.toFixed(2)}`);
                    // 3. CRITICAL FIX: Force a full page reload to get the new list from the server
                    location.reload(); 
                } else {
                    // 4. Revert UI on failure and show error
                    alert('❌ Error saving price: ' + result.message);
                    statusDiv.textContent = `Error! Check console.`;
                    statusDiv.style.color = '#c62828';
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while saving the price.');
                statusDiv.textContent = `Error! Check console.`;
                statusDiv.style.color = '#c62828';
            }
        }

        /**
         * Deletes the latest price entry for a vegetable.
         * @param {number} id - The Price_ID of the entry to delete.
         * @param {string} name - The name of the vegetable for confirmation/display.
         */
        async function deletePrice(id, name) {
            if (!confirm(`Are you sure you want to delete the latest price entry for ${name} (ID: ${id})? This is usually not recommended for price history.`)) {
                return;
            }

            handleAction('Delete Price History Entry (Sending)', name);

            try {
                const formData = new FormData();
                formData.append('action', 'delete_price');
                formData.append('id', id);

                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success: Latest price entry for ${name} deleted.`);
                    handleAction('Delete Price History Entry (DB)', name);
                    // Remove the item from the local array and re-render/reload
                    location.reload(); 
                } else {
                    alert('❌ Error deleting price: ' + result.message);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while deleting the price.');
            }
        }


        // --- NEWS FUNCTIONALITY (omitted for brevity, assume unchanged) ---

        /**
         * Handles news form submission. Reloads page on success to update the list with the new post ID.
         */
        async function addNewNews() {
            const title = document.getElementById('newsTitle').value;
            const category = document.getElementById('newsCategory').value;
            const content = document.getElementById('newsContentText').value;

            if (!title || !category || !content) {
                alert('Please fill in all fields (Title, Category, Content).');
                return;
            }

            try {
                const formData = new FormData();
                formData.append('action', 'insert_new_news'); // New action to insert news
                formData.append('title', title);
                formData.append('category', category);
                formData.append('content', content);

                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success! News Post: "${title}" published successfully. ID: ${result.new_id}\n\nRefreshing the list now...`);
                    handleAction('Publish News (DB)', title);
                    // For simplicity and data integrity, we reload the entire page
                    location.reload(); 
                } else {
                    alert('❌ Error publishing news: ' + result.message);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while publishing the news.');
            }
        }

        /**
         * Renders the list of news posts for the admin view dynamically.
         */
        function renderNewsContent() {
            const newsContentDiv = document.getElementById('newsContent');
            // Select and remove all previously rendered news cards (keep the form)
            let existingCards = newsContentDiv.querySelectorAll('.content-card.news-item-card');
            existingCards.forEach(card => card.remove());

            // Get the news form (the first child)
            const newsForm = newsContentDiv.querySelector('.add-news-form');
            if (newsForm) {
                // Remove all subsequent siblings (which are old news cards or the loading message)
                let nextSibling = newsForm.nextElementSibling;
                while (nextSibling) {
                    let temp = nextSibling.nextElementSibling;
                    nextSibling.remove();
                    nextSibling = temp;
                }
            }


            if (newsUpdatesAdmin.length === 0) {
                // Insert an empty message after the form
                newsContentDiv.insertAdjacentHTML('beforeend', '<div class="content-card news-item-card" style="text-align: center; color: #555; padding: 20px;">No news posts found. Publish one above.</div>');
                return;
            }

            newsUpdatesAdmin.forEach(item => {
                const cardHTML = `
                    <div class="content-card news-item-card" id="news-card-${item.News_ID}">
                        <div class="card-title">${item.Title}</div>
                        <div class="card-subtitle">Category: ${item.Category} | Post ID: #${item.News_ID}</div>
                        <div class="card-text">📅 Published: ${item.Date_Published} | By: ${item.Posted_By_Role}</div>
                        <div class="card-text" style="white-space: pre-wrap; margin-top: 10px;">${item.Content}</div>
                        <div style="margin-top: 10px;">
                            <button class="btn-edit" onclick="editNewsPost(${item.News_ID}, '${item.Title.replace(/'/g, "\\'")}')">Edit</button>
                            <button class="btn-delete" onclick="deleteNewsPost(${item.News_ID}, '${item.Title.replace(/'/g, "\\'")}')">Delete</button>
                        </div>
                    </div>
                `;
                newsContentDiv.insertAdjacentHTML('beforeend', cardHTML);
            });
        }

        /**
         * Deletes a news post via an AJAX call.
         * @param {number} id - The News_ID to delete.
         * @param {string} title - The title of the post for confirmation/display.
         */
        async function deleteNewsPost(id, title) {
            if (!confirm(`Are you sure you want to delete the news post titled: "${title}" (ID: ${id})?`)) {
                return;
            }

            handleAction('Delete News Post (Sending)', title);

            try {
                const formData = new FormData();
                formData.append('action', 'delete_news'); // The new action handler
                formData.append('id', id);

                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success: News post "${title}" deleted.`);
                    handleAction('Delete News Post', title);
                    // Remove the card from the UI (Optimistic/fast update)
                    const cardToRemove = document.getElementById(`news-card-${id}`);
                    if (cardToRemove) {
                        cardToRemove.remove();
                        // Update the local JS array so renderNewsContent is correct if run again
                        newsUpdatesAdmin = newsUpdatesAdmin.filter(item => item.News_ID != id);
                    }
                } else {
                    alert('❌ Error deleting news post: ' + result.message);
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while deleting the news post.');
            }
        }

        /**
         * Placeholder for the Edit functionality.
         */
        function editNewsPost(id, title) {
            alert(`Edit feature not fully implemented yet.\n\nEdit action triggered for post: "${title}" (ID: ${id})`);
            handleAction('Edit Placeholder', title);
        }

        // --- REPORTED ISSUE FUNCTIONALITY (omitted for brevity, assume unchanged) ---

        /**
         * Renders the reported issues dynamically using the reportedIssues array.
         */
        function renderIssuesContent() {
            const issueContentDiv = document.getElementById('issueContent');
            // Clear existing dynamic content
            issueContentDiv.innerHTML = '';
            
            // Define all possible statuses for the dropdown (Must match ENUM in DB)
            const statuses = ['Pending', 'In Progress', 'Resolved', 'Closed', 'Denied'];

            if (reportedIssues.length === 0) {
                issueContentDiv.insertAdjacentHTML('beforeend', '<div class="content-card" style="text-align: center; color: #555; padding: 20px;">No reported issues found.</div>');
                return;
            }

            reportedIssues.forEach(issue => {
                // Create dropdown options
                let optionsHTML = statuses.map(status => 
                    `<option value="${status}" ${issue.Status === status ? 'selected' : ''}>${status}</option>`
                ).join('');
                
                // Prepare image display - This section handles the "View Photo" link
                const photoHtml = issue.Photo_URL ? 
                    `<a href="${issue.Photo_URL}" target="_blank" style="color:#1565c0; font-size: 14px; font-weight: 500;">View Photo/Evidence</a>` : 
                    `<span style="color:#777; font-size: 14px;">No Photo Provided</span>`;

                const cardHTML = `
                    <div class="content-card" data-issue-id="${issue.Report_ID}">
                        <div class="card-title">${issue.Category} - ${issue.Location}</div>
                        <div class="card-subtitle">Report ID: #${issue.Report_ID} | Reported by User ID: ${issue.User_ID}</div>
                        <div class="card-text">📅 Reported: ${new Date(issue.Report_Time).toLocaleDateString()}</div>
                        <div class="card-text"><strong>Description:</strong> ${issue.Description}</div>
                        <div class="card-text">${photoHtml}</div>
                        <div class="action-row" style="margin-top: 15px;">
                            <span style="font-weight: 600; margin-right: 10px;">Status:</span>
                            <select class="status-dropdown" id="issue-status-${issue.Report_ID}">
                                ${optionsHTML}
                            </select>
                            <button class="btn-update" onclick="updateIssueStatus(${issue.Report_ID})">Update</button>
                        </div>
                    </div>
                `;
                issueContentDiv.insertAdjacentHTML('beforeend', cardHTML);
            });
        }

        /**
         * Sends an AJAX request to update the status of a reported issue in the database.
         * @param {number} id - The Report_ID of the issue to update.
         */
        async function updateIssueStatus(id) {
            const dropdown = document.getElementById(`issue-status-${id}`);
            const newStatus = dropdown.value;
            
            const originalIssue = reportedIssues.find(i => i.Report_ID === id);

            if (!confirm(`Are you sure you want to change the status of Issue #${id} to '${newStatus}'?`)) {
                // Revert dropdown selection if the user cancels
                if (originalIssue) {
                    dropdown.value = originalIssue.Status;
                }
                return;
            }

            handleAction('Update Status (Sending)', `Issue #${id} to ${newStatus}`);

            try {
                const formData = new FormData();
                formData.append('action', 'update_issue_status'); // The new action handler
                formData.append('id', id);
                formData.append('status', newStatus);
                
                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success! ${result.message}\n\nRefreshing the list now...`);
                    // Update the local JS array
                    if (originalIssue) {
                        originalIssue.Status = newStatus;
                    }
                    // For robustness and simplicity, reload the page to get fresh data
                    location.reload(); 
                } else {
                    alert('❌ Error updating status: ' + result.message);
                    // Revert dropdown selection on failure
                    if (originalIssue) {
                        dropdown.value = originalIssue.Status;
                    }
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while updating the status.');
                // Revert dropdown selection on network error
                if (originalIssue) {
                    dropdown.value = originalIssue.Status;
                }
            }
        }

        // --- NEW DOCUMENT REQUEST RENDERING & LOGIC ---

        // Define all possible statuses for the document dropdown
        const documentStatuses = ['Pending', 'Approved', 'For Pickup', 'Completed', 'Denied'];

        /**
         * Renders the document requests dynamically using the documentRequests array.
         */
        function renderDocumentRequests() {
            const documentContentDiv = document.getElementById('documentContent');
            // Clear existing content 
            documentContentDiv.innerHTML = ''; 

            if (documentRequests.length === 0) {
                documentContentDiv.insertAdjacentHTML('beforeend', '<div class="content-card" style="text-align: center; color: #555; padding: 20px;">No document requests found.</div>');
                return;
            }

            documentRequests.forEach(request => {
                const requestId = request.Request_ID;
                const status = request.Status;
                const dateRequested = request.Date_Requested;
                const purpose = request.Purpose_of_Request || 'N/A';
                
                // Create dropdown options
                let optionsHTML = documentStatuses.map(s => 
                    `<option value="${s}" ${status === s ? 'selected' : ''}>${s}</option>`
                ).join('');

                const cardHTML = `
                    <div class="content-card" data-request-id="${requestId}">
                        <div class="card-title">${request.Document_Type}</div>
                        <div class="card-subtitle">Requested by: ${request.Fullname} | Request ID: #${requestId}</div>
                        <div class="card-text">📅 Requested: ${dateRequested} | Purpose: ${purpose}</div>
                        <div class="action-row">
                            <select class="status-dropdown" id="document-status-${requestId}">
                                ${optionsHTML}
                            </select>
                            <button class="btn-update" onclick="updateDocumentStatus(${requestId})">Update</button>
                            <button class="btn-view" onclick="handleAction('view', 'Document Request #${requestId}')">View</button>
                            <button class="btn-delete" onclick="handleAction('Delete', 'Document Request #${requestId}')">Delete</button>
                        </div>
                    </div>
                `;
                documentContentDiv.insertAdjacentHTML('beforeend', cardHTML);
            });
        }

        /**
         * Sends an AJAX request to update the status of a document request.
         * @param {number} id - The Request_ID of the document to update.
         */
        async function updateDocumentStatus(id) {
            const dropdown = document.getElementById(`document-status-${id}`);
            const newStatus = dropdown.value;
            
            const originalRequest = documentRequests.find(r => r.Request_ID === id);

            if (!confirm(`Are you sure you want to change the status of Request #${id} to '${newStatus}'?`)) {
                // Revert dropdown selection if the user cancels
                if (originalRequest) {
                    dropdown.value = originalRequest.Status;
                }
                return;
            }

            handleAction('Update Document Status (Sending)', `Request #${id} to ${newStatus}`);

            try {
                const formData = new FormData();
                formData.append('action', 'update_document_status'); 
                formData.append('id', id);
                formData.append('status', newStatus);
                
                const response = await fetch('admin.php', { method: 'POST', body: formData });
                const result = await response.json();

                if (result.success) {
                    alert(`✅ Success! ${result.message}`);
                    // Update the local JS array and UI
                    if (originalRequest) {
                        originalRequest.Status = newStatus;
                    }
                    // No need to reload, the local array and UI are updated.
                } else {
                    alert('❌ Error updating status: ' + result.message);
                    // Revert dropdown selection on failure
                    if (originalRequest) {
                        dropdown.value = originalRequest.Status;
                    }
                }
            } catch (error) {
                console.error('Fetch error:', error);
                alert('A network error occurred while updating the status.');
                // Revert dropdown selection on network error
                if (originalRequest) {
                    dropdown.value = originalRequest.Status;
                }
            }
        }


        // Placeholder function (Original)
        function sendUserNotification() {
            const recipientId = document.getElementById('notificationRecipientId').value;
            const title = document.getElementById('notificationTitle').value;
            if (!recipientId || !title) {
                alert('Please enter a Recipient ID and Title.');
                return;
            }
            alert(`Notification "${title}" sent to User ID: ${recipientId}. (Functionality not connected to DB)`);
            handleAction('Send Notification', title);
        }

        // --- NEW LOGOUT FUNCTION ---
        function logout() {
            // In a real application, you would perform an AJAX call here to end the session
            // For this task, we simply redirect to the login page
            window.location.href = 'dlogin.html';
        }
        // --- END NEW LOGOUT FUNCTION ---


        // --- NAVIGATION AND INIT ---

        let currentSection = 'validation';
        let currentTabElement = null;

        // Define tab actions and titles
        const tabMap = {
            'validation': { title: 'User Validation', action: null },
            'issue': { title: 'Reported Issues', action: renderIssuesContent },
            'document': { title: 'Document Requests', action: renderDocumentRequests }, // <-- UPDATED ACTION
            'price': { title: 'Vegetable Price List', action: renderPriceTable },
            'news': { title: 'News and Updates', action: renderNewsContent },
            'notification': { title: 'Notifications', action: null }
        };


        function showSection(section, clickedElement) {
            if (currentTabElement) {
                currentTabElement.classList.remove('active-text');
            }
            
            currentSection = section;
            currentTabElement = clickedElement;

            const titleElement = document.getElementById('sectionTitle');
            const containers = document.querySelectorAll('.content-container');
            containers.forEach(container => container.classList.add('hidden'));

            const activeTabBg = document.getElementById('activeTabBg');
            const scroller = document.querySelector('.content-scroller');
            
            const sectionData = tabMap[section];
            if (sectionData) {
                titleElement.textContent = sectionData.title;
                const activeContainer = document.getElementById(section + 'Content');
                activeContainer.classList.remove('hidden');

                if (sectionData.action) {
                    // Call the rendering function for the active tab
                    sectionData.action();
                }
            }
            const clickedLeft = clickedElement.offsetLeft;
            const clickedWidth = clickedElement.offsetWidth;
            
            activeTabBg.style.left = `${clickedLeft}px`;
            activeTabBg.style.width = `${clickedWidth}px`;

            clickedElement.classList.add('active-text');

            if (scroller) {
                scroller.scrollTop = 0;
            }
        }

        function handleAction(actionType, itemName) {
            console.log(`Action: ${actionType} on item: ${itemName}`);

            if (actionType !== 'New Price History' && actionType !== 'Delete Price History Entry' && actionType !== 'Publish News (DB)' && actionType !== 'Update Status (Sending)' && actionType !== 'Update Document Status (Sending)') {
                // Keep alert only for non-frequent actions like validation/issue handling
                alert(`Action Performed: ${actionType} on ${itemName}`);
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            const initialTab = document.querySelector('.tab-button[data-section="validation"]');
            if (initialTab) {
                showSection('validation', initialTab); 
            }
        });
    </script>
</body>
</html>