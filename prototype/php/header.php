<?php
if (session_status() === PHP_SESSION_NONE) {
  session_start();
}

$base = '/prototype/php';
?>
<header>
  <nav class="navbar navbar-expand-sm navbar-dark shadow-sm py-3" style="background: var(--blue-dark);">
    <div class="container px-3 px-md-4"> <a class="brand-title" href="/prototype/index.html">University of East London</a>

      <!-- Toggler for mobile -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#uelNavbar"
        aria-controls="uelNavbar" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Collapsible menu -->
      <div class="collapse navbar-collapse" id="uelNavbar">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

          <li class="nav-item">
            <a class="nav-link fw-normal" href="/prototype/index.html">Home</a>
          </li>

          <!-- Links according to the page -->
          <?php if (isset($_SESSION['user_id'])): ?>
            <li class="nav-item">
              <a class="nav-link fw-normal" href="<?= $base ?>/dashboard.php">Dashboard</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn accent-btn text-white fw-normal ms-2" href="<?= $base ?>/logout.php">Logout</a>
            </li>
          <?php else: ?>
            <li class="nav-item">
              <a class="nav-link fw-normal" href="<?= $base ?>/login.php">Login</a>
            </li>
            <li class="nav-item">
              <a class="nav-link btn accent-btn text-white fw-normal ms-2" href="<?= $base ?>/register.php">Register</a>
            </li>
          <?php endif; ?>

        </ul>
      </div>

    </div>
  </nav>
</header>