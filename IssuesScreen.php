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
        <a href="logout.php" class="btn btn-secondary mt-3" style="margin-bottom: 5px;">Logout</a>
        </br>
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
                            <?php 
                            $query = $conn->prepare("SELECT * FROM iss_persons WHERE id = :id");
                            $query->bindValue(':id', $_SESSION['user_id'], PDO::PARAM_STR);
                            $query->execute();
                            $currentUser = $query->fetch(PDO::FETCH_ASSOC);

                            // only admin and issue assignee should be able to update/delete an issue
                            if($_SESSION['user_id'] == $issue['per_id'] || $currentUser['admin'] == 'yes'){
                                echo '<a href="update.php?id=' . $issue['id'] . '" class="btn btn-warning btn-sm">Update</a>';
                                echo ('<a href="delete.php?id=' . $issue['id'] . '" class="btn btn-danger btn-sm" onclick="return confirm(' . 'Are you sure you want to delete this issue?' . ');">Delete</a>');
                            }
                            ?>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
