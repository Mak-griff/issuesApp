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

    // Prepare insert statement
    $stmt = $conn->prepare("INSERT INTO iss_persons (fName, lName, mobile, email, pwd_hash, pwd_salt, admin) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssss", $fName, $lName, $mobile, $email, $pwd_hash, $salt, $admin);

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
</head>
<body>
    <h2>Create an Account</h2>
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <form action="join.php" method="POST">
        <label for="fName">First Name:</label>
        <input type="text" name="fName" required><br><br>

        <label for="lName">Last Name:</label>
        <input type="text" name="lName" required><br><br>

        <label for="mobile">Mobile:</label>
        <input type="text" name="mobile" required><br><br>

        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <label for="admin">Admin (yes/no):</label>
        <input type="text" name="admin" required><br><br>

        <button type="submit">Create Account</button>
    </form>

    <br>
    <a href="index.php">Already have an account? Login here.</a>
</body>
</html>
