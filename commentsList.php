<?php
session_start();
require_once '../Database/db.php';  // Database connection file

// Fetch issues from the database using PDO
$query = "SELECT * FROM iss_comments";
$stmt = $conn->query($query);  // PDO::query() method executes the query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Issues List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .navbar {
            margin-bottom: 20px;
        }
    </style>
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
        <h2 class="text-center">Comments List</h2>
        
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Commenter ID</th>
                    <th>Issue ID</th>
                    <th>Short Comment</th>
                    <th>Posted Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch each issue and display it
                while ($issue = $stmt->fetch(PDO::FETCH_ASSOC)) {  // Fetch associative array
                ?>
                    <tr>
                        <td><?php echo $issue['id']; ?></td>
                        <td><?php echo $issue['per_id']; ?></td>
                        <td><?php echo $issue['iss_id']; ?></td>
                        <td><?php echo $issue['short_comment']; ?></td>
                        <td><?php echo $issue['posted_date']; ?></td>
                        <td>
                            <a href="detailsScreen.php?id=<?php echo $issue['iss_id']; ?>" class="btn btn-info btn-sm">View Issue</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
