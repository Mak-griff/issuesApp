<?php
session_start();
require_once '../Database/db.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Getting user input
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Retrieve user from the database
    $stmt = $conn->prepare("SELECT * FROM iss_persons WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        $salt = $user['pwd_salt'];
        $hashed_password = sha1($password . $salt);  // Hashing the password with salt

        // Check if the password matches the stored hash
        if ($hashed_password === $user['pwd_hash']) {
            $_SESSION['user_id'] = $user['id'];
            header('Location: IssuesScreen.php');  // Redirect to the IssuesScreen
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
</head>
<body>
    <h2>Login</h2>
    <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

    <form action="index.php" method="POST">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <button type="submit">Login</button>
    </form>

    <br>
    <a href="join.php">Create an Account</a>
</body>
</html>
