<?php
/**
 * The template for displaying Archive pages.
 */

get_header(); ?>

<div id="cm-content-wrapper" class="alignwide">
    <div class="cm-content">
		<?php
		if ( have_posts() ) : while ( have_posts() ) : the_post();
			$post_meta = get_post_meta( get_the_ID() );
			$is_single = false;
			require( 'deceased.php' );
		endwhile;
		endif;
		?>
    </div>
</div>

<?php get_footer(); ?>
