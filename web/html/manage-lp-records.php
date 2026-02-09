<?php
include "includes/header.php";
$pageTitle = "Manage LP Records";

require_once "includes/database.php";

// Handle delete
if (isset($_POST['deleteRecordId'])) {
    $recordId = (int) $_POST['deleteRecordId'];
    if ($recordId > 0) {
        mysqli_query($db, "DELETE FROM myrecords__records WHERE recordId = $recordId");
        mysqli_query($db, "DELETE FROM myrecords__tracks WHERE recordId = $recordId");
        mysqli_query($db, "DELETE FROM myrecords__artist_record WHERE recordId = $recordId");
        $success = "LP record and its tracks deleted successfully.";
    }
}

$query = "
    SELECT r.recordId, r.recordName, a.artistName, t.trackName, t.trackId
    FROM myrecords__records r
    JOIN myrecords__artist_record ar ON r.recordId = ar.recordId
    JOIN myrecords__artists a ON ar.artistId = a.artistId
    LEFT JOIN myrecords__tracks t ON r.recordId = t.recordId
    WHERE r.format = 'LP'
    ORDER BY r.recordId, t.trackId
";

$result = mysqli_query($db, $query);
$records = [];
while ($row = mysqli_fetch_assoc($result)) {
    $records[$row['recordId']]['info'] = [
        'recordName' => $row['recordName'],
        'artistName' => $row['artistName']
    ];
    if (!empty($row['trackName'])) {
        $records[$row['recordId']]['tracks'][] = $row['trackName'];
    }
}
?>

    <div class="container py-4">
        <h1 class="mb-4">Manage LP Records</h1>
        <div class="mb-3">
            <a href="manage-music.php" class="btn btn-info">
                &larr; Back to Manage Music
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <table class="table table-bordered">
            <thead class="table-light">
            <tr>
                <th>Artist Name</th>
                <th>Album Name</th>
                <th>Tracks</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $recordId => $data): ?>
                <tr>
                    <td><?= htmlspecialchars($data['info']['artistName']) ?></td>
                    <td><?= htmlspecialchars($data['info']['recordName']) ?></td>
                    <td>
                        <?php if (!empty($data['tracks'])): ?>
                            <ul class="mb-0">
                                <?php foreach ($data['tracks'] as $track): ?>
                                    <li><?= htmlspecialchars($track) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        <?php else: ?>
                            <em>No tracks</em>
                        <?php endif; ?>
                    </td>
                    <td class="align-middle">
                        <form method="post" class="d-flex gap-2">
                            <input type="hidden" name="deleteRecordId" value="<?= $recordId ?>">
                            <a href="edit-lp-record.php?recordId=<?= $recordId ?>" class="btn btn-sm btn-primary">Edit</a>
                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this LP record and its tracks?')">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php include "includes/footer.php"; ?>