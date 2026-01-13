<?php
require_once '../config.php';
requireRole(2); // Only teachers

$user_id = $_SESSION['user_id'];
$success = "";
$errors = [];

// Fetching submissions for teacher
$stmt = $pdo->prepare("
    SELECT 
        s.submission_id,
        u.username AS student_name,
        a.title AS assignment_title,
        c.course_name,
        s.grade_value
    FROM submissions s
    INNER JOIN enrollments e ON s.enrollment_id = e.enrollment_id
    INNER JOIN users u ON e.user_id = u.user_id
    INNER JOIN assignments a ON s.assignment_id = a.assignment_id
    INNER JOIN courses c ON a.course_id = c.course_id
    WHERE c.user_id = ?
    ORDER BY s.submitted_at ASC
");

$stmt->execute([$user_id]);
$submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle grading
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['grades'])) {
    foreach ($_POST['grades'] as $submission_id => $grade_value) {

        // Allow empty input (skip grading)
        if ($grade_value === '') {
            continue;
        }

        $grade_value = floatval($grade_value);

        // Updating grade directly in submissions table
        $update = $pdo->prepare("
            UPDATE submissions
            SET grade_value = ?, graded_at = NOW()
            WHERE submission_id = ?
        ");
        $update->execute([$grade_value, $submission_id]);
    }

    $success = "Grades updated successfully!";
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Grade Students</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css?v=1.2">
    <link rel="stylesheet" href="../../css/student_teacher.css?v=1.2">
</head>

<body style="
    background-image: url(../../pictures/teacher_2.jpg);
    background-size: 30%;
    background-position: right bottom;
    background-repeat: no-repeat;
    background-attachment: fixed;
">

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="container_">

        <div class="card shadow-lg rounded-1 border-0 p-4 bg-light">
            <div class="text-center mb-5">
                <h2 class="fw-bold d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-pencil-fill fs-3"></i>
                    Grade Students
                </h2>
                <p class="fs-6 text-dark">Enter grades for your students' submissions</p>
            </div>

            <!-- Message if no submissions exist -->
            <?php if (empty($submissions)): ?>
                <p class="text-center text-muted fs-6">No grades to show!</p>

            <?php else: ?>
                <!-- Success message -->
                <?php if ($success): ?>
                    <div class="alert alert-success text-center fw-semibold">
                        <?= htmlspecialchars($success) ?>
                    </div>
                <?php endif; ?>

                <!-- Grading form -->
                <form id="gradeForm" method="POST"></form>
                <div class="table-responsive shadow-sm rounded-3 mb-4">

                    <!-- Submissions Responsive Table -->
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th><i class="bi bi-book me-1"></i> Course</th>
                                <th><i class="bi bi-journal-bookmark-fill me-1"></i> Assignment</th>
                                <th><i class="bi bi-person-fill me-1"></i> Student</th>
                                <th><i class="bi bi-graph-up-arrow me-1"></i> Grade</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($submissions as $sub): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($sub['course_name']); ?></td>
                                    <td><?= htmlspecialchars($sub['assignment_title']); ?></td>
                                    <td><?= htmlspecialchars($sub['student_name']); ?></td>
                                    <td>
                                        <input
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            max="100"
                                            name="grades[<?= $sub['submission_id']; ?>]"
                                            value="<?= $sub['grade_value'] ?? ''; ?>"
                                            class="form-control form-control-sm shadow-sm"
                                            placeholder="0-100"
                                            style="width: 70px; text-align: center; display: inline-block;"
                                            form="gradeForm">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Submit button -->
                <div class="d-flex justify-content-end">
                    <button type="submit" class="btn btn-success fw-semibold shadow-sm px-4" form="gradeForm">
                        <i class="bi bi-save me-1"></i> Save Grades
                    </button>
                </div>

            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>