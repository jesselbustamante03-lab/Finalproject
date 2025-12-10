<?php
header('Content-Type: application/json');

// 1. Database Configuration (MUST match XAMPP setup)
$servername = "localhost";
$username = "root";     
$password = "";         // IMPORTANT: If you set a password for root, enter it here.
$dbname = "baryotap";
$tableName = "vegetable_price"; // MATCHES table name from your admin code

// 2. Get the date from the AJAX request
$input_date = isset($_GET['date']) ? $_GET['date'] : null;

if (!$input_date) {
    http_response_code(400);
    // This error means the JavaScript fetch request failed to include the date parameter.
    echo json_encode(['error' => 'Date parameter is missing in the request.']);
    exit();
}

// 3. Connect to the database using mysqli
$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    // 🛑 If connection fails, this error is displayed.
    http_response_code(500);
    // Displaying the connection error helps with debugging
    echo json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// 4. Prepare the query using the user's table and columns
// We select Vegetable_Name and Price based on the Date_Updated column.
$sql = "SELECT Vegetable_Name, Price FROM " . $tableName . " WHERE Date_Updated = ?";

$stmt = $conn->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(['error' => 'SQL prepare failed: ' . $conn->error]);
    $conn->close();
    exit();
}

// Bind the date parameter (s = string)
$stmt->bind_param("s", $input_date);
$stmt->execute();
$result = $stmt->get_result();

$prices = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Map the database column names (e.g., Vegetable_Name) 
        // to the keys the JavaScript expects (e.g., vegetable_name)
        $prices[] = [
            'vegetable_name' => $row['Vegetable_Name'],
            'price' => (float) $row['Price']
        ];
    }
}

// 5. Close connections and return JSON
$stmt->close();
$conn->close();

if (count($prices) > 0) {
    echo json_encode($prices);
} else {
    // If no data is found for the date, return a clean message
    echo json_encode(['error' => 'No prices found for this date.']);
}
?>