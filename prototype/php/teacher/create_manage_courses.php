<?php
require_once '../config.php';
requireRole(2); // Only teachers

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Handle new course creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['course_name'])) {
    $course_name = trim($_POST['course_name']);

    // Checking course name length
    if (strlen($course_name) < 3) {
        $errors[] = "Course name must be at least 3 characters long.";
    } else {
        // Checking if the teacher already has a course with the same name
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM courses WHERE user_id = ? AND course_name = ?");
        $stmt->execute([$user_id, $course_name]);
        $count = $stmt->fetchColumn();

        if ($count > 0) {
            $errors[] = "You already have a course with this name.";
        } else {
            // Inserting new course
            $stmt = $pdo->prepare("INSERT INTO courses (course_name, user_id) VALUES (?, ?)");
            if ($stmt->execute([$course_name, $user_id])) {
                $success = "Course created successfully!";
            } else {
                $errors[] = "Failed to create course. Please try again.";
            }
        }
    }
}

// Fetching teacher's courses
$stmt = $pdo->prepare("SELECT * FROM courses WHERE user_id = ?");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Courses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/styles.css">
</head>

<body>

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-lg rounded-4 border-0 p-4 bg-light">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold d-flex justify-content-center align-items-center gap-2" style="color: #67573f;">
                            <i class="bi bi-journal-bookmark-fill fs-3"></i>
                            Manage Your Courses
                        </h2>
                        <p class="fs-6 text-dark">Create and view the courses you teach</p>
                    </div>

                    <!-- Display errors  -->
                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger text-center fw-semibold">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display success message -->
                    <?php if (!empty($success)): ?>
                        <div class="alert alert-success text-center fw-semibold">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Creating a new course -->
                    <div class="card shadow-sm mb-5 rounded-3 border-0">
                        <div class="card-header text-dark rounded-top-3 bg-light border-start border-4" style="background: linear-gradient(90deg, #C6B69E, #ADBBDA);">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-plus-circle me-2"></i>Create New Course
                            </h5>
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <div class="mb-4">
                                    <label for="course_name" class="form-label fw-medium">Course Name</label>
                                    <input type="text" name="course_name" id="course_name" class="form-control shadow-sm" placeholder="e.g. Web Development, Database Systems" required>
                                </div>
                                <button type="submit" class="btn btn-success px-4 shadow-sm fw-semibold">Create Course</button>
                            </form>
                        </div>
                    </div>

                    <!-- Listing teacher's existing courses -->
                    <div class="card shadow-sm rounded-3 border-0">
                        <div class="card-header rounded-top-3 bg-light border-start border-4" style="background: linear-gradient(90deg, #C6B69E, #ADBBDA);">
                            <h5 class="mb-0 fw-semibold">
                                <i class="bi bi-book me-2"></i>Your Courses
                            </h5>
                        </div>
                        <div class="card-body">
                            <!-- Message if no courses exist -->
                            <?php if (empty($courses)): ?>
                                <p class="text-muted text-center fs-6">No courses created yet!</p>
                            <?php else: ?>
                                <!-- Responsive Table -->
                                <div class="table-responsive">
                                    <table class="table table-hover align-middle shadow-sm">
                                        <thead class="table-secondary">
                                            <tr>
                                                <th>S/N</th>
                                                <th>Course Name</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php $sn = 1; ?>
                                            <?php foreach ($courses as $course): ?>
                                                <tr>
                                                    <td><?= $sn++; ?></td>
                                                    <td><?= htmlspecialchars($course['course_name']); ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>