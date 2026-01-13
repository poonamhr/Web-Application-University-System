<?php
require_once '../config.php';
requireRole(1); // Only students

$student_id = $_SESSION['user_id'];

// Fetching grades for student
$stmt = $pdo->prepare("
    SELECT 
        c.course_name,
        a.title AS assignment_title,
        s.grade_value,
        s.graded_at
    FROM enrollments e
    INNER JOIN submissions s ON s.enrollment_id = e.enrollment_id
    INNER JOIN assignments a ON s.assignment_id = a.assignment_id
    INNER JOIN courses c ON a.course_id = c.course_id
    WHERE e.user_id = ?
      AND s.grade_value IS NOT NULL
    ORDER BY c.course_name, a.due_date
");
$stmt->execute([$student_id]);

$grades = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Grades</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css?v=1.2">
    <link rel="stylesheet" href="../../css/student_teacher.css?v=1.2">
</head>

<body style="background-image: url(../../pictures/grades.png); background-size: cover; background-position: center 35%; background-repeat: no-repeat; background-attachment: fixed;">

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="container_">

        <div class="card shadow-lg rounded-1 border-0 p-4 bg-light">
            <div class="text-center mb-5">
                <h2 class="fw-bold d-flex align-items-center justify-content-center gap-2">
                    <i class="bi bi-clipboard-data"></i>
                    Your Grades
                </h2>
                <p class="fs-6 text-dark">View your gardes for all of your courses</p>
            </div>

            <!-- Message if no grades exist -->
            <?php if (empty($grades)): ?>
                <p class="text-center text-muted fs-6">No grades available yet!</p>

            <?php else: ?>
                <!-- Grades Responsive Table -->
                <div class="table-responsive shadow-sm rounded-3 mb-4">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Course</th>
                                <th>Assignment</th>
                                <th>Grade</th>
                                <th>Graded At</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($grades as $grade): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($grade['course_name']); ?></td>
                                    <td><?= htmlspecialchars($grade['assignment_title']); ?></td>
                                    <td class="<?= $grade['grade_value'] >= 50 ? 'text-success' : 'text-danger'; ?>">
                                        <?= htmlspecialchars($grade['grade_value']); ?></td>
                                    <td><?php echo htmlspecialchars(date('d M Y, H:i', strtotime($grade['graded_at']))); ?></td>
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