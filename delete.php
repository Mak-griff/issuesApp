<?php
session_start();
if (!isset($_SESSION['user_id'])){
    session_destroy();
    header("Location: index.php");
}
require_once '../database/database.php';  // Database connection file

// Check if the issue_id is passed in the URL
if (isset($_GET['id'])) {
    $query = $conn->prepare("SELECT * FROM iss_issues WHERE id = :id");
    $query->bindValue(':id', $_GET['id'], PDO::PARAM_STR);
    $query->execute();
    $issue = $query->fetch(PDO::FETCH_ASSOC);

    // only admin or the user associated with the issue are able to update the issue
    if(!($_SESSION['admin'] == "yes" || $_SESSION['user_id'] == $issue['per_id'])){
        header('Location: issuesList.php');
        exit();
    }
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
