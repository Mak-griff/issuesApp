<?php
session_start();
require_once '../Database/db.php';  // Database connection file

// Fetch issues from the database using PDO
$query = "SELECT * FROM iss_issues";
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
        .btn-create {
            background-color: #28a745;
            color: white;
        }

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
                    <li class="nav-item">
                        <a class="btn btn-create nav-link" href="create.php">Create New Issue</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center">Issues List</h2>
        
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Short Description</th>
                    <th>Open Date</th>
                    <th>Close Date</th>
                    <th>Priority</th>
                    <th>Actions</th>
                    <th>PDF Attachment</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch each issue and display it
                while ($issue = $stmt->fetch(PDO::FETCH_ASSOC)) {  // Fetch associative array
                ?>
                    <tr>
                        <td><?php echo $issue['id']; ?></td>
                        <td><?php echo $issue['short_description']; ?></td>
                        <td><?php echo $issue['open_date']; ?></td>
                        <td><?php echo $issue['close_date']; ?></td>
                        <td><?php echo $issue['priority']; ?></td>
                        <td>
                            <a href="detailsScreen.php?id=<?php echo $issue['id']; ?>" class="btn btn-info btn-sm">View</a>
                            <?php 
                            // Only admin and issue assignee should be able to update/delete an issue
                            if ($_SESSION['user_id'] == $issue['per_id'] || $_SESSION['admin'] == 'yes') {
                                echo ('<a href="update.php?id=' . $issue['id'] . '" class="btn btn-warning btn-sm">Update</a>');
                                echo ('<a href="delete.php?id=' . $issue['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(' . 'Are you sure you want to delete this issue?' . ');">Delete</a>');
                            }
                            ?>
                        </td>
                        <td><?php
                            $pdfPath = './uploads/' . $issue['pdf_attachment'];
                            if (!empty($issue['pdf_attachment']) && file_exists($pdfPath)) {
                                // Show the PDF link if a valid attachment exists
                                echo '<a href="' . $pdfPath . '" target="_blank" class="btn btn-info btn-sm">View PDF</a>';
                            } else {
                                // Display no attachment message if no PDF is attached
                                echo "No attachment";
                            }
                        ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
