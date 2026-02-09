<?php
include "includes/header.php";
require_once "includes/functions.php";
$pageTitle = "Manage Music";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

$success = '';
$error = '';

// If form submitted to edit artist
if (isset($_POST['editArtist'])) {
    $artistId = (int) $_POST['artistId'];
    $artistName = mysqli_real_escape_string($db, strip_tags(trim($_POST['artistName'])));
    if ($artistName === '') {
        $error = "Artist name cannot be empty.";
    } else {
        $query = "UPDATE myrecords__artists SET artistName = ? WHERE artistId = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "si", $artistName, $artistId);
        mysqli_stmt_execute($stmt);
        $success = "Artist updated successfully.";
    }
}

// If form submitted to edit label
if (isset($_POST['editLabel'])) {
    $labelId = (int) $_POST['labelId'];
    $labelName = mysqli_real_escape_string($db, strip_tags(trim($_POST['labelName'])));
    if ($labelName === '') {
        $error = "Record label name cannot be empty.";
    } else {
        $query = "UPDATE myrecords__recordLabel SET recordLabelName = ? WHERE recordLabelId = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "si", $labelName, $labelId);
        mysqli_stmt_execute($stmt);
        $success = "Record label updated successfully.";
    }
}

// If form submitted to edit genre
if (isset($_POST['editGenre'])) {
    $genreId = (int) $_POST['genreId'];
    $genreName = mysqli_real_escape_string($db, strip_tags(trim($_POST['genreName'])));
    if ($genreName === '') {
        $error = "Genre name cannot be empty.";
    } else {
        $query = "UPDATE myrecords__genres SET genreName = ? WHERE genreId = ?";
        $stmt = mysqli_prepare($db, $query);
        mysqli_stmt_bind_param($stmt, "si", $genreName, $genreId);
        mysqli_stmt_execute($stmt);
        $success = "Genre updated successfully.";
    }
}

// Handle delete artist (with FK and track check)
if (isset($_POST['deleteArtist'])) {
    $artistId = (int) $_POST['artistId'];
    if ($artistId) {
        if (hasAssociatedTracks($db, $artistId)) {
            $error = "This artist is associated with one or more tracks and cannot be deleted.";
        } else {
            $query = "DELETE FROM myrecords__artists WHERE artistId = ?";
            $stmt = mysqli_prepare($db, $query);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "i", $artistId);
                mysqli_stmt_execute($stmt);
                $affected = mysqli_stmt_affected_rows($stmt);

                if ($affected > 0) {
                    $success = "Artist deleted successfully.";
                } else {
                    $error = "Artist could not be deleted. Either it doesn't exist or is referenced by another table.";
                }

                mysqli_stmt_close($stmt);
            } else {
                $error = "Failed to prepare delete statement: " . mysqli_error($db);
            }
        }
    } else {
        $error = "Please select an artist to delete.";
    }
}

// Handle delete label
if (isset($_POST['deleteLabel'])) {
    $labelId = (int) $_POST['labelId'];
    if ($labelId) {
        if (hasAssociatedRecords($db, $labelId)) {
            $error = "This label is associated with one or more records and cannot be deleted.";
        } else {
            $stmt = mysqli_prepare($db, "DELETE FROM myrecords__recordLabel WHERE recordLabelId = ?");
            mysqli_stmt_bind_param($stmt, "i", $labelId);
            mysqli_stmt_execute($stmt);
            $success = "Record label deleted successfully.";
        }
    } else {
        $error = "Please select a label to delete.";
    }
}

// Handle delete genre
if (isset($_POST['deleteGenre'])) {
    $genreId = (int) $_POST['genreId'];
    if ($genreId) {
        if (hasAssociations($db, 'myrecords__tracks', 'genreId', $genreId)) {
            $error = "This genre is associated with one or more tracks and cannot be deleted.";
        } else {
            $stmt = mysqli_prepare($db, "DELETE FROM myrecords__genres WHERE genreId = ?");
            mysqli_stmt_bind_param($stmt, "i", $genreId);
            mysqli_stmt_execute($stmt);
            $success = "Genre deleted successfully.";
        }
    } else {
        $error = "Please select a genre to delete.";
    }
}

// Refresh dropdowns after updates/deletes
$artists = mysqli_query($db, "SELECT artistId, artistName FROM myrecords__artists ORDER BY artistName");
$labels = mysqli_query($db, "SELECT recordLabelId, recordLabelName FROM myrecords__recordLabel ORDER BY recordLabelName");
$genres = mysqli_query($db, "SELECT genreId, genreName FROM myrecords__genres ORDER BY genreName");
?>






<div class="container py-4">
    <h1 class="mb-4">Manage Music</h1>

    <!-- Links to record management -->
    <div class="d-flex justify-content-start gap-2 mb-3">
        <a href="manage-45-records.php" class="btn btn-info">
            Manage 45 Records <span class="ms-2">&rarr;</span>
        </a>
        <a href="manage-lp-records.php" class="btn btn-info">
            Manage LP Records <span class="ms-2">&rarr;</span>
        </a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <ul class="nav nav-tabs bg-warning" id="manageTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['editArtist']) || empty($_POST) ? 'active' : '' ?>" data-bs-toggle="tab" href="#artist" role="tab">Edit Artist</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['editLabel']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#label" role="tab">Edit Record Label</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['editGenre']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#genre" role="tab">Edit Genre</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['deleteArtist']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#delete-artist" role="tab">Delete Artist</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['deleteLabel']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#delete-label" role="tab">Delete Record Label</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['deleteGenre']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#delete-genre" role="tab">Delete Genre</a>
        </li>
    </ul>

    <div class="tab-content p-3 border">

        <!-- ============================
     Edit Artist Tab
     ============================= -->
        <div class="tab-pane fade <?= isset($_POST['editArtist']) || empty($_POST) ? 'show active' : '' ?>" id="artist" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="editArtist" value="1">
                <label for="artistId"><select id="artistId" class="form-select mb-2" name="artistId">
                        <option value="">Choose artist</option>
                        <?php while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>"><?= htmlspecialchars($row['artistName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="artistName" class="form-control mb-2" placeholder="New Artist Name">
                    <button type="submit" class="btn btn-primary">Update Artist</button>
            </form>
        </div>

        <!-- ============================
     Edit Record Label Tab
     ============================= -->

        <div class="tab-pane fade <?= isset($_POST['editLabel']) ? 'show active' : '' ?>" id="label" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="editLabel" value="1">
                <label for="labelId"><select id="labelId" class="form-select mb-2" name="labelId">
                        <option value="">Choose label</option>
                        <?php while($row = mysqli_fetch_assoc($labels)): ?>
                            <option value="<?= $row['recordLabelId'] ?>"><?= htmlspecialchars($row['recordLabelName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="labelName" class="form-control mb-2" placeholder="New Label Name">
                    <button type="submit" class="btn btn-primary">Update Label</button>
            </form>
        </div>

        <!-- ============================
     Edit Genre Tab
     ============================= -->
        <div class="tab-pane fade <?= isset($_POST['editGenre']) ? 'show active' : '' ?>" id="genre" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="editGenre" value="1">
                <label for="genreId"><select id="genreId" class="form-select mb-2" name="genreId">
                        <option value="">Choose genre</option>
                        <?php while($row = mysqli_fetch_assoc($genres)): ?>
                            <option value="<?= $row['genreId'] ?>"><?= htmlspecialchars($row['genreName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                    <input type="text" name="genreName" class="form-control mb-2" placeholder="New Genre Name">
                    <button type="submit" class="btn btn-primary">Update Genre</button>
            </form>
        </div>

        <!-- ============================
     Delete Artist Tab
     ============================= -->

        <div class="tab-pane fade <?= isset($_POST['deleteArtist']) ? 'show active' : '' ?>" id="delete-artist" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="deleteArtist" value="1">
                <label>Select Artist to Delete</label>
                <select class="form-select mb-2" name="artistId">
                    <option value="">Choose artist</option>
                    <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                        <option value="<?= $row['artistId'] ?>"><?= htmlspecialchars($row['artistName']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this artist?')">Delete Artist</button>
            </form>
        </div>

        <!-- ============================
     Delete Record Label Tab
     ============================= -->
        <div class="tab-pane fade <?= isset($_POST['deleteLabel']) ? 'show active' : '' ?>" id="delete-label" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="deleteLabel" value="1">
                <label>Select Label to Delete</label>
                <select class="form-select mb-2" name="labelId">
                    <option value="">Choose label</option>
                    <?php mysqli_data_seek($labels, 0); while($row = mysqli_fetch_assoc($labels)): ?>
                        <option value="<?= $row['recordLabelId'] ?>"><?= htmlspecialchars($row['recordLabelName']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this label?')">Delete Label</button>
            </form>
        </div>

        <!-- ============================
       Delete Genre Tab
       ============================= -->
        <div class="tab-pane fade <?= isset($_POST['deleteGenre']) ? 'show active' : '' ?>" id="delete-genre" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="deleteGenre" value="1">
                <label>Select Genre to Delete</label>
                <select class="form-select mb-2" name="genreId">
                    <option value="">Choose genre</option>
                    <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                        <option value="<?= $row['genreId'] ?>"><?= htmlspecialchars($row['genreName']) ?></option>
                    <?php endwhile; ?>
                </select>
                <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this genre?')">Delete Genre</button>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
