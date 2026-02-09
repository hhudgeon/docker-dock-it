<?php
include "includes/header.php";
$pageTitle = "Edit 45 Record";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

$success = '';
$error = '';

// Always define recordId regardless of request method
$recordId = (int) ($_POST['recordId'] ?? $_GET['recordId'] ?? 0);

// Get existing record label from database for dropdown default
$recordLabelId = 0;
$recordQuery = "SELECT format, recordLabelId FROM myrecords__records WHERE recordId = $recordId";
$recordResult = mysqli_query($db, $recordQuery);
$record = mysqli_fetch_assoc($recordResult);
if ($record) {
    $recordLabelId = (int) $record['recordLabelId'];
}

// Fetch current tracks
$trackQuery = "SELECT trackId, trackName, trackLink, trackInfo, artistId, genreId FROM myrecords__tracks WHERE recordId = $recordId ORDER BY trackId";
$trackResult = mysqli_query($db, $trackQuery);
$tracks = mysqli_fetch_all($trackResult, MYSQLI_ASSOC);

$sideA = $tracks[0] ?? null;
$sideB = $tracks[1] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = false;

    // Sanitize Record Label ID
    $recordLabelId = (int) ($_POST['recordLabelId'] ?? 0);

    // Sanitize Side A
    $sideA_name = htmlspecialchars(strip_tags(trim($_POST['sideA_name'])));
    $sideA_artistId = (int) ($_POST['sideA_artistId'] ?? 0);
    $sideA_genreId = isset($_POST['sideA_genreId']) && $_POST['sideA_genreId'] !== '' ? (int) $_POST['sideA_genreId'] : null;
    $sideA_link = htmlspecialchars(strip_tags(trim($_POST['sideA_link'])));
    $sideA_info = htmlspecialchars(strip_tags(trim($_POST['sideA_info'])));

    // Sanitize Side B
    $sideB_name = htmlspecialchars(strip_tags(trim($_POST['sideB_name'])));
    $sideB_artistId = (int) ($_POST['sideB_artistId'] ?? 0);
    $sideB_genreId = isset($_POST['sideB_genreId']) && $_POST['sideB_genreId'] !== '' ? (int) $_POST['sideB_genreId'] : null;
    $sideB_link = htmlspecialchars(strip_tags(trim($_POST['sideB_link'])));
    $sideB_info = htmlspecialchars(strip_tags(trim($_POST['sideB_info'])));

    // Update label
    $stmt = mysqli_prepare($db, "UPDATE myrecords__records SET recordLabelId = ? WHERE recordId = ?");
    mysqli_stmt_bind_param($stmt, "ii", $recordLabelId, $recordId);
    if (mysqli_stmt_execute($stmt)) {
        $updated = true;
    }

    // Update Side A track
    if (isset($sideA['trackId'])) {
        $q = "UPDATE myrecords__tracks SET trackName=?, artistId=?, genreId=?, trackLink=?, trackInfo=? WHERE trackId=?";
        $s = mysqli_prepare($db, $q);
        mysqli_stmt_bind_param($s, "sisssi", $sideA_name, $sideA_artistId, $sideA_genreId, $sideA_link, $sideA_info, $sideA['trackId']);
        if (mysqli_stmt_execute($s)) {
            $updated = true;
        }
    }

    // Update or insert Side B track
    if (!empty($sideB_name) && $sideB_artistId > 0) {
        if (isset($sideB['trackId'])) {
            $q = "UPDATE myrecords__tracks SET trackName=?, artistId=?, genreId=?, trackLink=?, trackInfo=? WHERE trackId=?";
            $s = mysqli_prepare($db, $q);
            mysqli_stmt_bind_param($s, "sisssi", $sideB_name, $sideB_artistId, $sideB_genreId, $sideB_link, $sideB_info, $sideB['trackId']);
            if (mysqli_stmt_execute($s)) {
                $updated = true;
            }
        } else {
            $q = "INSERT INTO myrecords__tracks (trackName, artistId, genreId, trackLink, trackInfo, recordId) VALUES (?, ?, ?, ?, ?, ?)";
            $s = mysqli_prepare($db, $q);
            mysqli_stmt_bind_param($s, "sisssi", $sideB_name, $sideB_artistId, $sideB_genreId, $sideB_link, $sideB_info, $recordId);
            if (mysqli_stmt_execute($s)) {
                $updated = true;
            }
        }
    }

    if ($updated) {
        $success = "Track updated successfully!";
    } else {
        $error = "No changes were made to the record.";
    }
}

$labels = mysqli_query($db, "SELECT recordLabelId, recordLabelName FROM myrecords__recordLabel ORDER BY recordLabelName");
$artists = mysqli_query($db, "SELECT artistId, artistName FROM myrecords__artists ORDER BY artistName");
$genres = mysqli_query($db, "SELECT genreId, genreName FROM myrecords__genres ORDER BY genreName");

// Fetch (ruff! I'm tired...) record info and tracks
$recordQuery = "SELECT format, recordLabelId FROM myrecords__records WHERE recordId = $recordId";
$recordResult = mysqli_query($db, $recordQuery);
$record = mysqli_fetch_assoc($recordResult);

$trackQuery = "
    SELECT trackId, trackName, trackLink, trackInfo, artistId, genreId
    FROM myrecords__tracks
    WHERE recordId = $recordId
    ORDER BY trackId
";
$trackResult = mysqli_query($db, $trackQuery);
$tracks = mysqli_fetch_all($trackResult, MYSQLI_ASSOC);

$sideA = $tracks[0] ?? null;
$sideB = $tracks[1] ?? null;
?>

<?php
// Preload Side A artist name
$artistA = '';
mysqli_data_seek($artists, 0);
while ($row = mysqli_fetch_assoc($artists)) {
    if (isset($sideA['artistId']) && $row['artistId'] == $sideA['artistId']) {
        $artistA = $row['artistName'];
        break;
    }
}

// Preload Side A genre name
$genreA = '';
mysqli_data_seek($genres, 0);
while ($row = mysqli_fetch_assoc($genres)) {
    if (isset($sideA['genreId']) && $row['genreId'] == $sideA['genreId']) {
        $genreA = $row['genreName'];
        break;
    }
}

// Preload record label name
$labelName = "(not set)";
mysqli_data_seek($labels, 0);
while ($row = mysqli_fetch_assoc($labels)) {
    if ($row['recordLabelId'] == $record['recordLabelId']) {
        $labelName = $row['recordLabelName'];
        break;
    }
}
?>

<?php if (!empty($success)): ?>
    <div class="container mt-4">
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="container py-4">
    <a href="manage-45-records.php" class="btn btn-info mb-3">&larr; Back to Manage 45's</a>
    <div class="card shadow-lg">
        <div class="card-header bg-light">
            <h4 class="mb-0">Edit 45 Record</h4>
        </div>
        <div class="card-body">
            <div class="row g-4 mb-4 align-items-start">
                <div class="col-md-6 pe-md-5">

                    <h5 class="fw-bold border-bottom pb-2 mb-3">Side A Summary</h5>
                    <p><strong>Current Track Name:</strong> <?= htmlspecialchars($sideA['trackName'] ?? '') ?></p>
                    <p><strong>Current Record Label:</strong> <?= htmlspecialchars($labelName) ?></p>
                    <p><strong>Current Artist Name:</strong> <?= htmlspecialchars($artistA) ?></p>
                    <p><strong>Current Genre:</strong> <?= htmlspecialchars($genreA) ?></p>
                    <p><strong>Current Track Link:</strong> <?= htmlspecialchars($sideA['trackLink'] ?? '') ?></p>
                    <p><strong>Current Track Info:</strong> <?= htmlspecialchars($sideA['trackInfo'] ?? '') ?></p>
                </div>
                <div class="col-md-6 ps-md-5">
                    <h5 class="fw-bold border-bottom pb-2 mb-3">Side B Summary</h5>
                    <p><strong>Current Track Name:</strong> <?= htmlspecialchars($sideB['trackName'] ?? '') ?></p>
                    <p><strong>Current Artist Name:</strong> <?php
                        mysqli_data_seek($artists, 0);
                        $artistB = '';
                        while ($row = mysqli_fetch_assoc($artists)) {
                            if (isset($sideB['artistId']) && $row['artistId'] == $sideB['artistId']) {
                                $artistB = $row['artistName'];
                                break;
                            }
                        }
                        echo htmlspecialchars($artistB);
                        ?></p>
                    <p><strong>Current Genre:</strong> <?php
                        mysqli_data_seek($genres, 0);
                        $genreB = '';
                        while ($row = mysqli_fetch_assoc($genres)) {
                            if (isset($sideB['genreId']) && $row['genreId'] == $sideB['genreId']) {
                                $genreB = $row['genreName'];
                                break;
                            }
                        }
                        echo htmlspecialchars($genreB);
                        ?></p>
                    <p><strong>Current Track Link:</strong> <?= htmlspecialchars($sideB['trackLink'] ?? '') ?></p>
                    <p><strong>Current Track Info:</strong> <?= htmlspecialchars($sideB['trackInfo'] ?? '') ?></p>
                </div>
            </div>

            <hr class="my-4">
            <form method="post" class="row g-3">
                <div class="col-md-6">
                    <label for="recordLabelId" class="form-label">Record Label <span class="text-danger">*</span></label>
                    <select id="recordLabelId" name="recordLabelId" class="form-select" required>
                        <option value="">Select a record label</option>
                        <?php mysqli_data_seek($labels, 0); while($row = mysqli_fetch_assoc($labels)): ?>
                            <option value="<?= $row['recordLabelId'] ?>" <?= $row['recordLabelId'] == $record['recordLabelId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['recordLabelName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="format" value="45">
                <hr class="mt-4">
                <h5>Side A</h5>
                <div class="col-md-6">
                    <label for="sideA_name" class="form-label">Track Name <span class="text-danger">*</span></label>
                    <input type="text" id="sideA_name" name="sideA_name" class="form-control" value="<?= htmlspecialchars($sideA['trackName'] ?? '') ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="sideA_artistId" class="form-label">Artist <span class="text-danger">*</span></label>
                    <select id="sideA_artistId" name="sideA_artistId" class="form-select" required>
                        <option value="">Select an artist</option>
                        <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>" <?= isset($sideA['artistId']) && $row['artistId'] == $sideA['artistId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['artistName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideA_genreId" class="form-label">Genre</label>
                    <select id="sideA_genreId" name="sideA_genreId" class="form-select">
                        <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                            <option value="<?= $row['genreId'] ?>" <?= isset($sideA['genreId']) && $row['genreId'] == $sideA['genreId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['genreName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideA_link" class="form-label">Track Link</label>
                    <input type="url" id="sideA_link" name="sideA_link" class="form-control" value="<?= htmlspecialchars($sideA['trackLink'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label for="sideA_info" class="form-label">Track Info</label>
                    <textarea id="sideA_info" name="sideA_info" class="form-control"><?= htmlspecialchars($sideA['trackInfo'] ?? '') ?></textarea>
                </div>
                <hr class="mt-4">
                <h5>Side B</h5>
                <div class="col-md-6">
                    <label for="sideB_name" class="form-label">Track Name</label>
                    <input type="text" id="sideB_name" name="sideB_name" class="form-control" value="<?= htmlspecialchars($sideB['trackName'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label for="sideB_artistId" class="form-label">Artist</label>
                    <select id="sideB_artistId" name="sideB_artistId" class="form-select">
                        <option value="">Select an artist</option>
                        <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>" <?= isset($sideB['artistId']) && $row['artistId'] == $sideB['artistId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['artistName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideB_genreId" class="form-label">Genre</label>
                    <select id="sideB_genreId" name="sideB_genreId" class="form-select">
                        <option value="">Select a genre</option>
                        <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                            <option value="<?= $row['genreId'] ?>" <?= isset($sideB['genreId']) && $row['genreId'] == $sideB['genreId'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['genreName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideB_link" class="form-label">Track Link</label>
                    <input type="url" id="sideB_link" name="sideB_link" class="form-control" value="<?= htmlspecialchars($sideB['trackLink'] ?? '') ?>">
                </div>
                <div class="col-md-12">
                    <label for="sideB_info" class="form-label">Track Info</label>
                    <textarea id="sideB_info" name="sideB_info" class="form-control"><?= htmlspecialchars($sideB['trackInfo'] ?? '') ?></textarea>
                </div>
                <div class="col-12">
                    <input type="hidden" name="recordId" value="<?= $recordId ?>">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="manage-45-records.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include "includes/footer.php"; ?>
