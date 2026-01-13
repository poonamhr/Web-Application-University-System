<?php
require_once '../config.php';
requireRole(2); // Only teachers

$user_id = $_SESSION['user_id'];
$errors = [];
$success = "";

// Fetching teacher's courses
$stmt = $pdo->prepare("SELECT * FROM courses WHERE user_id = ?");
$stmt->execute([$user_id]);
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Validation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Form input
    $course_id = $_POST['course_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $due_date = $_POST['due_date'] ?? null;

    // Required fields
    if (!$course_id || !$title || !$due_date) {
        $errors[] = "All fields are required.";
    }  // validating date is not in the past
    elseif (strtotime($due_date) < strtotime(date('Y-m-d'))) {
        $errors[] = "Submission deadline cannot be in the past.";
    } else {
        // Inserting assignment into database
        $stmt = $pdo->prepare("  INSERT INTO assignments (course_id, title, description, due_date)
    VALUES (?, ?, ?, ?)");
        $stmt->execute([$course_id, $title, $description, $due_date]);
        $success = "Assignment posted successfully!";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Assignment</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" href="../../css/styles.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
</head>

<body style="
    background-image: url(../../pictures/teacher_3.jpg);
    background-size: 20%;
    background-position: right bottom;
    background-repeat: no-repeat;
    background-attachment: fixed;
">

    <!-- Navbar -->
    <?php include '../header.php'; ?>

    <!-- Main Content -->
    <div class="container my-2">
        <div class="row justify-content-center">
            <div class="col-lg-9">
                <div class="card shadow-lg rounded-4 border-0 p-4">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold d-flex align-items-center justify-content-center gap-2" style="color: #67573f;">
                            <i class="bi bi-upload fs-4"></i>
                            Post Assignment
                        </h2>
                        <p class="fs-6 text-dark">
                            Create and publish academic assignments for enrolled courses
                        </p>
                    </div>

                    <!-- Decorative line -->
                    <div style="height: 6px; width: 100%; margin: -15px 0 20px 0; border-radius: 3px; background: linear-gradient(90deg, rgba(198,182,158,0) 0%, #C6B69E 25%, #ADBBDA 75%, rgba(173,187,218,0) 100%); box-shadow: 0 2px 6px rgba(0,0,0,0.1);"></div>

                    <!-- Display errors -->
                    <?php if ($errors): ?>
                        <div class="alert alert-danger text-center fw-semibold">
                            <?php foreach ($errors as $error): ?>
                                <div><?= htmlspecialchars($error) ?></div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                    <!-- Display success -->
                    <?php if ($success): ?>
                        <div class="alert alert-success text-center fw-semibold">
                            <?= htmlspecialchars($success) ?>
                        </div>
                    <?php endif; ?>

                    <!-- Post Assignment Form -->
                    <form method="POST">
                        <div class="mb-4">

                            <!-- Course Selection -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">Courses</label>
                                <div class="custom-select-wrapper" style="position: relative;">

                                    <!-- Selected option -->
                                    <div class="custom-select-selected" tabindex="0" style="padding: 0.5rem; border: 1px solid #ced4da; border-radius: 0.375rem; display: flex; align-items: center; gap: 0.5rem; background: linear-gradient(90deg, #f9f7f3 0%, #eef1f8 100%); outline: none;">
                                        <i class="bi bi-mortarboard-fill"></i>
                                        <span>— Choose a course —</span>
                                    </div>
                                    <!-- Dropdown options -->
                                    <ul class="custom-select-options"
                                        style="list-style: none;margin: 0;padding: 0;border: 1px solid #ced4da;border-radius: 0.375rem;position: absolute;top: 100%;left: 0; width: 100%;max-height: 200px;overflow-y: auto;display: none;z-index: 9999;background-color: #fff;box-shadow: 0 4px 10px rgba(0,0,0,0.1);">

                                        <?php foreach ($courses as $course): ?>
                                            <li style="padding: 0.5rem; transition: background 0.3s;"
                                                onmouseover="this.style.background='linear-gradient(90deg, #C6B69E, #ADBBDA)'"
                                                onmouseout="this.style.background='transparent'"
                                                onclick="selectOption(this, <?= $course['course_id'] ?>)">
                                                <?= htmlspecialchars($course['course_name']); ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                    <input type="hidden" name="course_id">
                                </div>
                            </div>

                            <!-- Assignment Title -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    Assignment Title
                                </label>
                                <div class="input-group shadow-sm rounded-3">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-pencil-square"></i>
                                    </span>
                                    <input type="text"
                                        name="title"
                                        class="form-control border-start-0"
                                        placeholder="e.g. Calculus Basics, Human Anatomy"
                                        required>
                                </div>
                            </div>

                            <!-- Assignment Description -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    Assignment Description
                                </label>
                                <div class="input-group shadow-sm rounded-3">
                                    <span class="input-group-text bg-white align-items-start pt-2">
                                        <i class="bi bi-text-left"></i>
                                    </span>
                                    <textarea name="description"
                                        class="form-control border-start-0"
                                        rows="4"
                                        placeholder="Provide description"></textarea>
                                </div>
                            </div>

                            <!-- Submission Deadline -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold text-dark">
                                    Submission Deadline
                                </label>
                                <div class="input-group shadow-sm rounded-3">
                                    <span class="input-group-text bg-white">
                                        <i class="bi bi-calendar-event-fill"></i>
                                    </span>
                                    <input type="text"
                                        id="due_date"
                                        name="due_date"
                                        class="form-control border-start-0"
                                        placeholder="Select date"
                                        required>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-flex justify-content-end">
                                <button type="submit" class="btn btn-success px-4 shadow-sm fw-semibold">
                                    <i class="bi bi-save me-1"></i> Post Assignments
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>
    </div>

    <!-- Footer -->
    <?php include '../footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="../../js/main.js"></script>
</body>

</html>