<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'university_system';

try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Checking if user is logged in
function isLoggedIn(): bool
{
    return isset($_SESSION['user_id']);
}

// Getting role name by role_id
function getRoleName(int $role_id): string
{
    return $role_id === 1 ? 'Student' : 'Teacher';
}

// Redirecting to login if not logged in
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

// Require a specific role to access a page
function requireRole(int $requiredRole): void
{
    requireLogin(); // Must be logged in first
    if (!isset($_SESSION['role_id']) || (int)$_SESSION['role_id'] !== $requiredRole) {
        $_SESSION['forbidden_access'] = true;
        header("Location: ../dashboard.php");
        exit;
    }
}


// Part B 

// Getting all courses a student is enrolled
function getStudentCourses(int $user_id)
{
    global $pdo;

    $stmt = $pdo->prepare("
 SELECT c.*
        FROM courses c
        JOIN enrollments e ON e.course_id = c.course_id
        WHERE e.user_id = :user_id
    ");
    $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Getting all courses taught by a teacher
function getTeacherCourses(int $user_id)
{
    global $pdo;
    $stmt = $pdo->prepare("
       SELECT *
        FROM courses
        WHERE user_id = :user_id
    ");
     $stmt->execute(['user_id' => $user_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Getting all assignments for a course
function getAssignments(int $course_id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT * FROM assignments WHERE course_id = :course_id
    ");
    $stmt->execute(['course_id' => $course_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Getting all submissions for an assignment 
function getSubmissions(int $assignment_id)
{
    global $pdo;
    $stmt = $pdo->prepare("
        SELECT s.*, u.username
        FROM submissions s
        JOIN enrollments e ON s.enrollment_id = e.enrollment_id
        JOIN users u ON e.user_id = u.user_id
        WHERE s.assignment_id = :assignment_id
    ");
    $stmt->execute(['assignment_id' => $assignment_id]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Adding or Updating grade for a submission
function addOrUpdateGrade(int $submission_id, float $grade_value)
{
    global $pdo;

    $stmt = $pdo->prepare("
        UPDATE submissions
        SET grade_value = :grade_value,
            graded_at = CURRENT_TIMESTAMP
        WHERE submission_id = :submission_id
    ");

    return $stmt->execute([
        'grade_value' => $grade_value,
        'submission_id' => $submission_id
    ]);
}

// Adding a new assignment (teacher)
function addAssignment(int $course_id, string $title, string $description, string $due_date)
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO assignments (course_id, title, description, due_date)
        VALUES (:course_id, :title, :description, :due_date)
    ");

    return $stmt->execute([
        'course_id' => $course_id,
        'title' => $title,
        'description' => $description,
        'due_date' => $due_date
    ]);
}

// Submitting an assignment (student)
function submitAssignment(int $assignment_id, int $enrollment_id, string $file_path)
{
    global $pdo;

    $stmt = $pdo->prepare("
        INSERT INTO submissions (assignment_id, enrollment_id, file_path)
        VALUES (:assignment_id, :enrollment_id, :file_path)
    ");

    return $stmt->execute([
        'assignment_id' => $assignment_id,
        'enrollment_id' => $enrollment_id,
        'file_path' => $file_path
    ]);
}

?>

