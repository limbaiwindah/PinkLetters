
<?php
$host = "localhost";
$username = "root";
$password = "";
$dbname = "pinkletters";

// connect to MySQL
$conn = new mysqli($host, $username, $password, $dbname);

// check connection
if ($conn->connect_error) {
    die ("Connection failed: " . $conn->connect_error);
}

// SQL to create table
$sql = "CREATE TABLE letters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL,
    delivery_date DATE NOT NULL,
    created_at DATETIME NOT NULL,
    message TEXT NOT NULL,
    sent TINYINT(1) DEFAULT 0 /* 0=false (not sent), 1=true (sent)*/
)";

if ($conn->query($sql) === TRUE) {
    echo "Table 'letters' created successfully.";
} else {
    echo "Error creating table" . $conn->error;
}

$conn->close();

?>
