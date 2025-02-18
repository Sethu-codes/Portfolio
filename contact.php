<?php
// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers to accept requests from your domain
header("Access-Control-Allow-Origin: sethu-codes.github.io"); // Replace * with your domain in production
header("Access-Control-Allow-Headers: Content-Type");
header("Content-Type: application/json");

// Check if it's a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Get JSON data from request body
$data = json_decode(file_get_contents('php://input'), true);

// Validate required fields
if (!isset($data['name']) || !isset($data['email']) || !isset($data['subject']) || !isset($data['message'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// Validate email format
if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Invalid email format']);
    exit;
}

// Prepare email content
$to = 'your-email@example.com'; // Replace with your email
$subject = 'Contact Form: ' . htmlspecialchars($data['subject']);
$headers = 'From: ' . htmlspecialchars($data['name']) . ' <' . $data['email'] . '>' . "\r\n" .
    'Reply-To: ' . $data['email'] . "\r\n" .
    'X-Mailer: PHP/' . phpversion();

$message = "Name: " . htmlspecialchars($data['name']) . "\n" .
           "Email: " . htmlspecialchars($data['email']) . "\n" .
           "Subject: " . htmlspecialchars($data['subject']) . "\n\n" .
           "Message:\n" . htmlspecialchars($data['message']);

// Send email
$success = mail($to, $subject, $message, $headers);

if ($success) {
    echo json_encode(['status' => 'success', 'message' => 'Thank you! Your message has been sent successfully.']);
} else {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => 'Failed to send email. Please try again later.']);
}
?>