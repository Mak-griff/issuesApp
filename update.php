<?php
session_start();
require_once '../Database/db.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get user input
    $issue_id = $_POST['issue_id'];
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $open_date = $_POST['open_date'];
    $close_date = $_POST['close_date'];
    $priority = $_POST['priority'];
    $org = $_POST['org'];
    $project = $_POST['project'];
    $per_id = $_POST['per_id'];

    // Prepare the SQL statement
    $stmt = $conn->prepare("UPDATE iss_issues SET short_description = :short_description, long_description = :long_description, open_date = :open_date, close_date = :close_date, priority = :priority, org = :org, project = :project, per_id = :per_id WHERE id = :id");

    // Bind parameters
    $stmt->bindParam(':short_description', $short_description, PDO::PARAM_STR);
    $stmt->bindParam(':long_description', $long_description, PDO::PARAM_STR);
    $stmt->bindParam(':open_date', $open_date, PDO::PARAM_STR);
    $stmt->bindParam(':close_date', $close_date, PDO::PARAM_STR);
    $stmt->bindParam(':priority', $priority, PDO::PARAM_STR);
    $stmt->bindParam(':org', $org, PDO::PARAM_STR);
    $stmt->bindParam(':project', $project, PDO::PARAM_STR);
    $stmt->bindParam(':per_id', $per_id, PDO::PARAM_INT);
    $stmt->bindParam(':id', $issue_id, PDO::PARAM_INT);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the issues list page after the update
        header('Location: IssuesScreen.php');
        exit();
    } else {
        $error_message = "Error updating issue. Please try again.";
    }
} else {
    // If not a POST request, fetch the issue data to populate the form for editing
    $issue_id = $_GET['id'];

    $stmt = $conn->prepare("SELECT * FROM iss_issues WHERE id = :id");
    $stmt->bindParam(':id', $issue_id, PDO::PARAM_INT);
    $stmt->execute();

    $issue = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$issue) {
        echo "Issue not found!";
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Issue</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

    <div class="container mt-5">
        <h2 class="text-center">Update Issue</h2>

        <?php if (isset($error_message)) { echo "<p style='color:red;'>$error_message</p>"; } ?>

        <form action="update.php" method="POST">
            <input type="hidden" name="issue_id" value="<?php echo htmlspecialchars($issue['id']); ?>">

            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <input type="text" class="form-control" id="short_description" name="short_description" value="<?php echo htmlspecialchars($issue['short_description']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="long_description" class="form-label">Long Description</label>
                <textarea class="form-control" id="long_description" name="long_description" required><?php echo htmlspecialchars($issue['long_description']); ?></textarea>
            </div>

            <div class="mb-3">
                <label for="open_date" class="form-label">Open Date</label>
                <input type="date" class="form-control" id="open_date" name="open_date" value="<?php echo htmlspecialchars($issue['open_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="close_date" class="form-label">Close Date</label>
                <input type="date" class="form-control" id="close_date" name="close_date" value="<?php echo htmlspecialchars($issue['close_date']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <input type="text" class="form-control" id="priority" name="priority" value="<?php echo htmlspecialchars($issue['priority']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="org" class="form-label">Organization</label>
                <input type="text" class="form-control" id="org" name="org" value="<?php echo htmlspecialchars($issue['org']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="project" class="form-label">Project</label>
                <input type="text" class="form-control" id="project" name="project" value="<?php echo htmlspecialchars($issue['project']); ?>" required>
            </div>

            <div class="mb-3">
                <label for="per_id" class="form-label">Assigned Person</label>
                <select class="form-control" id="per_id" name="per_id" required>
                    <option value="" disabled selected></option>
                    <!-- Populate the dropdown with users from the iss_persons table -->
                    <?php
                    $person_stmt = $conn->query("SELECT id, CONCAT(fname, ' ', lname) AS full_name FROM iss_persons");
                    while ($person = $person_stmt->fetch(PDO::FETCH_ASSOC)) {
                        $selected = $person['id'] == $issue['per_id'] ? 'selected' : '';
                        echo "<option value='{$person['id']}' {$selected}>{$person['full_name']}</option>";
                    }
                    ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Update Issue</button>
        </form>

        <a href="IssuesScreen.php" class="btn btn-secondary mt-3">Back to Issues List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
