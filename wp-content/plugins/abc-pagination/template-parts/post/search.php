<?php

if ( ! defined( 'WPINC' ) ) {
    die;
}

/**
 * @version 1.2.0
 */

/**
 * @var array $args
 */

?>

<span class="abc-pagination-letter abc-pagination-letters--search js-abc-pagination-search" data-search_id="<?php echo $_search_id = uniqid( 'abc_pagination_search_' ) ?>" data-type="<?php echo esc_attr( $args['type'] ) ?>">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32">
        <path d="m29.56 27.44-6.09-6.09C25.05 19.32 26 16.77 26 14c0-6.63-5.37-12-12-12S2 7.37 2 14s5.37 12 12 12c2.77 0 5.32-.95 7.35-2.53l6.09 6.09c.29.29.68.44 1.06.44s.77-.15 1.06-.44c.59-.59.59-1.54 0-2.12ZM5 14c0-4.96 4.04-9 9-9s9 4.04 9 9-4.04 9-9 9-9-4.04-9-9Z" fill="currentColor"/>
    </svg>
</span>

<?php add_action( 'abc_pagination/post_list/after', function () use ( $_search_id ) {
    ?>
    <template id="<?php echo $_search_id ?>">
        <div class="abc-pagination-search active">
            <input type="text" class="abc-pagination-search__input" placeholder="<?php echo __( 'Search...', 'abc-pagination' ) ?>">
            <div class="abc-pagination-tab__close js-abc-pagination-search-close">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512">
                    <path d="M440.97 407.03c9.37 9.37 9.37 24.57 0 33.94-4.69 4.69-10.83 7.03-16.97 7.03s-12.28-2.34-16.97-7.03L256 289.94 104.97 440.97C100.28 445.66 94.14 448 88 448s-12.28-2.34-16.97-7.03c-9.37-9.37-9.37-24.57 0-33.94L222.06 256 71.03 104.97c-9.37-9.37-9.37-24.57 0-33.94 9.37-9.37 24.57-9.37 33.94 0L256 222.06 407.03 71.03c9.37-9.37 24.57-9.37 33.94 0 9.37 9.37 9.37 24.57 0 33.94L289.94 256l151.03 151.03Z" fill="currentColor"></path>
                </svg>
            </div>
        </div>
    </template>
    <?php
} ); ?>
