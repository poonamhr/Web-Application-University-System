<?php
require_once '../config.php';
requireRole(2); // Only teachers

$user_id = $_SESSION['user_id'];

// Fetching submissions for teacher's courses
$stmt = $pdo->prepare("
    SELECT 
        s.submission_id,
        s.file_path,
        s.submitted_at,
        u.username AS student_name,
        a.title AS assignment_title,
        c.course_name
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Student Submissions</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css?v=1.2">
    <link rel="stylesheet" href="../../css/student_teacher.css?v=1.2">
</head>

<body>

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="container_">

        <div class="card shadow-lg rounded-1 border-0 p-4 bg-light">
            <div class="text-center mb-5">
                <h2 class="fw-bold d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Student Submissions
                </h2>
                <p class="fs-6 text-dark">View all submissions for your courses</p>
            </div>

            <!-- No submissions message -->
            <?php if (empty($submissions)): ?>
                <p class="text-center text-muted fs-6">No submissions found!</p>

            <?php else: ?>
                <!-- View Submissions Responsive Table  -->
                <div class="table-responsive shadow-sm rounded-3 mb-4">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th><i class="bi bi-book me-1"></i> Course</th>
                                <th><i class="bi bi-journal-bookmark-fill me-1"></i>Assignment</th>
                                <th><i class="bi bi-person-fill me-1"></i>Student</th>
                                <th><i class="bi bi-clock-fill me-1"></i>Submitted At</th>
                                <th><i class="bi bi-file-earmark-text me-1"></i>File</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($submissions as $submission): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($submission['course_name']); ?></td>
                                    <td><?= htmlspecialchars($submission['assignment_title']); ?></td>
                                    <td><?= htmlspecialchars($submission['student_name']); ?></td>
                                    <td><?= htmlspecialchars(date('d M Y, H:i', strtotime($submission['submitted_at']))); ?></td>
                                    <td>
                                        <?php if (!empty($submission['file_path'])): ?>
                                            <?php
                                            // Encoding filename for safe URL
                                            $filename = basename($submission['file_path']);
                                            $encoded  = rawurlencode($filename);
                                            ?>
                                            <a href="/prototype/php/uploads/<?= $encoded ?>"
                                                target="_blank"
                                                class="btn btn-sm btn-primary shadow-sm">
                                                <i class="bi bi-file-earmark-text me-1"></i> View File
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No File</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>