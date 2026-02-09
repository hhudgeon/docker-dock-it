<?php
include "includes/header.php";
$pageTitle = "Welcome";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

$username = htmlspecialchars($_SESSION['authUser']['username']);
?>
<div class="container py-5">
    <h1 class="mb-4 text-center">Welcome, <?= $username ?>!</h1>
    <div class="row justify-content-center">
        <div class="col-md-5">
            <a href="add-music.php" class="text-decoration-none">
                <div class="card bg-success shadow mb-4 h-100">
                    <div class="card-header fw-bold">Add Music</div>
                    <div class="card-body">
                        <p class="card-text">Click here to add music to Heather's record box!</p>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-5">
            <a href="manage-music.php" class="text-decoration-none">
                <div class="card bg-danger shadow mb-4 h-100">
                    <div class="card-header fw-bold">Manage Music</div>
                    <div class="card-body">
                        <p class="card-text">Click here to edit some of Heather's records or delete them!</p>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>
