<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: index.php');  // Redirect if not logged in
    exit();
}

// Retrieve user information from the database using session ID
require_once '../Database/db.php';
$stmt = $conn->prepare("SELECT * FROM iss_persons WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

echo "Welcome, " . $user['fname'] . " " . $user['lname'] . "<br>";
echo "Email: " . $user['email'] . "<br>";

// Check if the user is an admin
if ($user['admin'] === 'yes') {
    echo "You are an admin.";
} else {
    echo "You are not an admin.";
}

// Display user-related issues or other data
?>
