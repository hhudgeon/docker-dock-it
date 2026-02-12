<?php

namespace HHBooksReviews;

/** @var BookMeta $meta */
$meta = BookMeta::getInstance();

// Current values
$pub_date   = $meta->getPubDate();
$isbn       = $meta->getIsbn();
$notes      = $meta->getStripNotes();
$publisher  = $meta->getPublisher();
$start      = $meta->getStripStart();
$end        = $meta->getStripEnd();
$price      = $meta->getPrice();
$format     = $meta->getFormat();

// Nonce
wp_nonce_field(BookMeta::NONCE_ACTION, BookMeta::NONCE);
?>

<p>
    <label for="hh_book_pub_date"><strong>Publication Date</strong></label><br>
    <input type="date"
           id="hh_book_pub_date"
           name="<?= BookMeta::PUB_DATE ?>"
           value="<?= esc_attr($pub_date) ?>">
</p>

<p>
    <label for="hh_book_isbn"><strong>ISBN</strong></label><br>
    <input type="text"
           id="hh_book_isbn"
           name="<?= BookMeta::ISBN ?>"
           value="<?= esc_attr($isbn) ?>"
           placeholder="978-1449433253">
</p>

<p>
    <label for="hh_book_format"><strong>Format</strong></label><br>
    <input type="text"
           id="hh_book_format"
           name="<?= BookMeta::FORMAT ?>"
           value="<?= esc_attr($format) ?>"
           placeholder="B/W, Color Sundays, Mixed">
</p>

<p>
    <label for="hh_book_publisher"><strong>Publisher</strong></label><br>
    <input type="text"
           id="hh_book_publisher"
           name="<?= BookMeta::PUBLISHER ?>"
           value="<?= esc_attr($publisher) ?>"
           placeholder="Andrews McMeel">
</p>

<p>
    <label for="hh_book_strip_start"><strong>Strip Range — Start</strong></label><br>
    <input type="date"
           id="hh_book_strip_start"
           name="<?= BookMeta::STRIP_START ?>"
           value="<?= esc_attr($start) ?>">
</p>

<p>
    <label for="hh_book_strip_end"><strong>Strip Range — End</strong></label><br>
    <input type="date"
           id="hh_book_strip_end"
           name="<?= BookMeta::STRIP_END ?>"
           value="<?= esc_attr($end) ?>">
</p>

<p>
    <label for="hh_book_price"><strong>Price</strong></label><br>
    <input type="number"
           id="hh_book_price"
           name="<?= BookMeta::PRICE ?>"
           value="<?= esc_attr($price) ?>"
           step="0.01" min="0" placeholder="14.99">
</p>

<p>
    <label for="hh_book_strip_notes"><strong>Strip Notes</strong></label><br>
    <textarea id="hh_book_strip_notes"
              name="<?= BookMeta::STRIP_NOTES ?>"
              rows="4"><?= esc_textarea($notes) ?></textarea>
</p>
