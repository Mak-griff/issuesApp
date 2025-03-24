<?php
include('../Database/db.php');

// Fetch persons for the dropdown
$stmt = $conn->prepare("SELECT id, CONCAT(fname, ' ', lname) AS full_name FROM iss_persons");
$stmt->execute();
$persons = $stmt->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Process the form data
    $short_description = $_POST['short_description'];
    $long_description = $_POST['long_description'];
    $open_date = $_POST['open_date'];
    $close_date = $_POST['close_date'];
    $priority = $_POST['priority'];
    $org = $_POST['org'];
    $project = $_POST['project'];
    $per_id = $_POST['per_id'];

    // Insert the issue into the database
    $stmt = $conn->prepare("INSERT INTO iss_issues (short_description, long_description, open_date, close_date, priority, org, project, per_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$short_description, $long_description, $open_date, $close_date, $priority, $org, $project, $per_id]);

    // Redirect to the index page after adding the issue
    header('Location: issuesScreen.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Issue</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Create New Issue</h2>
        <form method="POST">
            <div class="mb-3">
                <label for="short_description" class="form-label">Short Description</label>
                <input type="text" class="form-control" id="short_description" name="short_description" required>
            </div>
            <div class="mb-3">
                <label for="long_description" class="form-label">Long Description</label>
                <textarea class="form-control" id="long_description" name="long_description" rows="3" required></textarea>
            </div>
            <div class="mb-3">
                <label for="open_date" class="form-label">Open Date</label>
                <input type="date" class="form-control" id="open_date" name="open_date" required>
            </div>
            <div class="mb-3">
                <label for="close_date" class="form-label">Close Date</label>
                <input type="date" class="form-control" id="close_date" name="close_date" required>
            </div>
            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <input type="text" class="form-control" id="priority" name="priority" required>
            </div>
            <div class="mb-3">
                <label for="org" class="form-label">Organization</label>
                <input type="text" class="form-control" id="org" name="org" required>
            </div>
            <div class="mb-3">
                <label for="project" class="form-label">Project</label>
                <input type="text" class="form-control" id="project" name="project" required>
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
            <button type="submit" class="btn btn-primary">Create Issue</button>
        </form>
        <a href="IssuesScreen.php" class="btn btn-secondary mt-3">Back to Issues List</a>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
