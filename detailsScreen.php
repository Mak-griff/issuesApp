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

        <a href="issuesList.php" class="btn btn-secondary mt-3">Back to Issues List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
