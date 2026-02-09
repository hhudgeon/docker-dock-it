<?php
// Start session and connect to DB
if (session_status() === PHP_SESSION_NONE) {
    session_name('myrecords');
    session_start();
}

/**
 * @var mysqli $db Database Connection
 */
require_once "database.php";
require_once "functions.php";

// Get the current page filename
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!doctype html>
<html lang="en">
<head>
    <title><?= $pageTitle ?? "Heather's Record Box!" ?></title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/bootstrap_theme.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="css/stylesheet.css">
</head>
<body>
<nav class="navbar navbar-expand-lg my-background" data-bs-theme="light">
    <div class="container-fluid">
        <a class="navbar-brand fancyfont" href="index.php">Heather's Record Box</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarNav">
            <!-- Left-aligned items -->
            <ul class="navbar-nav">
                <li class="nav-item">
                    <a class="btn btn-info me-2 <?= ($currentPage == 'index.php') ? 'active' : '' ?>" href="index.php" role="button">Home</a>
                </li>
                <li class="nav-item">
                    <a class="btn btn-info me-2 <?= ($currentPage == 'all-records.php') ? 'active' : '' ?>" href="all-records.php" role="button">All Records</a>
                </li>

                <?php if (isset($_SESSION['authUser'])): ?>
                    <li class="nav-item">
                        <a class="btn btn-primary me-2 <?= ($currentPage == 'add-music.php') ? 'active' : '' ?>" href="add-music.php" role="button">Add Music</a>
                    </li>
                    <li class="nav-item">
                        <a class="btn btn-warning me-2 <?= ($currentPage == 'manage-music.php') ? 'active' : '' ?>" href="manage-music.php" role="button">Manage Music</a>
                    </li>
                <?php endif; ?>
            </ul>

            <!-- Right-aligned item (Logout) -->
            <ul class="navbar-nav ms-auto">
                <?php if (isset($_SESSION['authUser'])): ?>
                    <li class="nav-item">
                        <a class="btn btn-primary" href="music-login.php?logout=true" role="button">
                            Logout (<?= htmlspecialchars($_SESSION['authUser']['username']) ?>)
                        </a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="btn btn-outline-success <?= ($currentPage == 'music-login.php') ? 'active' : '' ?>" href="music-login.php" role="button">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

    </div>
</nav>
