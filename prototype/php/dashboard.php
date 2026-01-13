<?php
require_once 'config.php';
requireLogin();

// Getting user role and username safely
$role_id = isset($_SESSION['role_id']) ? (int)$_SESSION['role_id'] : 0;
$username = htmlspecialchars($_SESSION['username']);  // Escaping username for safety

// Checking if a forbidden action message should be shown
$forbidden = false;
if (isset($_SESSION['forbidden_access']) && $_SESSION['forbidden_access'] === true) {
    $forbidden = true;
    unset($_SESSION['forbidden_access']); // Clearing it after showing once
}

// Defining dashboard links based on role
$dashboard_links = [];
if ($role_id === 1) { // Student
    $dashboard_links = [
        ['href' => 'student/view_courses.php', 'text' => 'View Courses', 'icon' => 'bi-journal-bookmark'],
        ['href' => 'student/view_assignments.php', 'text' => 'View Assignments', 'icon' => 'bi-journal-text'],
        ['href' => 'student/view_grades.php', 'text' => 'View Grades', 'icon' => 'bi-bar-chart-line'],
    ];
} elseif ($role_id === 2) { // Teacher
    $dashboard_links = [
        ['href' => 'teacher/create_manage_courses.php', 'text' => 'Create / Manage Courses', 'icon' => 'bi-journal-bookmark-fill'],
        ['href' => 'teacher/post_assignments.php', 'text' => 'Post Assignments', 'icon' => 'bi-journal-plus'],
        ['href' => 'teacher/view_submissions.php', 'text' => 'View Submissions', 'icon' => 'bi-inbox'],
        ['href' => 'teacher/grade_students.php', 'text' => 'Grade Students', 'icon' => 'bi-stars'],
    ];
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/dashboard.css">
</head>

<body>

    <!-- Navbar -->
    <?php include 'header.php'; ?>

    <!-- Dashboard Content -->
    <div class="dashboard-bg">
        <div class="dashboard-wrapper">
            <div class="dashboard-card">
                <h1 class="dash-title">
                    Welcome, <?php echo $username; ?>
                    <i class="bi bi-hand-wave-fill text-warning"></i>
                </h1>

                <!-- Role or Forbidden Notice -->
                <?php if ($forbidden): ?>
                    <p class="text-danger fw-bold fs-3 text-center">Forbidden Action</p>
                <?php elseif ($role_id === 1): ?>
                    <p class="role-text">Role: Student</p>
                <?php elseif ($role_id === 2): ?>
                    <p class="role-text">Role: Teacher</p>
                <?php else: ?>
                    <p class="text-danger">Unknown Role</p>
                <?php endif; ?>

                <!-- Dashboard Links -->
                <?php if (!$forbidden): ?>
                    <div class="row mt-4">
                        <?php foreach ($dashboard_links as $link): ?>
                            <div class="col-md-6 mb-3">
                                <a href="<?php echo $link['href']; ?>" class="dash-btn d-flex align-items-center justify-content-center">
                                    <i class="bi <?php echo $link['icon']; ?> icon-badge"></i>
                                    <span class="ms-2"><?php echo $link['text']; ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>