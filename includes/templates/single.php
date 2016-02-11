<?php get_header(); ?>

    <?php if ( is_single() ){
        the_title( '<h1 class="entry-title">', '</h1>' );
        the_ID();
    }

    ?>

    we are in the plugin custom file

<?php get_footer(); ?>