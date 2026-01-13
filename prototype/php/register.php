<?php
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Reading form data
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $role_id = $_POST['role_id'];
    $reg_code = trim($_POST['reg_code']);
    $registration_code = bin2hex(random_bytes(5));

    // Checking for empty fields
    if (empty($username) || empty($email) || empty($password) || empty($role_id) || empty($reg_code)) {
        $errors[] = "All fields are required.";
    } else {
        // Checking if email already exists
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->rowCount() > 0) {
            $errors[] = "Email already registered.";
        }
    }

    // Validating registration code
    if ($role_id == 1 && $reg_code !== 'STUD2025') {
        $errors[] = "Invalid registration code for Student.";
    }
    if ($role_id == 2 && $reg_code !== 'PROF2025') {
        $errors[] = "Invalid registration code for Professor.";
    }

    // Inserting into database if no errors
    if (empty($errors)) {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("
            INSERT INTO users (username, email, password_hash, role_id, registration_code) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $email, $password_hash, $role_id, $registration_code]);

        header('Location: login.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UEL Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css/styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-light"
    style="background: url(../pictures/high-school-graduation.jpg) no-repeat center center fixed; background-size: cover;">

    <!-- Navbar -->
    <?php include 'header.php'; ?>

    <!-- Register Form -->
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card border-0 rounded-4" style="
                    background: rgba(255,255,255,0.95);
                    backdrop-filter: blur(5px);
                    box-shadow:
                        0 3px 8px rgba(0,0,0,0.15),
                        0 6px 20px rgba(0,0,0,0.08),
                        0 0 15px rgba(0,120,255,0.12);">

                    <div class="card-header text-center bg-white border-0 rounded-top-4 py-2"
                        style="box-shadow: inset 0 -1px 0 rgba(0,0,0,0.05);">
                        <h4 class="text-dark mb-0 fw-bold">Register</h4>
                    </div>

                    <div class="card-body px-4 pb-3 pt-2">

                        <?php if ($errors): ?>
                            <div class="alert alert-danger rounded-3 shadow-sm py-2">
                                <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-2">
                                <label for="username" class="form-label fw-semibold">Username</label>
                                <input type="text" id="username" name="username"
                                    class="form-control rounded-3 shadow-sm"
                                    style="height: 42px; border: 1px solid #ddd;" required>
                            </div>

                            <div class="mb-2">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" class="form-control rounded-3 shadow-sm"
                                    style="height: 42px; border: 1px solid #ddd;" required>
                            </div>

                            <div class="mb-2">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control rounded-3 shadow-sm"
                                    style="height: 42px; border: 1px solid #ddd;" required>
                            </div>

                            <div class="mb-2">
                                <label for="role_id" class="form-label fw-semibold">Role</label>
                                <select id="role_id" name="role_id" class="form-control rounded-3 shadow-sm"
                                    style="height: 42px; border: 1px solid #ddd;" required>
                                    <option value="">Select Role</option>
                                    <?php
                                    $roles = $pdo->query("SELECT * FROM roles")->fetchAll();
                                    foreach ($roles as $role) {
                                        echo "<option value='{$role['role_id']}'>" . ucfirst($role['role_name']) . "</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="reg_code" class="form-label fw-semibold">Registration Code</label>
                                <input type="text" id="reg_code" name="reg_code"
                                    class="form-control rounded-3 shadow-sm"
                                    style="height: 42px; border: 1px solid #ddd;" required>
                            </div>

                            <!-- Register Button --->
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold shadow-sm" style="
                                height: 43px;
                                font-size: 1rem;
                                transition: 0.25s;
                                box-shadow: 0 3px 10px rgba(0,80,200,0.25);
                            " onmouseover="this.style.transform='scale(1.025)'"
                                onmouseout="this.style.transform='scale(1)'">
                                Register
                            </button>

                            <p class="mt-2 text-center">
                                Already have an account?
                                <a href="login.php"><b style="color: blue;">Login here</b></a>
                            </p>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>