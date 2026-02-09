<?php
include "includes/header.php";
$pageTitle = "All Records";


// Handle sorting parameters from URL
$sort = $_GET['sort'] ?? 'artistName';
$dir = $_GET['dir'] ?? 'ASC';

// whitelist of allowed columns to sort by
$validColumns = ['artistName', 'trackName', 'recordName', 'genreName', 'recordLabelName', 'format'];
if (!in_array($sort, $validColumns)) {
    $sort = 'artistName';
}
$dir = strtoupper($dir) === 'DESC' ? 'DESC' : 'ASC';

// SQL query with dynamic ORDER BY clause
$query = "
    SELECT 
    a.artistId,
    a.artistName,
    t.trackName,
    r.recordName,
    g.genreName,
    rl.recordLabelName,
    rec.format,
    t.trackLink,
    t.trackInfo,
    rec.recordId
    FROM myrecords__tracks t
    LEFT JOIN myrecords__artists a ON t.artistId = a.artistId
    LEFT JOIN myrecords__records rec ON t.recordId = rec.recordId
    LEFT JOIN myrecords__genres g ON t.genreId = g.genreId
    LEFT JOIN myrecords__recordLabel rl ON rec.recordLabelId = rl.recordLabelId
    LEFT JOIN myrecords__records r ON t.recordId = r.recordId
    ORDER BY $sort $dir
";

$result = mysqli_query($db, $query);
if (!$result) {
    die("Query failed: " . mysqli_error($db));
}
?>
<div class="container py-4">
    <h1 class="mb-4">All of My Records</h1><br>
    <p class="p-bg">Click on an artist or album name for more details!</p>

    <table class="table table-striped table-hover table-responsive">
        <thead class="table-warning">
        <tr>
            <th><?= sortableColumnHeader('artistName', 'Artist', $sort, $dir) ?></th>
            <th><?= sortableColumnHeader('trackName', 'Track Name', $sort, $dir) ?></th>
            <th><?= sortableColumnHeader('recordName', 'Album Name', $sort, $dir) ?></th>
            <th><?= sortableColumnHeader('genreName', 'Genre', $sort, $dir) ?></th>
            <th><?= sortableColumnHeader('recordLabelName', 'Record Label', $sort, $dir) ?></th>
            <th><?= sortableColumnHeader('format', 'Record Type', $sort, $dir) ?></th>
        </tr>
        </thead>
        <tbody>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td>
                    <a href="artist-info.php?artistId=<?= urlencode($row['artistId']) ?>">
                        <?= htmlspecialchars($row['artistName']) ?>
                    </a>
                </td>
                <td><?= htmlspecialchars($row['trackName']) ?></td>
                <td>
                    <?php if (!empty($row['recordName'])): ?>
                        <a href="album-info.php?recordId=<?= urlencode($row['recordId']) ?>">
                            <?= htmlspecialchars($row['recordName']) ?>
                        </a>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($row['genreName']) ?></td>
                <td><?= htmlspecialchars($row['recordLabelName']) ?></td>
                <td><?= htmlspecialchars($row['format']) ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</div>
<?php
include "includes/footer.php";
?>
