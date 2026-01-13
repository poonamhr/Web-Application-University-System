<?php
require_once '../config.php';
requireLogin();
requireRole(1); // Only students

$user_id = $_SESSION['user_id'];

// Handling enrollment/unenrollment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['course_id'], $_POST['action'])) {
    $course_id = (int)$_POST['course_id'];
    $action = $_POST['action'];

    if ($action === 'enroll') {
        // Check already enrolled
        $stmtCheck = $pdo->prepare("SELECT * FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmtCheck->execute([$user_id, $course_id]);
        // Inserting enrollment record
        if ($stmtCheck->rowCount() === 0) {
            $stmtEnroll = $pdo->prepare("INSERT INTO enrollments (user_id, course_id) VALUES (?, ?)");
            $stmtEnroll->execute([$user_id, $course_id]);
        }
        // Deleting enrollment record
    } elseif ($action === 'unenroll') {
        $stmtDelete = $pdo->prepare("DELETE FROM enrollments WHERE user_id = ? AND course_id = ?");
        $stmtDelete->execute([$user_id, $course_id]);
    }

    // Refresh page to reflect changes
    header("Location: view_courses.php");
    exit;
}

// Fetching all courses with teacher names
$stmt = $pdo->query("
    SELECT c.course_id, c.course_name, u.username AS teacher_name
    FROM courses c
    JOIN users u ON c.user_id = u.user_id
");
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetching already enrolled courses
$stmtEnrolled = $pdo->prepare("SELECT course_id FROM enrollments WHERE user_id = ?");
$stmtEnrolled->execute([$user_id]);
$enrolledCourses = $stmtEnrolled->fetchAll(PDO::FETCH_COLUMN);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>My Courses</title>
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
                    <i class="bi bi-mortarboard-fill me-2"></i>
                    Your Courses
                </h2>
                <p class="fs-6 text-dark">You can enroll or unenroll from courses</p>
            </div>

            <!-- Message if no courses exist -->
            <?php if (empty($courses)): ?>
                <p class="text-center text-muted fs-6">No courses found!</p>

            <?php else: ?>
                <!-- Courses Responsive Table -->
                <div class="table-responsive shadow-sm rounded-3 mb-4">
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Course Name</th>
                                <th>Teacher</th>
                                <th>Enroll</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php $i = 1; ?>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <td><?= $i++; ?></td>
                                    <td><?= htmlspecialchars($course['course_name']); ?></td>
                                    <td><?= htmlspecialchars($course['teacher_name']); ?></td>

                                    <!-- Enrollment actions -->
                                    <td class="text-center">
                                        <div class="d-inline-flex align-items-center justify-content-center">
                                            <?php if (in_array($course['course_id'], $enrolledCourses)): ?>
                                                <!-- Enrolled -->
                                                <span class="badge bg-success">Enrolled</span>

                                                <!-- Unenroll form  -->
                                                <form method="POST" class="m-0 p-0">
                                                    <input type="hidden" name="course_id" value="<?php echo $course['course_id']; ?>">
                                                    <input type="hidden" name="action" value="unenroll">
                                                    <button type="submit" class="btn btn-danger btn-sm p-0 m-0" style="width: 15px; height: 1px;" title="Unenroll"></button>
                                                </form>

                                            <?php else: ?>
                                                <!-- Enroll form -->
                                                <form method="POST" class="m-0 p-0">
                                                    <input type="hidden" name="course_id" value="<?= $course['course_id']; ?>">
                                                    <input type="hidden" name="action" value="enroll">
                                                    <button type="submit" class="btn btn-primary w-100 fw-semibold shadow-sm">
                                                        Enroll
                                                    </button>
                                                </form>
                                            <?php endif; ?>
                                        </div>
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