<?php
session_start();
require_once '../Database/db.php';  // Database connection file

// Check if the issue_id is passed in the URL
if (isset($_GET['id'])) {
    $issue_id = $_GET['id'];

    // Prepare the DELETE SQL statement using PDO
    $stmt = $conn->prepare("DELETE FROM iss_issues WHERE id = :id");

    // Bind the issue ID parameter to the statement
    $stmt->bindParam(':id', $issue_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the issues list page after deletion
        header('Location: issuesList.php');
        exit();
    } else {
        echo "Error deleting issue. Please try again.";
    }
} else {
    echo "No issue ID provided.";
}
?>
