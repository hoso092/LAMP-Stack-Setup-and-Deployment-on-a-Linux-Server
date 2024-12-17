
<?php
// Database connection details
$host = "localhost"; // MySQL host
$user = "web_user";  // MySQL username
$password = "StrongPassword123"; // MySQL password
$dbname = "web_db";  // Database name

// Establish connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("<div style='color: red; font-weight: bold;'>Connection failed: " . $conn->connect_error . "</div>");
}

// Create a simple table if it doesn't exist
$sql = "CREATE TABLE IF NOT EXISTS visitors (
            id INT AUTO_INCREMENT PRIMARY KEY,
            ip_address VARCHAR(50),
            visit_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";

if ($conn->query($sql) === FALSE) {
    die("<div style='color: red; font-weight: bold;'>Error creating table: " . $conn->error . "</div>");
}

// Get the current visitor's IP address
$ip_address = $_SERVER['REMOTE_ADDR'];

// Insert visitor's IP address into the table
$insert = "INSERT INTO visitors (ip_address) VALUES ('$ip_address')";
if ($conn->query($insert) === FALSE) {
    die("<div style='color: red; font-weight: bold;'>Error inserting data: " . $conn->error . "</div>");
}

// Fetch the current time (this can be done in PHP or from MySQL)
$current_time = date("Y-m-d H:i:s");

// Styling for the page
echo "<!DOCTYPE html>";
echo "<html lang='en'>";
echo "<head>";
echo "<meta charset='UTF-8'>";
echo "<meta name='viewport' content='width=device-width, initial-scale=1.0'>";
echo "<title>Welcome to Our Website</title>";
echo "<style>";
echo "body { font-family: Arial, sans-serif; background-color: #f4f4f9; color: #333; margin: 0; padding: 20px; }";
echo "h1 { color: #5c6bc0; }";
echo "h2 { color: #3e4a89; }";
echo ".container { max-width: 800px; margin: auto; background-color: #fff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }";
echo ".visitor-log { margin-top: 30px; background-color: #e8f0fe; padding: 15px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }";
echo ".visitor-log table { width: 100%; border-collapse: collapse; margin-top: 15px; }";
echo ".visitor-log table, .visitor-log th, .visitor-log td { border: 1px solid #ccc; }";
echo ".visitor-log th, .visitor-log td { padding: 10px; text-align: left; }";
echo ".visitor-log th { background-color: #e3f2fd; color: #333; }";
echo ".footer { text-align: center; margin-top: 30px; font-size: 0.9em; color: #777; }";
echo "</style>";
echo "</head>";
echo "<body>";

echo "<div class='container'>";
echo "<h1>Welcome to Our Website</h1>";
echo "<p>Your IP Address: <strong>$ip_address</strong></p>";
echo "<p>Current Time: <strong>$current_time</strong></p>";

// Retrieve and display all visits from the database
$result = $conn->query("SELECT * FROM visitors");
if ($result->num_rows > 0) {
    echo "<div class='visitor-log'>";
    echo "<h2>Visitor Log:</h2>";
    echo "<table>";
    echo "<tr><th>ID</th><th>IP Address</th><th>Visit Time</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $row['id'] . "</td>";
        echo "<td>" . $row['ip_address'] . "</td>";
        echo "<td>" . $row['visit_time'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
    echo "</div>";
}

echo "</div>";

// Footer Section
echo "<div class='footer'>Thank you for visiting our website! &copy; " . date("Y") . "</div>";

echo "</body>";
echo "</html>";

// Close the connection
$conn->close();
?>

