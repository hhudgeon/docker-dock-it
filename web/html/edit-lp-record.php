<?php
include "includes/header.php";
$pageTitle = "Edit LP Record";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

$success = '';
$error = '';

$recordId = (int)($_POST['recordId'] ?? $_GET['recordId'] ?? 0);

$recordQuery = "SELECT recordName, recordLabelId, albumLink FROM myrecords__records WHERE recordId = $recordId AND format = 'LP'";
$recordResult = mysqli_query($db, $recordQuery);

if (!$recordResult) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Database error: " . mysqli_error($db) . "</div></div>";
    include "includes/footer.php";
    exit;
}

$record = mysqli_fetch_assoc($recordResult);
if (!$record) {
    echo "<div class='container py-4'><div class='alert alert-danger'>Invalid LP Record ID.</div></div>";
    include "includes/footer.php";
    exit;
}

$recordLabelId = (int)$record['recordLabelId'];

$labels = mysqli_query($db, "SELECT recordLabelId, recordLabelName FROM myrecords__recordLabel ORDER BY recordLabelName");
$genres = mysqli_query($db, "SELECT genreId, genreName FROM myrecords__genres ORDER BY genreName");

$trackQuery = "SELECT trackId, trackName, genreId, trackInfo FROM myrecords__tracks WHERE recordId = $recordId ORDER BY trackId";
$trackResult = mysqli_query($db, $trackQuery);
$tracks = mysqli_fetch_all($trackResult, MYSQLI_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updated = false;

    $recordName = htmlspecialchars(strip_tags(trim($_POST['recordName'] ?? '')));
    $recordLabelId = (int)($_POST['recordLabelId'] ?? 0);
    $albumLink = htmlspecialchars(strip_tags(trim($_POST['albumLink'] ?? '')));

    $stmt = mysqli_prepare($db, "UPDATE myrecords__records SET recordName=?, recordLabelId=?, albumLink=? WHERE recordId=?");
    mysqli_stmt_bind_param($stmt, "sisi", $recordName, $recordLabelId, $albumLink, $recordId);
    if (mysqli_stmt_execute($stmt)) {
        $updated = true;
    } else {
        $error .= "Record update failed: " . mysqli_error($db) . "<br>";
    }

    if (!empty($_POST['trackId']) && is_array($_POST['trackId'])) {
        foreach ($_POST['trackId'] as $i => $trackId) {
            $trackName = htmlspecialchars(strip_tags(trim($_POST['trackName'][$i] ?? '')));
            $trackGenreId = isset($_POST['trackGenre'][$i]) && $_POST['trackGenre'][$i] !== '' ? (int) $_POST['trackGenre'][$i] : null;
            $trackInfo = htmlspecialchars(strip_tags(trim($_POST['trackInfo'][$i] ?? '')));

            if ($trackId !== '') {
                $tid = (int)$trackId;
                $stmt = mysqli_prepare($db, "UPDATE myrecords__tracks SET trackName=?, genreId=?, trackInfo=? WHERE trackId=?");
                if (!$stmt) {
                    $error .= "Prepare failed for track update: " . mysqli_error($db) . "<br>";
                    continue;
                }
                mysqli_stmt_bind_param($stmt, "sisi", $trackName, $trackGenreId, $trackInfo, $tid);
                if (mysqli_stmt_execute($stmt)) {
                    $updated = true;
                } else {
                    $error .= "Track update failed: " . mysqli_error($db) . "<br>";
                }
            }
        }
    }

    if ($updated) {
        $success = "LP Record updated successfully!";

        // Refresh updated data for summary display
        $recordQuery = "SELECT recordName, recordLabelId, albumLink FROM myrecords__records WHERE recordId = $recordId AND format = 'LP'";
        $recordResult = mysqli_query($db, $recordQuery);
        $record = mysqli_fetch_assoc($recordResult);
        $recordLabelId = (int)$record['recordLabelId'];

        $trackQuery = "SELECT trackId, trackName, genreId, trackInfo FROM myrecords__tracks WHERE recordId = $recordId ORDER BY trackId";
        $trackResult = mysqli_query($db, $trackQuery);
        $tracks = mysqli_fetch_all($trackResult, MYSQLI_ASSOC);
    } elseif (empty($error)) {
        $error = "No changes were made.";
    }
}
?>


    <?php if ($success): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= $success ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= $error ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>
</div>

<div class="container py-4">
    <a href="manage-lp-records.php" class="btn btn-info mb-3">&larr; Manage LP Records</a>
<div class="card shadow-lg">
        <div class="card-header bg-light">
            <h4 class="mb-0">Edit LP Record</h4>
        </div>
        <div class="card-body">
            <h5 class="fw-bold border-bottom pb-2 mb-3">LP Summary</h5>
            <p><strong>Current Record Name:</strong> <?= htmlspecialchars($record['recordName']) ?></p>
            <p><strong>Current Album Link:</strong> <?= htmlspecialchars($record['albumLink']) ?></p>
            <p><strong>Current Record Label:</strong>
                <?php
                mysqli_data_seek($labels, 0);
                while ($row = mysqli_fetch_assoc($labels)) {
                    if ($row['recordLabelId'] == $record['recordLabelId']) {
                        echo htmlspecialchars($row['recordLabelName']);
                        break;
                    }
                }
                ?>
            </p>
            <p><strong>Current Tracks:</strong></p>
            <ul>
                <?php foreach ($tracks as $track): ?>
                    <li>
                        <?= htmlspecialchars($track['trackName']) ?> (Genre:
                        <?php
                        mysqli_data_seek($genres, 0);
                        while ($row = mysqli_fetch_assoc($genres)) {
                            if ($track['genreId'] == $row['genreId']) {
                                echo htmlspecialchars($row['genreName']);
                                break;
                            }
                        }
                        ?>)
                        <?= $track['trackInfo'] ? "<br><em>Info: " . htmlspecialchars($track['trackInfo']) . "</em>" : '' ?>
                    </li>
                <?php endforeach; ?>
            </ul>

            <form method="post" class="row g-3">
                <input type="hidden" name="recordId" value="<?= $recordId ?>">

                <div class="col-md-6">
                    <label for="recordName" class="form-label">Record Name</label>
                    <input type="text" id="recordName" name="recordName" class="form-control"
                           value="<?= htmlspecialchars($record['recordName']) ?>" required>
                </div>

                <div class="col-md-6">
                    <label for="recordLabelId" class="form-label">Record Label</label>
                    <select id="recordLabelId" name="recordLabelId" class="form-select" required>
                        <option value="">Select label</option>
                        <?php mysqli_data_seek($labels, 0);
                        while ($row = mysqli_fetch_assoc($labels)): ?>
                            <option value="<?= $row['recordLabelId'] ?>" <?= $row['recordLabelId'] == $recordLabelId ? 'selected' : '' ?>>
                                <?= htmlspecialchars($row['recordLabelName']) ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="col-md-12">
                    <label for="albumLink" class="form-label">Album Link</label>
                    <input type="url" id="albumLink" name="albumLink" class="form-control"
                           value="<?= htmlspecialchars($record['albumLink']) ?>">
                </div>

                <hr class="mt-4">
                <h5>Tracks</h5>
                <?php foreach ($tracks as $i => $track): ?>
                    <div class="row mb-3">
                        <input type="hidden" name="trackId[]" value="<?= $track['trackId'] ?>">
                        <div class="col-md-4">
                            <label for="trackName<?= $i ?>" class="form-label">Track Name</label>
                            <input type="text" id="trackName<?= $i ?>" name="trackName[]" class="form-control"
                                   value="<?= htmlspecialchars($track['trackName']) ?>">
                        </div>
                        <div class="col-md-4">
                            <label for="trackGenre<?= $i ?>" class="form-label">Genre</label>
                            <select id="trackGenre<?= $i ?>" name="trackGenre[]" class="form-select">
                                <option value="">Select genre</option>
                                <?php mysqli_data_seek($genres, 0);
                                while ($row = mysqli_fetch_assoc($genres)): ?>
                                    <option value="<?= $row['genreId'] ?>" <?= $row['genreId'] == $track['genreId'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['genreName']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="trackInfo<?= $i ?>" class="form-label">Track Info</label>
                            <input type="text" id="trackInfo<?= $i ?>" name="trackInfo[]" class="form-control"
                                   value="<?= htmlspecialchars($track['trackInfo']) ?>">
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="col-12">
                    <button class="btn btn-primary" type="submit">Save Changes</button>
                    <a href="manage-lp-records.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
<?php include "includes/footer.php"; ?>
