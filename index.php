<?php
session_start();
require_once '../database/database.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from the database using PDO
    $stmt = $conn->prepare("SELECT * FROM iss_persons WHERE email = :email");
    $stmt->bindValue(':email', $email, PDO::PARAM_STR);  // Bind the email parameter
    
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result) {
        $user = $result;
        $salt = $user['pwd_salt'];
        $hashed_password = sha1($password . $salt);  // Hashing the password with salt

        // Check if the password matches the stored hash
        if ($hashed_password === $user['pwd_hash']) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['admin'] = $user['admin'];
            header('Location: issuesList.php');  // Redirect to the issuesList
            exit();
        } else {
            $error_message = "Invalid password.";
        }
    } else {
        $error_message = "No account found with that email.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <p><i>Welcome to Issues Tracker!</i></p>
                        <h3>Login</h3> 
                    </div>
                    <div class="card-body">
                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>
                        <form action="index.php" method="POST">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="join.php" class="text-decoration-none">Create an Account</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
