<?php
include "includes/header.php";
$pageTitle = "Album Information";


// Validate recordId
$recordId = isset($_GET['recordId']) ? (int) $_GET['recordId'] : 0;
if ($recordId <= 0) {
    echo "<p>Invalid album selected.</p>";
    include "includes/footer.php";
    exit;
}

// Query album and track details
$query = "
    SELECT 
        a.artistName,
        r.recordName,
        r.albumLink,
        rl.recordLabelName,
        t.trackName,
        g.genreName,
        t.trackInfo
    FROM myrecords__records r
    LEFT JOIN myrecords__recordLabel rl ON r.recordLabelId = rl.recordLabelId
    LEFT JOIN myrecords__tracks t ON r.recordId = t.recordId
    LEFT JOIN myrecords__artists a ON t.artistId = a.artistId
    LEFT JOIN myrecords__genres g ON t.genreId = g.genreId
    WHERE r.recordId = ?
";

$stmt = mysqli_prepare($db, $query);
mysqli_stmt_bind_param($stmt, "i", $recordId);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);


if (!$result) {
    echo "<p>Query failed: " . mysqli_error($db) . "</p>";
    include "includes/footer.php";
    exit;
}

$tracks = [];
$albumInfo = null;
while ($row = mysqli_fetch_assoc($result)) {
    if (!$albumInfo) {
        $albumInfo = [
            'artistName' => $row['artistName'],
            'recordName' => $row['recordName'],
            'albumLink' => $row['albumLink'],
            'recordLabelName' => $row['recordLabelName']
        ];
    }
    $tracks[] = [
        'trackName' => $row['trackName'],
        'genreName' => $row['genreName'],
        'trackInfo' => $row['trackInfo']
    ];
}

if (!$albumInfo) {
    echo "<p>No information found for this album.</p>";
    include "includes/footer.php";
    exit;
}
?>
<div class="album-body">
    <div class="album-card">
        <h1 class="mb-3"><?= htmlspecialchars($albumInfo['recordName']) ?></h1>
        <h3><strong>Artist:</strong> <?= htmlspecialchars($albumInfo['artistName']) ?></h3>
        <p><strong>Record Label:</strong> <?= htmlspecialchars($albumInfo['recordLabelName']) ?></p>
        <?php if (!empty($albumInfo['albumLink'])): ?>
            <p><a href="<?= htmlspecialchars($albumInfo['albumLink']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">Listen to Album</a></p>
        <?php endif; ?>

        <h4 class="mt-4">Tracks</h4>
        <ul class="list-group">
            <?php foreach ($tracks as $track): ?>
                <li class="list-group-item">
                    <strong><?= htmlspecialchars($track['trackName']) ?></strong>
                    <?php if (!empty($track['genreName'])): ?>
                        <em>(<?= htmlspecialchars($track['genreName']) ?>)</em>
                    <?php endif; ?>
                    <?php if (!empty($track['trackInfo'])): ?>
                        <br><small><?= nl2br(htmlspecialchars($track['trackInfo'])) ?></small>
                    <?php endif; ?>
                </li>
            <?php endforeach; ?>
        </ul>

        <a href="all-records.php" class="btn btn-primary mt-4">&larr; Back to All Records</a>
    </div>
</div>

<?php include "includes/footer.php"; ?>
