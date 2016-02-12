<?php get_header(); ?>

    <?php if ( is_single() ){
        the_title( '<h1 class="entry-title">', '</h1>' );
    if ( has_post_thumbnail() ) {
        the_post_thumbnail();
    }

    $required_fields = get_option('cm_fields');
    $fields = get_post_meta(get_the_ID());

    foreach ($required_fields as $required){
        echo '<p>';
        echo $required. ': ';
        $required = strtolower($required);
        $required = preg_replace('/\s+/', '', $required);
        echo current($fields[$required]);
       echo '</p>';
    }

    }

    ?>

<?php get_footer(); ?>