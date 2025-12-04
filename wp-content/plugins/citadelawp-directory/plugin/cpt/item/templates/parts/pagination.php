<?php
    $current_page = max(1, get_query_var('paged'));
?>
<div class="citadela-block-pagination-wrap">
    <div class="citadela-block-pagination">
        <?php
        echo paginate_links(array(
            'base'      => get_pagenum_link(1) . '%_%',
            'format'    => 'page/%#%/',
            'current'   => $current_page,
            'total'     => $total_pages,
            'prev_text' => __('« Previous'),
            'next_text' => __('Next »'),
        ))
        ?>
    </div>
</div>
