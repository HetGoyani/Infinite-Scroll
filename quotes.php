<?php

// Hetkumar Goyani

// Database connection parameters
$host = "localhost";
$uname = "sa000885637";
$pass = "Sa_20030922";
$dbname = "sa000885637";

try {
    // Create a PDO database connection
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $uname, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If connection fails, terminate the script and display an error message
    die("Connection failed: " . $e->getMessage());
}

// Set the number of quotes to retrieve per page
$per_page = 20;

// Get the page number from the AJAX call, ensuring it is a positive integer
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;

// Calculate the offset
$offset = ($page - 1) * $per_page;

// Query to retrieve quotes and authors
$query = "SELECT quotes.quote_text, authors.author_name
          FROM quotes
          JOIN authors ON quotes.author_id = authors.author_id
          LIMIT :per_page
          OFFSET :offset";

$stmt = $conn->prepare($query);
$stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();

// Fetch quotes as associative array
$quotes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Function to generate a Bootstrap 5 Card HTML string
function generateCard($quote, $author)
{
    return '<div class="card mb-3 a4card w-100">
                <div class="card-header">' . htmlspecialchars($author) . '</div>
                <div class="card-body d-flex align-items-center">
                    <p class="card-text w-100">' . htmlspecialchars($quote) . '</p>
                </div>
            </div>';
}

// Generate an array of HTML cards
$htmlCards = array_map(function ($quote) {
    return generateCard($quote['quote_text'], $quote['author_name']);
}, $quotes);

// Encode the array as JSON and send it as the response
header('Content-Type: application/json');
echo json_encode($htmlCards);
?>