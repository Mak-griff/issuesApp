<?php
session_start();
if (!isset($_SESSION['user_id'])){
    session_destroy();
    header("Location: index.php");
}
require_once '../database/database.php';  // Database connection file

// Get issue ID from query string
$issue_id = $_GET['id'];

// Prepare the SQL statement using PDO
$stmt = $conn->prepare("SELECT * FROM iss_issues WHERE id = :id");
$stmt->bindParam(':id', $issue_id, PDO::PARAM_INT);  // Bind the issue ID to the prepared statement

// Execute the statement
$stmt->execute();

// Fetch the issue data
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    echo "Issue not found!";
    exit();
}

// Handle adding a new comment
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_comment'])) {
    $short_comment = $_POST['short_comment'];
    $long_comment = $_POST['long_comment'];
    $per_id = $_SESSION['user_id'];
    $posted_date = date('Y-m-d');

    $insertStmt = $conn->prepare("INSERT INTO iss_comments (per_id, iss_id, short_comment, long_comment, posted_date) VALUES (:per_id, :iss_id, :short_comment, :long_comment, :posted_date)");
    $insertStmt->bindParam(':per_id', $per_id);
    $insertStmt->bindParam(':iss_id', $issue_id);
    $insertStmt->bindParam(':short_comment', $short_comment);
    $insertStmt->bindParam(':long_comment', $long_comment);
    $insertStmt->bindParam(':posted_date', $posted_date);
    $insertStmt->execute();
}

// Handle comment deletion
if (isset($_GET['delete_comment'])) {
    $commentId = $_GET['delete_comment'];

    // Check ownership
    $checkStmt = $conn->prepare("SELECT * FROM iss_comments WHERE id = :id AND per_id = :per_id");
    $checkStmt->execute([':id' => $commentId, ':per_id' => $_SESSION['user_id']]);
    $comment = $checkStmt->fetch();

    if ($comment) {
        $deleteStmt = $conn->prepare("DELETE FROM iss_comments WHERE id = :id");
        $deleteStmt->bindParam(':id', $commentId);
        $deleteStmt->execute();
    }
}

// Fetch all comments for this issue
$commentsStmt = $conn->prepare("SELECT ic.*, CONCAT(p.fname, ' ', p.lname) AS commenter_name FROM iss_comments ic JOIN iss_persons p ON ic.per_id = p.id WHERE iss_id = :issue_id ORDER BY posted_date DESC");
$commentsStmt->bindParam(':issue_id', $issue_id);
$commentsStmt->execute();
$comments = $commentsStmt->fetchAll(PDO::FETCH_ASSOC);


// Construct the PDF path
if (!empty($issue['pdf_attachment'])){
    $pdfPath = './uploads/' . $issue['pdf_attachment'];  // Assuming the PDF file is stored in './uploads/' directory
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issue Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Issue Tracker</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="commentsList.php">View Comments</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="usersList.php">View Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="issuesList.php">View Issues</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Issue Details</h2>

        <div class="card">
            <div class="card-body">
                <p><strong>Short Description:</strong> <?php echo htmlspecialchars($issue['short_description']); ?></p>
                <p><strong>Long Description:</strong> <?php echo htmlspecialchars($issue['long_description']); ?></p>
                <p><strong>Open Date:</strong> <?php echo htmlspecialchars($issue['open_date']); ?></p>
                <p><strong>Close Date:</strong> <?php echo htmlspecialchars($issue['close_date']); ?></p>
                <p><strong>Priority:</strong> <?php echo htmlspecialchars($issue['priority']); ?></p>
                <p><strong>Organization:</strong> <?php echo htmlspecialchars($issue['org']); ?></p>
                <p><strong>Project:</strong> <?php echo htmlspecialchars($issue['project']); ?></p>
                <p><strong>Assigned Person:</strong> <?php echo htmlspecialchars($issue['per_id']); ?></p>

                <!-- Check if PDF is attached and generate the link -->
                <?php if (!empty($issue['pdf_attachment']) && file_exists($pdfPath)): ?>
                    <p><strong>PDF Attachment:</strong> 
                        <a href="<?php echo $pdfPath; ?>" target="_blank" class="btn btn-info btn-sm">View PDF</a>
                    </p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Comments Section -->
<div class="mt-5">
    <h4>Comments</h4>

    <!-- Display Comments -->
    <?php if (count($comments) > 0): ?>
        <?php foreach ($comments as $comment): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5 class="card-title"><?php echo htmlspecialchars($comment['short_comment']); ?></h5>
                    <p class="card-text"><?php echo nl2br(htmlspecialchars($comment['long_comment'])); ?></p>
                    <p class="card-text">
                        <small class="text-muted">Posted by <?php echo htmlspecialchars($comment['commenter_name']); ?> on <?php echo htmlspecialchars($comment['posted_date']); ?></small>
                    </p>

                    <!-- Delete button for user's own comments -->
                    <?php if ($comment['per_id'] == $_SESSION['user_id']): ?>
                        <a href="?id=<?php echo $issue_id; ?>&delete_comment=<?php echo $comment['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this comment?')">Delete</a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="text-muted">No comments yet.</p>
    <?php endif; ?>

    <!-- Add Comment Form -->
    
    <form action="" method="POST" class="mb-4">
        <input type="hidden" name="add_comment" value="1">
        <div class="card">
            <div class="card-body">
                <h5>New Comment</h5>
                <div class="mb-3">
                    <label for="short_comment" class="form-label">Short Comment</label>
                    <input type="text" class="form-control" id="short_comment" name="short_comment" required>
                </div>
                <div class="mb-3">
                    <label for="long_comment" class="form-label">Long Comment</label>
                    <textarea class="form-control" id="long_comment" name="long_comment" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Post Comment</button>
            </div>
        </div>
    </form>
</div>


        <a href="issuesList.php" class="btn btn-secondary mt-3">Back to Issues List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
