<?php
/* @var $cm_search_action string */
/* @var $cm_search_label_text string */
/* @var $cm_search_placeholder_text string */
/* @var $cm_search_button_text string */
/* @var $cm_search_value string */

?>

<form class="cm_search_form" action="<?php echo esc_attr( $cm_search_action ); ?>">
	<div>
		<?php if ( ! empty( $cm_search_value ) ) : ?>
			<div class="cm_search_clear">
				<a href="<?php echo $cm_search_action; ?>">
					<?php echo esc_html__( 'Clear' ); ?>
				</a>
			</div>
		<?php endif; ?>
		<input
			id="cm_search"
			type="search"
			name="q"
			placeholder="<?php echo esc_attr( $cm_search_placeholder_text ); ?>"
			value="<?php echo esc_attr( $cm_search_value ); ?>"
		/>
	</div>
	<button><?php echo esc_html( $cm_search_button_text ); ?></button>
</form>
