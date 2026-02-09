<?php
include "includes/header.php";
$pageTitle = "Manage 45 Records";

if (!isset($_SESSION['authUser'])) {
    header("Location: music-login.php");
    exit();
}

// Handle delete
if (isset($_POST['deleteRecordId'])) {
    $recordId = (int) $_POST['deleteRecordId'];
    if ($recordId > 0) {
        mysqli_query($db, "DELETE FROM myrecords__records WHERE recordId = $recordId");
        mysqli_query($db, "DELETE FROM myrecords__tracks WHERE recordId = $recordId");
        $success = "45 record and its tracks deleted successfully.";
    }
}

$query = "
    SELECT r.recordId, t.trackName, a.artistName, t.trackId
    FROM myrecords__records r
    JOIN myrecords__tracks t ON r.recordId = t.recordId
    JOIN myrecords__artists a ON t.artistId = a.artistId
    WHERE r.format = '45'
    ORDER BY r.recordId, t.trackId
";

$result = mysqli_query($db, $query);
$records = [];
while ($row = mysqli_fetch_assoc($result)) {
    $records[$row['recordId']][] = $row;
}
?>

    <div class="container py-4">
        <h1 class="mb-4">Manage 45 Records</h1>

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
                <th>Track Name (Side)</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($records as $recordId => $tracks): ?>
                <?php foreach ($tracks as $index => $row): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['artistName']) ?></td>
                        <td>
                            <?= htmlspecialchars($row['trackName']) ?>
                            <?= $index === 0 ? '(Side A)' : '(Side B)' ?>
                        </td>
                        <?php if ($index === 0): ?>
                            <td rowspan="<?= count($tracks) ?>" class="align-middle">
                                <form method="post" class="d-flex gap-2">
                                    <input type="hidden" name="deleteRecordId" value="<?= $recordId ?>">
                                    <a href="edit-45-record.php?recordId=<?= $recordId ?>" class="btn btn-sm btn-primary">Edit</a>
                                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this 45 record and its tracks?')">Delete</button>
                                </form>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>

<?php include "includes/footer.php"; ?>