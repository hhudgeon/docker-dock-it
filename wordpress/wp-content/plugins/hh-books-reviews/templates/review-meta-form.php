<?php
namespace HHBooksReviews;

$meta = ReviewMeta::getInstance();

// Current values
$name = $meta->getName();
$loc  = $meta->getLocation();
$rate = (int) $meta->getRating();
$book = (int) $meta->getBookId();

// Pull Books for dropdown
$books = get_posts([
    'post_type'      => 'hh-book',
    'numberposts'    => -1,
    'orderby'        => 'title',
    'order'          => 'ASC',
    'suppress_filters' => false,
]);
?>

<?php wp_nonce_field('hh_review_meta_save', 'hh_review_meta_nonce'); ?>

<p>
    <label for="hh_review_name"><strong>Name (reviewer)</strong></label><br>
    <input type="text" id="hh_review_name"
           name="<?= ReviewMeta::REVIEWER_NAME ?>"
           value="<?= esc_attr($name) ?>"
           style="width:100%;">
</p>

<p>
    <label for="hh_review_location"><strong>Location (city, state)</strong></label><br>
    <input type="text" id="hh_review_location"
           name="<?= ReviewMeta::REVIEWER_LOCATION ?>"
           value="<?= esc_attr($loc) ?>"
           style="width:100%;"
           placeholder="e.g., Madison, WI">
</p>

<p>
    <label for="hh_review_rating"><strong>Rating</strong></label><br>
    <select name="<?= ReviewMeta::REVIEW_RATING ?>" id="hh_review_rating" style="width:100%;">
        <?php for ($i = 1; $i <= 5; $i++): ?>
            <option value="<?= $i ?>" <?= selected($rate, $i, false) ?>><?= $i ?></option>
        <?php endfor; ?>
    </select>
</p>

<p>
    <label for="hh_review_book"><strong>Book</strong></label><br>
    <select name="<?= ReviewMeta::REVIEW_BOOK_ID ?>" id="hh_review_book" style="width:100%;">
        <option value="0">— Select —</option>
        <?php foreach ($books as $b): ?>
            <option value="<?= (int) $b->ID ?>" <?= selected($book, $b->ID, false) ?>>
                <?= esc_html(get_the_title($b)) ?>
            </option>
        <?php endforeach; ?>
    </select>
</p>
