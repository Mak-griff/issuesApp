<?php
session_start();
require_once '../Database/db.php';  // Database connection file

// Fetch users from the database using PDO
$query = "SELECT * FROM iss_persons";
$stmt = $conn->query($query);  // PDO::query() method executes the query
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>users List</title>
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
        <h2 class="text-center">Users List</h2>
        
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Phone #</th>
                    <th>Email</th>
                    <th>Admin Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch each user and display it
                while ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {  // Fetch associative array
                ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['lname'] . ", " . $user['fname']; ?></td>
                        <td><?php echo $user['mobile']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['admin']; ?></td>
                        <td>
                        <?php 
                            // Only admin and user assignee should be able to update/delete an user
                            if ($_SESSION['user_id'] == $user['id'] || $_SESSION['admin'] == 'yes') {
                                echo '<a href="updateUser.php?id=' . $user['id'] . '" class="btn btn-warning btn-sm">Update</a>';
                                echo ('<a href="deleteUser.php?user_id=' . $user['id'] . '" class="btn btn-danger btn-sm">Delete</a>');
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
