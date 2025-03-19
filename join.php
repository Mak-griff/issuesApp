<?php
session_start();
require_once '../Database/db.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $fName = $_POST['fName'];
    $lName = $_POST['lName'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $admin = $_POST['admin'];

    // Generate salt and hash password
    $salt = bin2hex(random_bytes(16));  // 16-byte random salt
    $pwd_hash = sha1($password . $salt);  // Hash the password with the salt

    // Prepare insert statement using named placeholders
    $stmt = $conn->prepare("INSERT INTO iss_persons (fName, lName, mobile, email, pwd_hash, pwd_salt, admin) VALUES (:fName, :lName, :mobile, :email, :pwd_hash, :pwd_salt, :admin)");

    // Bind parameters
    $stmt->bindParam(':fName', $fName, PDO::PARAM_STR);
    $stmt->bindParam(':lName', $lName, PDO::PARAM_STR);
    $stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':pwd_hash', $pwd_hash, PDO::PARAM_STR);
    $stmt->bindParam(':pwd_salt', $salt, PDO::PARAM_STR);
    $stmt->bindParam(':admin', $admin, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        header('Location: index.php');  // Redirect to the login page after successful registration
        exit();
    } else {
        $error_message = "Error creating account. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Create an Account</h3>
                    </div>
                    <div class="card-body">

                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>

                        <form action="join.php" method="POST">
                            <div class="mb-3">
                                <label for="fName" class="form-label">First Name:</label>
                                <input type="text" name="fName" class="form-control" id="fName" required>
                            </div>

                            <div class="mb-3">
                                <label for="lName" class="form-label">Last Name:</label>
                                <input type="text" name="lName" class="form-control" id="lName" required>
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile:</label>
                                <input type="text" name="mobile" class="form-control" id="mobile" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" id="email" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">Password:</label>
                                <input type="password" name="password" class="form-control" id="password" required>
                            </div>

                            <div class="mb-3">
                                <label for="admin" class="form-label">Admin (yes/no):</label>
                                <input type="text" name="admin" class="form-control" id="admin" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Create Account</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="index.php" class="text-decoration-none">Already have an account? Login here.</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
