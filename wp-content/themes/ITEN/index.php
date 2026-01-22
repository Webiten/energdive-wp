<?php
get_header();
?>

<main id="primary" class="site-main">
    <?php
    if ( have_posts() ) :
        while ( have_posts() ) :
            the_post();
            the_content(); // 🔥 THIS IS MUST FOR ELEMENTOR
        endwhile;
    endif;
    ?>
</main>

<?php
get_footer();
