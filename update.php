<?php
session_start();
if (!isset($_SESSION['user_id'])){
    session_destroy();
    header("Location: index.php");
}
require_once '../database/database.php';  // Database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // only admin or the user associated with the issue are able to update the issue
    if(!($_SESSION['admin'] == "yes" || $_SESSION['user_id'] == $_POST['per_id'])){
        header('Location: issuesList.php');
        exit();
    }

    // Fetch current issue record before processing file logic
$issue_id = $_POST['issue_id'];

$stmt = $conn->prepare("SELECT * FROM iss_issues WHERE id = :id");
$stmt->bindParam(':id', $issue_id, PDO::PARAM_INT);
$stmt->execute();
$issue = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$issue) {
    die("Issue not found.");
}

$newFileNameToSave = $issue['pdf_attachment']; // Default to existing


// Handle file upload (if new one is added)
if (isset($_FILES['pdf_attachment']) && $_FILES['pdf_attachment']['error'] === UPLOAD_ERR_OK) {
    $fileTmpPath = $_FILES['pdf_attachment']['tmp_name'];
    $fileName = $_FILES['pdf_attachment']['name'];
    $fileSize = $_FILES['pdf_attachment']['size'];
    $fileType = $_FILES['pdf_attachment']['type'];
    $fileNameCmps = explode(".", $fileName);
    $fileExtension = strtolower(end($fileNameCmps));

    if ($fileExtension !== 'pdf') {
        die("Only PDF files are allowed!");
    }
    if ($fileSize > 2 * 1024 * 1024) {
        die("File size exceeds 2MB limit!");
    }

    $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
    $uploadFileDir = './uploads/';
    $dstPath = $uploadFileDir . $newFileName;

    if (!is_dir($uploadFileDir)) {
        mkdir($uploadFileDir, 0755, true);
    }

    if (move_uploaded_file($fileTmpPath, $dstPath)) {
        // Delete the old file if it exists
        if (!empty($issue['pdf_attachment']) && file_exists('./uploads/' . $issue['pdf_attachment'])) {
            unlink('./uploads/' . $issue['pdf_attachment']);
        }
        $newFileNameToSave = $newFileName;
    } else {
        die("Error uploading the new file.");
    }
}

// Handle file removal (checkbox)
if (isset($_POST['remove_pdf']) && $_POST['remove_pdf'] == "1") {
    if (!empty($issue['pdf_attachment']) && file_exists('./uploads/' . $issue['pdf_attachment'])) {
        unlink('./uploads/' . $issue['pdf_attachment']);
    }
    $newFileNameToSave = NULL;
}


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
    $stmt = $conn->prepare("UPDATE iss_issues SET short_description = :short_description, long_description = :long_description, open_date = :open_date, close_date = :close_date, priority = :priority, org = :org, project = :project, per_id = :per_id, pdf_attachment = :pdf_attachment WHERE id = :id");
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
    $stmt->bindParam(':pdf_attachment', $newFileNameToSave, PDO::PARAM_STR);

    // Execute the statement
    if ($stmt->execute()) {
        // Redirect to the issues list page after the update
        header('Location: issuesList.php');
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
    $existingPdf = $issue['pdf_attachment'];
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

        <form action="update.php" method="POST" enctype="multipart/form-data">
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
                <input type="date" class="form-control" id="close_date" name="close_date" value="<?php echo htmlspecialchars($issue['close_date']); ?>">
            </div>

            <div class="mb-3">
                <label for="priority" class="form-label">Priority</label>
                <select class="form-control" id="priority" name="priority" required>
                    <option value="" disabled selected></option>
                    <?php 
                        $options = array("low", "med", "high");
                        foreach ($options as $option) {
                            $select = $issue['priority'] == $option ? 'selected' : '';
                            echo "<option value='{$option}' {$select}>{$option}</option>";
                        }
                    ?>
            </select>
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
            <div class="mb-3">
                <label for="pdf_attachment" class="form-label">PDF Attachment</label>
                <?php if (!empty($existingPdf)) : ?>
                    <p>Existing PDF: <a href="uploads/<?php echo htmlspecialchars($existingPdf); ?>" target="_blank"><?php echo htmlspecialchars($existingPdf); ?></a></p>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remove_pdf" id="remove_pdf" value="1">
                        <label class="form-check-label" for="remove_pdf">Remove existing PDF</label>
                    </div>
                <?php endif; ?>
                <input type="file" class="form-control mt-2" id="pdf_attachment" name="pdf_attachment" accept="application/pdf">
            </div>
            <button type="submit" class="btn btn-primary">Update Issue</button>
        </form>

        <a href="issuesList.php" class="btn btn-secondary mt-3">Back to Issues List</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
