<?php
session_start();
require_once '../Database/db.php';  // Database connection file

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You are not logged in.";
    exit;
}

// Get the current logged-in user's ID from the session
$user_id = $_SESSION['user_id'];

// Check if the user is deleting themselves
$isDeletingSelf = false;

if (isset($_GET['user_id'])) {
    $user_to_delete = $_GET['user_id'];
    if ($user_id == $user_to_delete) {
        $isDeletingSelf = true;
    }
} else {
    echo "No user selected for deletion.";
    exit;
}

// Fetch user details (optional)
$stmt = $conn->prepare("SELECT * FROM iss_persons WHERE id = :id");
$stmt->bindParam(':id', $user_to_delete, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "User not found.";
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Start a transaction to ensure atomic deletion
    try {
        $conn->beginTransaction();

        // Delete the user from the 'iss_persons' table
        $stmt = $conn->prepare("DELETE FROM iss_persons WHERE id = :id");
        $stmt->bindParam(':id', $user_to_delete, PDO::PARAM_INT);
        $stmt->execute();

        // Commit the transaction
        $conn->commit();

        // If the user is deleting themselves, log them out
        if ($isDeletingSelf) {
            // Destroy the session
            session_unset();
            session_destroy();

            // Redirect to the login page after successful logout
            header('Location: ../index.php');
            exit;
        }

        // If not deleting themselves, redirect back to the admin dashboard (or relevant page)
        header('Location: usersList.php'); // Change this to your users list page
        exit;
    } catch (Exception $e) {
        // If an error occurs, rollback the transaction
        $conn->rollBack();
        echo "Error deleting user: " . $e->getMessage();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delete User</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Delete User</h2>

        <!-- Show user details -->
        <p><strong>First Name:</strong> <?php echo htmlspecialchars($user['fname']); ?></p>
        <p><strong>Last Name:</strong> <?php echo htmlspecialchars($user['lname']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>

        <!-- Warning and confirmation form -->
        <div class="alert alert-danger mt-4">
            <h4>Warning!</h4>
            <p>Are you sure you want to delete this user's account? This action cannot be undone.</p>
            <?php if ($isDeletingSelf): ?>
                <p><strong>Note:</strong> If you delete your account, you will be logged out and redirected to the login page.</p>
            <?php endif; ?>
        </div>

        <form method="POST" onsubmit="return confirm('Are you sure you want to delete this user? This action is irreversible.');">
            <button type="submit" class="btn btn-danger">Delete Account</button>
        </form>

        <a href="usersList.php" class="btn btn-secondary mt-3">Cancel and Go Back</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
