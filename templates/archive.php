<?php
/**
 * The template for displaying Archive pages.
 */

use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\settings\Admin_Options_Page;

get_header(); ?>

<div id="cm-content-wrapper" class="alignwide">
	<div class="cm-content">

		<?php
		$show_search_in_archive = Admin_Options_Page::get_current_or_default_option(
			'cm_option_settings_show_search_in_archive'
		);

		if ( $show_search_in_archive ) {
			echo do_shortcode( '[cm_search]' );
		}
		?>

		<?php
		global $paged;
		global $wp_query;

		$posts_per_page = get_option( 'posts_per_page' );
		$post_query     = new WP_Query(
			array(
				'showposts' => $posts_per_page,
				'post_type' => Custom_Post_Type::post_type(),
				'paged'     => $paged,
			)
		);

		$total_found_posts = $post_query->found_posts;
		$total_page        = ceil( $total_found_posts / $posts_per_page );

		$big = 999999999; // need an unlikely integer
		$pagination = paginate_links(
			array(
				'base'      => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),
				'format'    => '?paged=%#%',
				'current'   => max( 1, $paged ),
				'total'     => $total_page,
				'next_text' => false,
				'prev_text' => false,
			)
		);
		?>
		<nav>
			<div>
				<?php previous_posts_link( __( 'Previous page', 'cm_translate' ) ); ?>
			</div>
			<div>
				<?php echo $pagination; ?>
			</div>
			<div>
				<?php next_posts_link( __( 'Next page', 'cm_translate' ) ); ?>
			</div>
		</nav>
		<?php
		if ( $post_query->have_posts() ) :
			while ( $post_query->have_posts() ) :
				$post_query->the_post();
				$post_meta = get_post_meta( get_the_ID() );
				$is_single = false;
				require 'deceased.php';
			endwhile;
		else :
			echo esc_html__( 'No results found.' );
		endif;
		?>
		<nav>
			<div>
				<?php previous_posts_link( __( 'Previous page', 'cm_translate' ) ); ?>
			</div>
			<div>
				<?php echo $pagination; ?>
			</div>
			<div>
				<?php next_posts_link( __( 'Next page', 'cm_translate' ) ); ?>
			</div>
		</nav>
	</div>
</div>
<?php wp_reset_postdata(); ?>
<?php get_footer(); ?>
