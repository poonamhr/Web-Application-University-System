<?php
require_once '../config.php';
requireRole(1); // Only students

$user_id = $_SESSION['user_id'];
$assignment_id = $_GET['assignment_id'] ?? null;
$errors = [];
$success = "";
$assignment = null;
$enrollment_id = null;

// Validating assignment ID
if (!$assignment_id) {
    $errors[] = "Invalid assignment ID.";
} else {
    // Checking if assignment exists & student is enrolled
    $stmt = $pdo->prepare("
        SELECT 
            a.assignment_id,
            a.title,
            a.description,
            a.due_date,
            e.enrollment_id
        FROM assignments a
        JOIN enrollments e ON e.course_id = a.course_id
        WHERE a.assignment_id = ?
          AND e.user_id = ?
    ");
    $stmt->execute([$assignment_id, $user_id]);
    $assignment = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($assignment) {
        $enrollment_id = $assignment['enrollment_id'];
    } else {
        $errors[] = "You are not enrolled in this course.";
    }
}

// Handling file submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['assignment_file']) && $enrollment_id) {
    $file = $_FILES['assignment_file'];

    $upload_dir = __DIR__ . '/../uploads/';

    // Creating upload directory if it does not exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Extracting file name and extension
    $filename = basename($file['name']);
    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    $allowed = ['pdf', 'doc', 'docx', 'zip'];

    if (!in_array($ext, $allowed)) {
        $errors[] = "Invalid file type. Allowed: PDF, DOC, DOCX, ZIP.";
    } else {
        $timestamped_filename = time() . "_" . $filename;
        $target_path = $upload_dir . $timestamped_filename;
        $stored_path = 'php/uploads/' . $timestamped_filename;

        // Moving uploaded file from temporary location to target directory
        if (move_uploaded_file($file['tmp_name'], $target_path)) {

        // Checking if student has already submitted this assignment
            $stmtCheck = $pdo->prepare("SELECT * FROM submissions WHERE assignment_id = ? AND enrollment_id = ?");
            $stmtCheck->execute([$assignment_id, $enrollment_id]);

             // If submission exists, updating it (resubmission)
            if ($stmtCheck->rowCount() > 0) {
                $stmtUpdate = $pdo->prepare("
                    UPDATE submissions 
                    SET file_path = ?, submitted_at = NOW()
                    WHERE assignment_id = ? AND enrollment_id = ?
                ");
                $stmtUpdate->execute([$stored_path, $assignment_id, $enrollment_id]);
                $success = "Assignment resubmitted successfully!";
            } else { // If no submission exists, inserting new submission
                $stmtInsert = $pdo->prepare("
                    INSERT INTO submissions (assignment_id, enrollment_id, file_path, submitted_at)
                    VALUES (?, ?, ?, NOW())
                ");
                $stmtInsert->execute([
                    $assignment_id,
                    $enrollment_id,
                    $stored_path
                ]);
                $success = "Assignment submitted successfully!";
            }
        } else {
            $errors[] = "Failed to upload the file.";
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css?v=1.2">
    <link rel="stylesheet" href="../../css/student_teacher.css?v=1.2">
</head>

<body>

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <div class="container_ position-relative">
        <img src='../../pictures/submit_assignments.png'
            class='position-absolute bottom-0 end-0 img-fluid d-none d-md-block'
            style='width:460px; height:260px; object-fit:cover; border-radius:8px; z-index:0;'
            alt='Submit Assignment'>

        <!-- Displaying errors -->
        <?php if ($errors): ?>
            <?php foreach ($errors as $e): ?>
                <div class="alert alert-danger text-center fw-semibold"><?php echo $e; ?></div>
            <?php endforeach; ?>
        <?php endif; ?>


        <!-- Displaying success message -->
        <?php if ($success): ?>
            <div class="alert alert-success text-center fw-semibold">
                <?= htmlspecialchars($success); ?>
            </div>
        <?php endif; ?>

        <h2 class="text-center mb-4"><i class="bi bi-upload"></i> Submit Assignment</h2>

        <!-- Assignment submission content -->
        <div class="row">
            <div class="col-md-7 col-lg-6">
                <form method="POST" enctype="multipart/form-data" class="mt-4 assignment-info">
                    <div class="mb-4">
                        <?php if ($assignment): ?>
                            <div class="assignment-info mb-4">
                                <div class="text-dark">
                                    <p style="text-align: left;"><strong>Title:</strong> <?= htmlspecialchars($assignment['title']); ?></p>
                                    <p style="text-align: left;"><strong>Due Date:</strong> <?= htmlspecialchars($assignment['due_date']); ?></p>
                                    <p style="text-align: left;"><strong>Description: </strong><?php echo htmlspecialchars($assignment['description']); ?></p>
                                </div>
                            </div>

                            <label for="assignment_file" class="form-label fw-semibold text-dark">
                                Upload your assignment file
                            </label>
                            <input
                                type="file"
                                name="assignment_file"
                                id="assignment_file"
                                class="form-control"
                                required>
                            <small class="text-dark d-block mt-2">
                                Allowed formats: PDF, DOC, DOCX, ZIP
                            </small>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit"
                        class="btn <?= $success ? 'badge bg-success' : 'btn-primary'; ?>">
                        <?= $success ? 'Submitted' : 'Submit Assignment'; ?>
                    </button>
                </form>
            </div>
        </div>
    <?php endif; ?>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html> 