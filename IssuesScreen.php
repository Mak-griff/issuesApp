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
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center">Issues List</h2>
        <a href="create.php" class="btn btn-primary">Create New Issue</a>

        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Short Description</th>
                    <th>Open Date</th>
                    <th>Close Date</th>
                    <th>Priority</th>
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
                        <td><?php echo $issue['short_description']; ?></td>
                        <td><?php echo $issue['open_date']; ?></td>
                        <td><?php echo $issue['close_date']; ?></td>
                        <td><?php echo $issue['priority']; ?></td>
                        <td>
                            <a href="detailsScreen.php?id=<?php echo $issue['id']; ?>" class="btn btn-info btn-sm">View Details</a>
                            <a href="update.php?id=<?php echo $issue['id']; ?>" class="btn btn-warning btn-sm">Update</a>
                            <a href="delete.php?id=<?php echo $issue['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this issue?');">Delete</a>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
