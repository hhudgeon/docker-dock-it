<?php

// Function to generate sortable column headers
function sortableColumnHeader($col, $label, $currentSort, $currentDir, $additionalParams = '')
{
$newDir = ($col === $currentSort && strtoupper($currentDir) === 'ASC') ? 'DESC' : 'ASC';

    if ($col === $currentSort) {
        $arrow = strtoupper($currentDir) === 'ASC'
            ? ' <i class="fas fa-sort-up text-info"></i>'
            : ' <i class="fas fa-sort-down text-info"></i>';
        $linkClass = 'fw-bold text-secondary';
    } else {
        $arrow = '';
        $linkClass = '';
    }

    $link = '?sort=' . urlencode($col) . '&dir=' . $newDir;
    if (!empty($additionalParams)) {
        $link .= '&' . $additionalParams;
    }

    return '<a href="' . $link . '" class="text-decoration-none ' . $linkClass . '">' . htmlspecialchars($label) . $arrow . '</a>';
}


function isFilled($value) {
    return isset($value) && trim($value) !== '';
}

// Check if artist has valid tracks (used for artist delete only)
function hasAssociatedTracks($db, $artistId) {
    // Clean up artist-record links that point to records with no tracks
    $cleanupQuery = "
        DELETE ar FROM myrecords__artist_record ar
        LEFT JOIN myrecords__tracks t ON ar.recordId = t.recordId
        WHERE t.recordId IS NULL AND ar.artistId = ?";
    $cleanupStmt = mysqli_prepare($db, $cleanupQuery);
    mysqli_stmt_bind_param($cleanupStmt, "i", $artistId);
    mysqli_stmt_execute($cleanupStmt);
    mysqli_stmt_close($cleanupStmt);

    // Now check if artist still has valid associated tracks
    $query = "
        SELECT COUNT(*) 
        FROM myrecords__artist_record ar
        INNER JOIN myrecords__records r ON ar.recordId = r.recordId
        INNER JOIN myrecords__tracks t ON r.recordId = t.recordId
        WHERE ar.artistId = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $artistId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $count > 0;
}

// Check for associations before delete (used for label and genre only)
function hasAssociations($db, $table, $column, $id) {
    $query = "SELECT COUNT(*) FROM $table WHERE $column = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $count > 0;
}

// Check if a label is still associated with any records that have tracks
function hasAssociatedRecords($db, $labelId) {
    $query = "
        SELECT COUNT(*) 
        FROM myrecords__records r
        INNER JOIN myrecords__tracks t ON r.recordId = t.recordId
        WHERE r.recordLabelId = ?";
    $stmt = mysqli_prepare($db, $query);
    mysqli_stmt_bind_param($stmt, "i", $labelId);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $count > 0;
}
