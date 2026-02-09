<?php
include "includes/header.php";
$pageTitle = "Login";

// Determine which form to show (default to login)
$view = $_GET['view'] ?? 'login';
?>
<div class="container login-container py-5 login-image">
    <div class="row">
        <?php if ($view === 'signup'): ?>
            <div class="col-12 mb-5">
                <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
                    <h3 class="mb-4">Sign Up</h3>
                    <?php
                    $accountCreated = false;

                    if (isset($_POST['signup'])) {
                        $username = trim(strip_tags($_POST['username']));
                        $email = trim(strip_tags($_POST['email']));
                        $password = trim($_POST['password']);

                        if (strlen($username) < 3 || strlen($username) > 20) {
                            echo "<div class='alert alert-warning'>Username must be between 3–20 characters.</div>";
                        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                            echo "<div class='alert alert-warning'>Invalid email format.</div>";
                        } else {
                            // Check for duplicate username or email
                            $checkQuery = "SELECT userId FROM myrecords__users WHERE username = ? OR email = ?";
                            $stmt = mysqli_prepare($db, $checkQuery);
                            mysqli_stmt_bind_param($stmt, "ss", $username, $email);
                            mysqli_stmt_execute($stmt);
                            mysqli_stmt_store_result($stmt);

                            if (mysqli_stmt_num_rows($stmt) > 0) {
                                echo '<div class="alert alert-danger">Username or email already exists.</div>';
                            } else {
                                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                                $query = "INSERT INTO myrecords__users (username, email, password, role) VALUES (?, ?, ?, 'user')";
                                $stmt = mysqli_prepare($db, $query);
                                mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hashedPassword);
                                if (mysqli_stmt_execute($stmt)) {
                                    $accountCreated = true;
                                    echo '<div class="alert alert-success"><b>Account created!</b><br>Please login.</div>';
                                } else {
                                    echo '<div class="alert alert-danger">Error creating account. Please try again.</div>';
                                }
                            }
                        }
                    }
                    ?>

                    <?php if (!$accountCreated): ?>
                        <form method="post" action="?view=signup">
                            <div class="form-group mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Your Username *" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="email" name="email" class="form-control" placeholder="Your Email *" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Your Password *" required>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="signup" class="btn btn-primary w-100" value="Sign Up">
                            </div>
                        </form>
                    <?php endif; ?>
                    <p class="mt-3 text-center">Already have an account? <a href="?view=login">Login here</a></p>
                </div>
            </div>
        <?php else: ?>
            <div class="col-12">
                <div class="card shadow p-4 mx-auto" style="max-width: 500px;">
                    <h3 class="mb-4">Login</h3>
                    <?php
                    if (isset($_POST['login'])) {
                        $username = trim(strip_tags($_POST['username']));
                        $password = trim($_POST['password']);

                        $query = "SELECT userId, username, password, role FROM myrecords__users WHERE username = ?";
                        $stmt = mysqli_prepare($db, $query);
                        mysqli_stmt_bind_param($stmt, "s", $username);
                        mysqli_stmt_execute($stmt);
                        mysqli_stmt_store_result($stmt);
                        mysqli_stmt_bind_result($stmt, $userId, $dbUsername, $hashedPassword, $role);

                        if (mysqli_stmt_fetch($stmt)) {
                            if (password_verify($password, $hashedPassword)) {
                                if (password_needs_rehash($hashedPassword, PASSWORD_DEFAULT)) {
                                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                                    $updateQuery = "UPDATE myrecords__users SET password = ? WHERE userId = ?";
                                    $updateStmt = mysqli_prepare($db, $updateQuery);
                                    mysqli_stmt_bind_param($updateStmt, "si", $newHash, $userId);
                                    mysqli_stmt_execute($updateStmt);
                                }

                                session_regenerate_id();
                                $_SESSION['authUser']['userId'] = $userId;
                                $_SESSION['authUser']['username'] = $dbUsername;
                                $_SESSION['authUser']['role'] = $role;

                                header('Location: welcome-user.php');
                                exit();
                            } else {
                                echo '<div class="alert alert-danger">Incorrect password. Please try again.</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger">Username not found. Please try again.</div>';
                        }
                    }

                    if (isset($_GET['logout'])) {
                        session_destroy();
                        header("Location: music-login.php");
                        exit();
                    }
                    ?>

                    <?php if (isset($_SESSION['authUser'])): ?>
                        <form method="get">
                            <input type="submit" name="logout" class="btn btn-secondary w-100" value="Log Out">
                        </form>
                    <?php else: ?>
                        <form method="post" action="?view=login">
                            <div class="form-group mb-3">
                                <input type="text" name="username" class="form-control" placeholder="Your Username *" required>
                            </div>
                            <div class="form-group mb-3">
                                <input type="password" name="password" class="form-control" placeholder="Your Password *" required>
                            </div>
                            <div class="form-group">
                                <input type="submit" name="login" class="btn btn-success w-100" value="Login">
                            </div>
                        </form>
                    <?php endif; ?>
                    <p class="mt-3 text-center">Don't have an account? <a href="?view=signup">Sign up here</a></p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php
include "includes/footer.php";
?>
