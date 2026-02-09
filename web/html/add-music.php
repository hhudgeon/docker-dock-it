<?php
include "includes/header.php";
$pageTitle = "Add Music";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

$error = '';
$success = '';

// Store values to retain input on error

/**
 * @var $db mysqli
 */
// Fetch for dropdowns
$artists = mysqli_query($db, "SELECT artistId, artistName FROM myrecords__artists ORDER BY artistName");
$labels = mysqli_query($db, "SELECT recordLabelId, recordLabelName FROM myrecords__recordLabel ORDER BY recordLabelName");
$genres = mysqli_query($db, "SELECT genreId, genreName FROM myrecords__genres ORDER BY genreName");
// For Add Track
$recordFormats = mysqli_query($db, "SELECT recordId, format, year FROM myrecords__records ORDER BY format");

// For Add Album (only records with recordName)
$namedRecords = mysqli_query($db, "SELECT recordId, recordName FROM myrecords__records WHERE recordName IS NOT NULL AND recordName != '' ORDER BY recordName");

// Add Artist
$artistNameInput = '';
if (isset($_POST['add_artist'])) {
    $artistName = strip_tags($_POST['artistName']);
    if ($artistName !== '') {
        $stmt = mysqli_prepare($db, "INSERT INTO myrecords__artists (artistName) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $artistName);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error adding artist: " . mysqli_error($db));
        }
        $success = "Artist added.";
        $artistNameInput = '';
        // Refresh dropdown data
        $artists = mysqli_query($db, "SELECT artistId, artistName FROM myrecords__artists ORDER BY artistName");
    } else {
        $error = "Artist name cannot be empty.";
    }
}

// Add Label
if (isset($_POST['add_label'])) {
    $labelName = strip_tags($_POST['recordLabelName']);
    if ($labelName !== '') {
        $stmt = mysqli_prepare($db, "INSERT INTO myrecords__recordLabel (recordLabelName) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $labelName);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error adding label: " . mysqli_error($db));
        }
        $success = "Label added.";

        // Refresh dropdown data
        $labels = mysqli_query($db, "SELECT recordLabelId, recordLabelName FROM myrecords__recordLabel ORDER BY recordLabelName");
    }
}
// Add 45 Record
if (isset($_POST['add_45'])) {
    $error = '';
    $recordLabelId = intval($_POST['recordLabelId']);
    $format = '45';

    $hasA = !empty($_POST['sideA_name']) && intval($_POST['sideA_artistId']) > 0;
    $hasB = !empty($_POST['sideB_name']) && intval($_POST['sideB_artistId']) > 0;

    if ($recordLabelId && ($hasA || $hasB)) {
        mysqli_query($db, "INSERT INTO myrecords__records (format, recordLabelId) VALUES ('$format', $recordLabelId)")
        or die("Error adding 45 record: " . mysqli_error($db));
        $recordId = mysqli_insert_id($db);

        if ($hasA) {
            $name = strip_tags($_POST['sideA_name']);
            $artistId = intval($_POST['sideA_artistId']);
            $genreId = intval($_POST['sideA_genreId']);
            $link = strip_tags($_POST['sideA_link']);
            $info = strip_tags($_POST['sideA_info']);

            $stmt = mysqli_prepare($db, "INSERT INTO myrecords__tracks (trackName, artistId, recordId, genreId, trackLink, trackInfo) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "siisis", $name, $artistId, $recordId, $genreId, $link, $info);
            if (!mysqli_stmt_execute($stmt)) {
                die("Error adding Side A track: " . mysqli_error($db));
            }

            mysqli_query($db, "INSERT IGNORE INTO myrecords__artist_record (artistId, recordId) VALUES ($artistId, $recordId)")
            or die("Error linking artist to 45 (Side A): " . mysqli_error($db));
        }

        if ($hasB) {
            $name = strip_tags($_POST['sideB_name']);
            $artistId = intval($_POST['sideB_artistId']);
            $genreId = intval($_POST['sideB_genreId']);
            $link = strip_tags($_POST['sideB_link']);
            $info = strip_tags($_POST['sideB_info']);

            $stmt = mysqli_prepare($db, "INSERT INTO myrecords__tracks (trackName, artistId, recordId, genreId, trackLink, trackInfo) VALUES (?, ?, ?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "siisis", $name, $artistId, $recordId, $genreId, $link, $info);
            if (!mysqli_stmt_execute($stmt)) {
                die("Error adding Side B track: " . mysqli_error($db));
            }

            mysqli_query($db, "INSERT IGNORE INTO myrecords__artist_record (artistId, recordId) VALUES ($artistId, $recordId)")
            or die("Error linking artist to 45 (Side B): " . mysqli_error($db));
        }

        $success = "45 record and tracks added successfully.";
    } else {
        $error = "Error: To add a 45 record, you must select a record label and enter at least one complete track with artist.";
    }
}

// Add LP Record
if (isset($_POST['add_lp'])) {
    $recordName = strip_tags($_POST['recordName']);
    $artistId = intval($_POST['artistId']);
    $albumLink = strip_tags($_POST['albumLink']);
    $recordLabelId = intval($_POST['recordLabelId']);
    $format = 'LP';

    if ($recordName && $artistId && $recordLabelId) {
        mysqli_query($db, "INSERT INTO myrecords__records (recordName, format, recordLabelId, albumLink) VALUES ('$recordName', '$format', $recordLabelId, '$albumLink')")
        or die("Error adding LP record: " . mysqli_error($db));
        $recordId = mysqli_insert_id($db);

        // Link artist to album
        mysqli_query($db, "INSERT INTO myrecords__artist_record (artistId, recordId) VALUES ($artistId, $recordId)")
        or die("Error linking artist to LP: " . mysqli_error($db));

        foreach ($_POST['trackNames'] as $index => $trackName) {
            $trackName = trim(strip_tags($trackName));
            $trackGenreId = isset($_POST['trackGenres'][$index]) && $_POST['trackGenres'][$index] !== '' ? intval($_POST['trackGenres'][$index]) : null;

            if ($trackName !== '') {
                $stmt = mysqli_prepare($db, "INSERT INTO myrecords__tracks (trackName, artistId, genreId, recordId) VALUES (?, ?, ?, ?)");
                mysqli_stmt_bind_param($stmt, "siii", $trackName, $artistId, $trackGenreId, $recordId);
                if (!mysqli_stmt_execute($stmt)) {
                    die("Error adding LP track: " . mysqli_error($db));
                }
            }
        }

        $success = "LP and tracks added successfully.";
    } else {
        $error = "All LP fields with * are required and at least one complete track.";
    }
}
// Add Genre
if (isset($_POST['add_genre'])) {
    $genreName = strip_tags($_POST['genreName']);
    if ($genreName !== '') {
        $stmt = mysqli_prepare($db, "INSERT INTO myrecords__genres (genreName) VALUES (?)");
        mysqli_stmt_bind_param($stmt, "s", $genreName);
        if (!mysqli_stmt_execute($stmt)) {
            die("Error adding genre: " . mysqli_error($db));
        }
        $success = "Genre added.";

        // Refresh dropdown data
        $genres = mysqli_query($db, "SELECT genreId, genreName FROM myrecords__genres ORDER BY genreName");
    }
}
?>

<div class="container py-4">
    <h1 class="mb-4">Add Music</h1><br>




    <?php if (!empty($error)): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($error) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php elseif (!empty($success)): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <?= htmlspecialchars($success) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    <?php endif; ?>

    <p class="text-muted p-bg">Fields marked with an asterisk (<span class="text-danger">*</span>) are required.</p>



    <ul class="nav nav-tabs bg-warning" id="musicTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['add_artist']) || empty($_POST) ? 'active' : '' ?>" data-bs-toggle="tab" href="#addArtist" role="tab">Add Artist</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['add_45']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#add45" role="tab">Add 45 Record</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['add_lp']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#addLP" role="tab">Add LP Record</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['add_label']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#addLabel" role="tab">Add Record Label</a>
        </li>
        <li class="nav-item">
            <a class="nav-link <?= isset($_POST['add_genre']) ? 'active' : '' ?>" data-bs-toggle="tab" href="#addGenre" role="tab">Add Genre</a>
        </li>
    </ul>

    <div class="tab-content mt-3 border">

        <!-- ============================
        Add Artist Tab
        ============================= -->
        <div class="tab-pane fade <?= (($_POST['form_submitted'] ?? '') === 'add_artist' || empty($_POST)) ? 'show active' : '' ?>" id="addArtist" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="add_artist" value="1">
                <input type="hidden" name="form_submitted" value="add_artist">
                <div class="mb-3">
                    <label for="artistName" class="form-label">Artist Name <span class="text-danger">*</span></label>
                    <input type="text" id="artistName" name="artistName" class="form-control" required value="<?= htmlspecialchars($artistNameInput) ?>">
                </div>
                <button class="btn btn-primary" type="submit">Add Artist</button>
                <hr>
                <label class="form-label">Existing Artists</label>
                <select class="form-select">
                    <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                        <option><?= htmlspecialchars($row['artistName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <!-- ============================
             Add 45 Record Tab
        ============================= -->
        <div class="tab-pane fade <?= (($_POST['form_submitted'] ?? '') === 'add_45') ? 'show active' : '' ?>" id="add45" role="tabpanel">
            <form method="post" class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Record Label <span class="text-danger">*</span></label>
                    <select name="recordLabelId" class="form-select">
                        <option value="">Select a record label</option>
                        <?php mysqli_data_seek($labels, 0); while($row = mysqli_fetch_assoc($labels)): ?>
                            <option value="<?= $row['recordLabelId'] ?>"><?= htmlspecialchars($row['recordLabelName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="format" value="45">
                <hr class="mt-4">
                <h5>Side A</h5>
                <div class="col-md-6">
                    <label for="sideA_name" class="form-label">Track Name <span class="text-danger">*</span></label>
                    <input type="text" id="sideA_name" name="sideA_name" class="form-control" value="<?= isset($_POST['sideA_name']) ? htmlspecialchars($_POST['sideA_name']) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="sideA_artistId" class="form-label">Artist <span class="text-danger">*</span></label>
                    <select id="sideA_artistId" name="sideA_artistId" class="form-select">
                        <option value="">Select an artist</option>
                        <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>"><?= htmlspecialchars($row['artistName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideA_genreId" class="form-label">Genre</label>
                    <select id="sideA_genreId" name="sideA_genreId" class="form-select">
                        <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                            <option value="<?= $row['genreId'] ?>"><?= htmlspecialchars($row['genreName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideA_link" class="form-label">Track Link</label>
                    <input type="url" id="sideA_link" name="sideA_link" class="form-control" value="<?= isset($_POST['sideA_link']) ? htmlspecialchars($_POST['sideA_link']) : '' ?>">
                </div>
                <div class="col-md-12">
                    <label for="sideA_info" class="form-label">Track Info</label>
                    <textarea id="sideA_info" name="sideA_info" class="form-control"><?=
                        isset($_POST['sideA_info']) ? htmlspecialchars($_POST['sideA_info']) : '' ?></textarea>
                </div>
                <hr class="mt-4">
                <h5>Side B</h5>
                <div class="col-md-6">
                    <label for="sideB_name" class="form-label">Track Name</label>
                    <input type="text" id="sideB_name" name="sideB_name" class="form-control" value="<?= isset($_POST['sideB_name']) ? htmlspecialchars($_POST['sideB_name']) : '' ?>">
                </div>
                <div class="col-md-6">
                    <label for="sideB_artistId" class="form-label">Artist</label>
                    <select id="sideB_artistId" name="sideB_artistId" class="form-select">
                        <option value="" disabled selected>Select an artist</option>
                        <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>"><?= htmlspecialchars($row['artistName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideB_genreId" class="form-label">Genre</label>
                    <select id="sideB_genreId" name="sideB_genreId" class="form-select">
                        <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                            <option value="<?= $row['genreId'] ?>"><?= htmlspecialchars($row['genreName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="sideB_link" class="form-label">Track Link</label>
                    <input type="url" id="sideB_link" name="sideB_link" class="form-control" value="<?= isset($_POST['sideB_link']) ? htmlspecialchars($_POST['sideB_link']) : '' ?>">
                </div>
                <div class="col-md-12">
                    <label for="sideB_info" class="form-label">Track Info</label>
                    <textarea id="sideB_info" name="sideB_info" class="form-control"><?=
                        isset($_POST['sideB_info']) ? htmlspecialchars($_POST['sideB_info']) : '' ?></textarea>
                </div>
                <div class="col-12">
                    <input type="hidden" name="add_45" value="1">
                    <input type="hidden" name="form_submitted" value="add_45">
                    <button class="btn btn-primary" type="submit">Add 45 Record</button>
                </div>
            </form>
        </div>
        <!-- ============================
        Add LP Record Tab
                ============================= -->
        <div class="tab-pane fade <?= (($_POST['form_submitted'] ?? '') === 'add_lp') ? 'show active' : '' ?>" id="addLP" role="tabpanel">
            <form method="post" class="mt-3">

                <div class="mb-3">
                    <label for="recordName" class="form-label">Album Name <span class="text-danger">*</span></label>
                    <input type="text" id="recordName" name="recordName" class="form-control" value="<?= isset($_POST['recordName']) ? htmlspecialchars($_POST['recordName']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Artist <span class="text-danger">*</span></label>
                    <select name="artistId" class="form-select">
                        <option value="" disabled selected>Select an artist</option>
                        <?php mysqli_data_seek($artists, 0); while($row = mysqli_fetch_assoc($artists)): ?>
                            <option value="<?= $row['artistId'] ?>"><?= htmlspecialchars($row['artistName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <input type="hidden" name="format" value="LP">
                <div class="mb-3">
                    <label class="form-label">Album Link</label>
                    <input type="url" name="albumLink" class="form-control" value="<?= isset($_POST['albumLink']) ? htmlspecialchars($_POST['albumLink']) : '' ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Record Label<span class="text-danger">*</span></label>
                    <select name="recordLabelId" class="form-select">
                        <?php mysqli_data_seek($labels, 0); while($row = mysqli_fetch_assoc($labels)): ?>
                            <option value="<?= $row['recordLabelId'] ?>"><?= htmlspecialchars($row['recordLabelName']) ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <?php for ($i = 1; $i <= 20; $i++): ?>
                    <div class="row mb-2">
                        <div class="col-md-6">
                            <label for="trackName_<?= $i ?>" class="form-label">Track <?= $i ?></label>
                            <input type="text" id="trackName_<?= $i ?>" name="trackNames[]" class="form-control"
                                   value="<?= isset($_POST['trackNames'][$i - 1]) ? htmlspecialchars($_POST['trackNames'][$i - 1]) : '' ?>">
                        </div>
                        <div class="col-md-6">
                            <label for="trackGenre_<?= $i ?>" class="form-label">Genre</label>
                            <select id="trackGenre_<?= $i ?>" name="trackGenres[]" class="form-select">
                                <option value="">Select a genre</option>
                                <?php mysqli_data_seek($genres, 0); while ($row = mysqli_fetch_assoc($genres)): ?>
                                    <option value="<?= $row['genreId'] ?>"
                                        <?= (isset($_POST['trackGenres'][$i - 1]) && $_POST['trackGenres'][$i - 1] == $row['genreId']) ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($row['genreName']) ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                <?php endfor; ?>
                <input type="hidden" name="add_lp" value="1">
                <input type="hidden" name="form_submitted" value="add_lp">
                <button class="btn btn-primary" type="submit">Add LP Record</button>
            </form>
        </div>

        <!-- ============================
     Add Label Tab
============================= -->
        <div class="tab-pane fade <?= (($_POST['form_submitted'] ?? '') === 'add_label') ? 'show active' : '' ?>" id="addLabel" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="add_label" value="1">
                <input type="hidden" name="form_submitted" value="add_label">
                <div class="mb-3">
                    <label for="recordLabelName" class="form-label">Record Label Name <span class="text-danger">*</span></label>
                    <input type="text" id="recordLabelName" name="recordLabelName" class="form-control" required value="<?= isset($_POST['recordLabelName']) ? htmlspecialchars($_POST['recordLabelName']) : '' ?>">
                </div>
                <button class="btn btn-primary" type="submit">Add Label</button>
                <hr>
                <label class="form-label">Existing Labels</label>
                <select class="form-select">
                    <?php mysqli_data_seek($labels, 0); while($row = mysqli_fetch_assoc($labels)): ?>
                        <option><?= htmlspecialchars($row['recordLabelName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>

        <!-- ============================
     Add Genre Tab
============================= -->
        <div class="tab-pane fade <?= (($_POST['form_submitted'] ?? '') === 'add_genre') ? 'show active' : '' ?>" id="addGenre" role="tabpanel">
            <form method="post" class="mt-3">
                <input type="hidden" name="add_genre" value="1">
                <input type="hidden" name="form_submitted" value="add_genre">
                <div class="mb-3">
                    <label for="genreName" class="form-label">Genre Name <span class="text-danger">*</span></label>
                    <input type="text" id="genreName" name="genreName" class="form-control" required value="<?= isset($_POST['genreName']) ? htmlspecialchars($_POST['genreName']) : '' ?>">
                </div>
                <button class="btn btn-primary" type="submit">Add Genre</button>
                <hr>
                <label class="form-label">Existing Genres</label>
                <select class="form-select">
                    <?php mysqli_data_seek($genres, 0); while($row = mysqli_fetch_assoc($genres)): ?>
                        <option><?= htmlspecialchars($row['genreName']) ?></option>
                    <?php endwhile; ?>
                </select>
            </form>
        </div>
    </div>
</div>
<?php include "includes/footer.php"; ?>


