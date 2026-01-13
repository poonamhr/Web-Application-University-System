<?php
session_start();

// If user is already logged in, redirecting to dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit;
}

// Including database connection and helper functions
require 'config.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Checking for empty fields
    if (empty($email) || empty($password)) {
        $errors[] = "Both fields are required.";
    } else {
        // Fetching user by email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Verifying password using PHP's password_verify
        if ($user && password_verify($password, $user['password_hash'])) {
            // Storing session details
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role_id'] = $user['role_id'];

            // Redirecting to common dashboard
            header('Location: dashboard.php');
            exit;
        } else {
            $errors[] = "Invalid email or password.";
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>UEL Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../css//styles.css">
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
</head>

<body class="bg-light"
    style="background: url('../pictures/graduation_1.jpg'); background-size: cover; background-position: center 35%; background-repeat: no-repeat; ">

    <!-- Navbar -->
    <?php include 'header.php'; ?>

    <!-- Login Form -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">

                <div class="card border-0 rounded-4" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(6px); box-shadow:  0 4px 10px rgba(0,0,0,0.15), 0 8px 25px rgba(0,0,0,0.08), 0 0 20px rgba(0,120,255,0.12);">

                    <div class="card-header text-center bg-white border-0 rounded-top-4 py-3"
                        style="box-shadow: inset 0 -1px 0 rgba(0,0,0,0.05);">
                        <h4 class="text-dark mb-0 fw-bold">Login</h4>
                    </div>

                    <div class="card-body px-4 pb-4">

                        <?php if ($errors): ?>
                            <div class="alert alert-danger rounded-3 shadow-sm">
                                <?php foreach ($errors as $error) echo "<div>$error</div>"; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">

                            <div class="mb-3">
                                <label for="email" class="form-label fw-semibold">Email</label>
                                <input type="email" id="email" name="email" class="form-control rounded-3 shadow-sm"
                                    style="height: 45px; border: 1px solid #ddd;" required>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label fw-semibold">Password</label>
                                <input type="password" id="password" name="password"
                                    class="form-control rounded-3 shadow-sm"
                                    style="height: 45px; border: 1px solid #ddd;" required>
                            </div>

                            <!-- Login Button-->
                            <button type="submit" class="btn btn-primary w-100 rounded-3 fw-semibold shadow-sm" style="
                                    height: 45px;
                                    transition: 0.3s;
                                    box-shadow: 0 4px 12px rgba(0, 80, 200, 0.25);
                                " onmouseover="this.style.transform='scale(1.03)'"
                                onmouseout="this.style.transform='scale(1)'">
                                Login
                            </button>
                        </form>

                        <p class="mt-3 text-center">
                            Don't have an account?
                            <a href="register.php"><b style="color: blue;">Register</b></a>
                        </p>

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