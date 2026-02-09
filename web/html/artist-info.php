<?php
include "includes/header.php";
$pageTitle = "Artist Information";

// Validate artistId
$artistId = isset($_GET['artistId']) ? (int) $_GET['artistId'] : 0;
if ($artistId <= 0) {
    echo "<p>Invalid artist selected.</p>";
    include "includes/footer.php";
    exit;
}

// Get sort and dir from URL or use defaults
$sort = $_GET['sort'] ?? 'trackName';
$dir = strtoupper($_GET['dir'] ?? 'ASC');

// Whitelist of sortable columns
$validColumns = ['trackName', 'recordName', 'genreName', 'format', 'recordLabelName'];
if (!in_array($sort, $validColumns)) {
    $sort = 'trackName';
}
$dir = $dir === 'DESC' ? 'DESC' : 'ASC';

// Get artist info
$query = "
    SELECT 
        a.artistName,
        t.trackName,
        r.recordName,
        r.recordId,
        g.genreName,
        t.trackLink,
        rec.albumLink,
        rec.format,
        rl.recordLabelName,
        t.trackInfo
    FROM myrecords__tracks t
    LEFT JOIN myrecords__artists a ON t.artistId = a.artistId
    LEFT JOIN myrecords__records r ON t.recordId = r.recordId
    LEFT JOIN myrecords__genres g ON t.genreId = g.genreId
    LEFT JOIN myrecords__records rec ON t.recordId = rec.recordId
    LEFT JOIN myrecords__recordLabel rl ON rec.recordLabelId = rl.recordLabelId
    WHERE a.artistId = $artistId
    ORDER BY $sort $dir
";

// Run the query
$result = mysqli_query($db, $query);

// If the query fails (e.g., SQL error), show error message, footer, and exit early
if (!$result) {
    echo "<p>Query failed: " . mysqli_error($db) . "</p>";
    include "includes/footer.php";
    exit;
}

// Fetch the first row to get the artist's name
$row = mysqli_fetch_assoc($result);

// If no tracks are found for the artist, show message, footer, and exit
if (!$row) {
    echo "<p>No tracks found for this artist.</p>";
    include "includes/footer.php";
    exit;
}

$artistName = $row['artistName'];
?>

<div class="container py-4">
    <h1 class="mb-3">Tracks by <?= htmlspecialchars($artistName) ?></h1><br>
    <a href="all-records.php" class="btn btn-primary mb-3">&larr; Back to All Records</a>
    <table class="table table-bordered table-striped table-responsive">
        <thead class="table-warning">
        <tr>
            <th><?= sortableColumnHeader('trackName', 'Track Name', $sort, $dir, "artistId=$artistId") ?></th>
            <th><?= sortableColumnHeader('recordName', 'Album Name', $sort, $dir, "artistId=$artistId") ?></th>
            <th><?= sortableColumnHeader('genreName', 'Genre', $sort, $dir, "artistId=$artistId") ?></th>
            <th>Link</th>
            <th><?= sortableColumnHeader('format', 'Record Type', $sort, $dir, "artistId=$artistId") ?></th>
            <th><?= sortableColumnHeader('recordLabelName', 'Record Label', $sort, $dir, "artistId=$artistId") ?></th>
            <th>Track Info</th>
        </tr>
        </thead>
        <tbody>
        <?php
        // Rewind result to include the first row again
        mysqli_data_seek($result, 0);
        while ($track = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?= htmlspecialchars($track['trackName']) ?></td>
                <td>
                    <?php if (!empty($track['recordName'])): ?>
                        <a href="album-info.php?recordId=<?= urlencode($track['recordId']) ?>">
                            <?= htmlspecialchars($track['recordName']) ?>
                        </a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($track['genreName'] ?? '—') ?></td>
                <td>
                    <?php if (!empty($track['trackLink'])): ?>
                        <a href="<?= htmlspecialchars($track['trackLink']) ?>" target="_blank" rel="noopener noreferrer">Listen</a>
                    <?php elseif (!empty($track['albumLink'])): ?>
                        <a href="<?= htmlspecialchars($track['albumLink']) ?>" target="_blank" rel="noopener noreferrer">Album</a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($track['format']) ?></td>
                <td><?= htmlspecialchars($track['recordLabelName']) ?></td>
                <td><?= nl2br(htmlspecialchars($track['trackInfo'])) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>

<?php include "includes/footer.php"; ?>
