<?php
require_once '../config.php';
requireRole(1); // Only students

$user_id = $_SESSION['user_id'];

// Fetching assignments for courses the student is enrolled in
$stmt = $pdo->prepare("
    SELECT 
        a.assignment_id,
        a.title,
        a.description,
        a.due_date,
        c.course_name,
        s.submitted_at
    FROM enrollments e
    JOIN courses c ON e.course_id = c.course_id
    JOIN assignments a ON a.course_id = c.course_id
    LEFT JOIN submissions s 
        ON s.assignment_id = a.assignment_id
        AND s.enrollment_id = e.enrollment_id
    WHERE e.user_id = ?
    ORDER BY a.due_date ASC
");
$stmt->execute([$user_id]);

// Storing all assignment records
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Assignments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
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
                    <i class="bi bi-pencil-square"></i>
                    Your Assignments
                </h2>
                <p class="fs-6 text-dark">View your assignments for all of your courses</p>
            </div>

            <!-- Message shown if no assignments exist -->
            <?php if (empty($assignments)): ?>
                <p class="text-center text-muted fs-6">No assignments found.</p>

            <?php else: ?>
                <!-- Responsive table -->
                <div class="table-responsive shadow-sm rounded-3 mb-4">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Course</th>
                                <th>Title</th>
                                <th>Due Date</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($assignments as $assignment): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($assignment['course_name']); ?></td>
                                    <td><?= htmlspecialchars($assignment['title']); ?></td>
                                    <td><?= htmlspecialchars($assignment['due_date']); ?></td>
                                    <td>
                                        <?php if ($assignment['submitted_at']): ?>
                                            <span class="badge bg-success">Submitted</span><br>
                                            <small class="text-success fw-semibold">
                                                <?= htmlspecialchars(date('d M Y, H:i', strtotime($assignment['submitted_at']))); ?>
                                            </small>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Not Submitted</span>
                                        <?php endif; ?>
                                    </td>

                                    <td>
                                        <!-- Submit Button -->
                                        <a
                                            href="submit_assignments.php?assignment_id=<?= $assignment['assignment_id']; ?>"
                                            class="btn <?= $assignment['submitted_at'] ? 'btn-success' : 'btn-primary'; ?> btn-sm">
                                            <?= $assignment['submitted_at'] ? 'Resubmit' : 'Submit'; ?>
                                        </a>
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