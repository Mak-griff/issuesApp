<?php
session_start();
if (!isset($_SESSION['user_id'])){
    session_destroy();
    header("Location: index.php");
}
require_once '../database/database.php';  // Database connection file

// Fetch the user's current data from the database (use the session user ID or query string parameter)
$user_id = $_SESSION['user_id']; // Assuming the user ID is stored in session after login

// Fetch user data from the database
$stmt = $conn->prepare("SELECT * FROM iss_persons WHERE id = :id");
$stmt->bindParam(':id', $user_id, PDO::PARAM_INT);
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// If the user doesn't exist, redirect or show an error
if (!$user) {
    echo "User not found!";
    exit;
}

// If the form is submitted, update the user's data
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $fname = $_POST['fname'];
    $lname = $_POST['lname'];
    $mobile = $_POST['mobile'];
    $email = $_POST['email'];
    $password = $_POST['password'];  // Optional: Only update if password is provided
    $admin = $_POST['admin'];

    // If password is provided, hash it
    if (!empty($password)) {
        $salt = bin2hex(random_bytes(16));  // 16-byte random salt
        $pwd_hash = sha1($password . $salt);  // Hash the password with the salt
    } else {
        // If no password is provided, keep the old password
        $pwd_hash = $user['pwd_hash'];
        $salt = $user['pwd_salt'];
    }

    // Prepare update statement
    $stmt = $conn->prepare("UPDATE iss_persons SET fname = :fname, lname = :lname, mobile = :mobile, email = :email, pwd_hash = :pwd_hash, pwd_salt = :pwd_salt, admin = :admin WHERE id = :id");
    
    // Bind parameters
    $stmt->bindParam(':fname', $fname, PDO::PARAM_STR);
    $stmt->bindParam(':lname', $lname, PDO::PARAM_STR);
    $stmt->bindParam(':mobile', $mobile, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
    $stmt->bindParam(':pwd_hash', $pwd_hash, PDO::PARAM_STR);
    $stmt->bindParam(':pwd_salt', $salt, PDO::PARAM_STR);
    $stmt->bindParam(':admin', $admin, PDO::PARAM_STR);
    $stmt->bindParam(':id', $user_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to profile or some success page after updating
        header('Location: usersList.php');
        exit();
    } else {
        $error_message = "Error updating account. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Account</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow-lg">
                    <div class="card-header bg-primary text-white text-center">
                        <h3>Update Your Account</h3>
                    </div>
                    <div class="card-body">

                        <?php if (isset($error_message)) { echo "<div class='alert alert-danger'>$error_message</div>"; } ?>

                        <form action="updateUser.php" method="POST">
                            <div class="mb-3">
                                <label for="fname" class="form-label">First Name:</label>
                                <input type="text" name="fname" class="form-control" id="fname" value="<?php echo htmlspecialchars($user['fname']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="lname" class="form-label">Last Name:</label>
                                <input type="text" name="lname" class="form-control" id="lname" value="<?php echo htmlspecialchars($user['lname']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="mobile" class="form-label">Mobile:</label>
                                <input type="text" name="mobile" class="form-control" id="mobile" value="<?php echo htmlspecialchars($user['mobile']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email:</label>
                                <input type="email" name="email" class="form-control" id="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password (Leave empty to keep the current password):</label>
                                <input type="password" name="password" class="form-control" id="password">
                            </div>

                            <div class="mb-3">
                                <label for="admin" class="form-label">Admin (yes/no):</label>
                                <input type="text" name="admin" class="form-control" id="admin" value="<?php echo htmlspecialchars($user['admin']); ?>" required>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">Update Account</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <a href="usersList.php" class="text-decoration-none">Back to Users List</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
