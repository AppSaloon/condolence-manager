<?php

namespace appsaloon\cm\form;

use appsaloon\cm\coffee_table\Coffee_Table_Controller;
use appsaloon\cm\model\Order;
use appsaloon\cm\register\Custom_Post_Type;
use appsaloon\cm\register\Location_Type;
use appsaloon\cm\register\Order_Type;
use WP_Query;

class Metabox {

	public function __construct() {
		add_action( 'add_meta_boxes', array( $this, 'add_metaboxes' ) );
		add_action( 'save_post', array( $this, 'save_condolence_person' ), 1, 2 );
		add_action( 'admin_enqueue_scripts', array( $this, 'metabox_css_jquery' ) );
		//add_action( 'publish_'.Custom_Post_Type::POST_TYPE, array($this, 'send_mail_to_family') );
	}

	/**
	 * Add metabox information about departed soul and metabox about information for the family
	 */
	public function add_metaboxes() {
		add_meta_box(
			'wpt_condolence_person_location',
			__( 'Information about deceased', 'cm_translate' ),
			array(
				$this,
				'deceased_callback',
			),
			Custom_Post_Type::post_type(),
			'normal',
			'high'
		);
		add_meta_box(
			'wpt_condolence_person_location_linked_location',
			__( 'Location', 'cm_translate' ),
			array(
				$this,
				'location_metabox',
			),
			Custom_Post_Type::post_type(),
			'side',
			'default'
		);
		add_meta_box(
			'wpt_condolence_person_location_side',
			__( 'View comments', 'cm_translate' ),
			array(
				$this,
				'password_callback',
			),
			Custom_Post_Type::post_type(),
			'side',
			'default'
		);
		add_meta_box(
			'wpt_condolence_person_location_orders',
			__( 'Orders', 'cm_translate' ),
			array(
				$this,
				'order_metabox',
			),
			Custom_Post_Type::post_type(),
			'normal',
			'default'
		);
		add_meta_box(
			'wpt_condolence_person_location_side_down',
			__( 'Coffee table', 'cm_translate' ),
			array(
				$this,
				'coffee_table_metabox',
			),
			Custom_Post_Type::post_type(),
			'side',
			'default'
		);
	}

	/**
	 * Define fields in metabox Information about departed soul
	 */
	public function deceased_callback() {
		global $post;

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'deceased_noncename' );

		$gender                   = $this->get_field_value( 'gender', $post->ID );
		$name                     = $this->get_field_value( 'name', $post->ID );
		$family_name              = $this->get_field_value( 'familyname', $post->ID );
		$honorary_title           = $this->get_field_value( 'honoraryitle', $post->ID );
		$place_of_birth           = $this->get_field_value( 'birthplace', $post->ID );
		$date_of_birth            = self::normalize_date( $this->get_field_value( 'birthdate', $post->ID ) );
		$place_of_death           = $this->get_field_value( 'placeofdeath', $post->ID );
		$date_of_death            = self::normalize_date( $this->get_field_value( 'dateofdeath', $post->ID ) );
		$date_of_funeral          = self::normalize_date( $this->get_field_value( 'funeraldate', $post->ID ) );
		$show_funeral_date        = $this->get_field_value( 'show_funeraldate', $post->ID );
		$funeral_information      = $this->get_field_value( 'funeralinformation', $post->ID );
		$prayer_vigil_information = $this->get_field_value( 'prayervigilinformation', $post->ID );
		$greeting_information     = $this->get_field_value( 'greetinginformation', $post->ID );
		$residence                = $this->get_field_value( 'residence', $post->ID );
		$mass_card                = $this->get_field_value( 'masscard', $post->ID );
		$prayer_card              = $this->get_field_value( 'prayer_card', $post->ID );
		$flowers                  = $this->get_field_value( 'flowers', $post->ID );
		$live_stream              = $this->get_field_value( 'live_stream', $post->ID );
		$live_stream_embed        = $this->get_field_value( 'live_stream_embed', $post->ID );
		$live_stream_url          = $this->get_field_value( 'live_stream_url', $post->ID );
		$live_stream_description  = $this->get_field_value( 'live_stream_description', $post->ID );
		$relations                = get_post_meta( $post->ID, 'relations', false );
		?>

		<table class="form-table">
			<tr>
				<td><?php echo esc_html__( 'Gender', 'cm_translate' ); ?></td>
				<td class="form-field">
					<select name="gender">
						<option><?php echo esc_html__( 'Select gender', 'cm_translate' ); ?></option>
						<option
							value="Male"
							<?php echo ( $gender === 'Male' ) ? 'selected' : ''; ?>
						>
							<?php echo esc_html__( 'Male', 'cm_translate' ); ?>
						</option>
						<option
							value="Female"
							<?php echo ( $gender === 'Female' ) ? 'selected' : ''; ?>
						>
							<?php echo esc_html__( 'Female', 'cm_translate' ); ?>
						</option>
					</select>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Name', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						name="name"
						value="<?php echo esc_attr( $name ); ?>"
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Family name', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						name="familyname"
						value="<?php echo esc_attr( $family_name ); ?>"
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Honorary title', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						name="honoraryitle"
						value="<?php echo esc_attr( $honorary_title ); ?>"
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Birthplace', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						name="birthplace"
						value="<?php echo esc_attr( $place_of_birth ); ?>"
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Birthdate', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						class="jquery-datepicker"
						name="birthdate"
						value="<?php echo esc_attr( $date_of_birth ); ?>"
						readonly
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Place of death', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						name="placeofdeath"
						value="<?php echo esc_attr( $place_of_death ); ?>"
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Date of death', 'cm_translate' ); ?></td>
				<td class="form-field">
					<input
						type="text"
						class="jquery-datepicker"
						name="dateofdeath"
						value="<?php echo esc_attr( $date_of_death ); ?>"
						readonly
					/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="funeraldate">
						<?php echo esc_html__( 'Funeral date', 'cm_translate' ); ?>
					</label>
				</td>
				<td class="form-field">
					<input
						type="text"
						class="jquery-datepicker"
						name="funeraldate"
						id="funeraldate"
						value="<?php echo esc_attr( $date_of_funeral ); ?>"
						readonly
					/>
				</td>
			</tr>
			<tr>
				<td>
					<label for="show_funeraldate">
						<?php echo esc_html__( 'Show funeral date', 'cm_translate' ); ?>
					</label>
				</td>
				<td class="form-field">
					<input
						type="checkbox"
						name="show_funeraldate"
						id="show_funeraldate"
						<?php echo ( $show_funeral_date !== '0' ) ? 'checked' : ''; ?>
					/>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Funeral information', 'cm_translate' ); ?></td>
				<td class="form-field">
					<textarea
						name="funeralinformation"
						rows="3"
					><?php echo esc_html( $funeral_information ); ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Prayer Vigil information', 'cm_translate' ); ?></td>
				<td class="form-field">
					<textarea
						name="prayervigilinformation"
						rows="3"
					><?php echo esc_html( $prayer_vigil_information ); ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Greeting information', 'cm_translate' ); ?></td>
				<td class="form-field">
					<textarea
						name="greetinginformation"
						rows="3"
					><?php echo esc_html( $greeting_information ); ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Residence', 'cm_translate' ); ?></td>
				<td class="form-field">
					<textarea
						rows="3"
						name="residence"
					><?php echo esc_html( $residence ); ?></textarea>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Mass card', 'cm_translate' ); ?></td>
				<td class="form-field">
					<div class="cm_file_picker">
						<input
							type="text"
							name="masscard"
							value="<?php echo esc_html( $mass_card ); ?>"
						>
						<input
							class="button"
							type="button"
							value="<?php echo esc_attr__( 'Choose mass card', 'cm_translate' ); ?>"
						/>
					</div>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Prayer card', 'cm_translate' ); ?></td>
				<td class="form-field">
					<div class="cm_file_picker">
						<input
							type="text"
							name="prayer_card"
							value="<?php echo esc_html( $prayer_card ); ?>"
						>
						<input
							class="button"
							type="button"
							value="<?php echo esc_attr__( 'Select a prayer card', 'cm_translate' ); ?>"
						/>
					</div>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Flowers', 'cm_translate' ); ?></td>
				<td class="form-field">
					<p class="description">
						<label>
							<input
								id="flowers"
								type="checkbox"
								name="flowers"
								value="1"
								<?php echo ( $flowers === '0' ) ? '' : 'checked'; ?>
							>
							<?php
							printf(
							/* translators: %1$s products shortcode */
							/* translators: %2$s order form shortcode */
								esc_html__( 'If enabled, a list of products and an order form will appear below a condolence page. Note, if you use a custom template file, you\'ll need to add these shortcodes: %1$s and %2$s.', 'cm_translate' ),
								'[cm_products]',
								'[cm_order_form]'
							);
							?>
						</label>
					</p>
				</td>
			</tr>
			<tr>
				<td><?php echo esc_html__( 'Live-stream', 'cm_translate' ); ?></td>
				<td class="form-field">
					<p class="description">
						<label>
							<input
								id="live_stream"
								type="checkbox"
								name="live_stream"
								value="1"
								<?php echo ( $live_stream === '1' ) ? 'checked' : ''; ?>
							>
							<?php echo esc_html__( 'If enabled, a live-stream button for the funeral will appear. Please enter a link and a description for the livestream below.', 'cm_translate' ); ?>
						</label>
					</p>
					<p class="description">
						<label>
							<input
								id="live_stream_embed"
								name="live_stream_embed"
								type="checkbox"
								value="1"
								<?php echo ( $live_stream_embed === '1' ) ? 'checked' : ''; ?>
							>
							<?php echo esc_html__( 'If enabled, the livestream video will be embedded within the page.', 'cm_translate' ); ?>
						</label>
					</p>
					<p>
						<input
							type="text"
							name="live_stream_url"
							placeholder="https://www.example.com/live-stream"
							value="<?php echo esc_attr( $live_stream_url ); ?>"
						>
					</p>
					<p>
						<input
							type="text"
							name="live_stream_description"
							placeholder="<?php echo esc_attr__( 'Example: the live-stream will start at 08:00', 'cm_translate' ); ?>"
							value="<?php echo esc_attr( $live_stream_description ); ?>"
						>
					</p>
				</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="2">
					<b><?php echo esc_html__( 'Relations', 'cm_translate' ); ?></b>
					<hr>
				</td>
			</tr>
			<?php if ( ! $relations ) : ?>
				<tr>
					<td><?php echo esc_html__( 'Relation 1', 'cm_translate' ); ?></td>
					<td class="form-field">
						<select name="relation_id1" id="relation_id1">
							<option><?php echo esc_html__( 'Select relation type', 'cm_translate' ); ?></option>
							<option value="Single"><?php echo esc_html__( 'Single', 'cm_translate' ); ?></option>
							<option value="Married"><?php echo esc_html__( 'Married', 'cm_translate' ); ?></option>
							<option value="Other"><?php echo esc_html__( 'Other', 'cm_translate' ); ?></option>
						</select>
						<button class="add_more">
							<span
								class="dashicons dashicons-plus-alt"
								title="<?php echo esc_attr__( 'Add more relation', 'cm_translate' ); ?>"
							></span>
						</button>
						<div class="relation1_more_information">
							<input
								type="text"
								class="relation1_other"
								name="relation1_other"
								placeholder="<?php echo esc_attr__( 'Relation name', 'cm_translate' ); ?>"
							>
							<input
								type="text"
								name="relation1_name"
								placeholder="<?php echo esc_attr__( 'Name', 'cm_translate' ); ?>"
							>
							<input
								type="text"
								name="relation1_familyname"
								placeholder="<?php echo esc_attr__( 'Family name', 'cm_translate' ); ?>"
							>
							<?php echo esc_html__( 'Is alive?', 'cm_translate' ); ?>
							<input type="checkbox" name="relation1_alive">
							<select name="relation1_gender">
								<option>Select gender</option>
								<option value="Male">
									<?php echo esc_html__( 'Male', 'cm_translate' ); ?>
								</option>
								<option value="Female">
									<?php echo esc_html__( 'Female', 'cm_translate' ); ?>
								</option>
							</select>
						</div>
					</td>
				</tr>
			<?php else : ?>
				<?php
				$count     = 1;
				$relations = current( $relations );
				?>
				<?php foreach ( $relations as $relation ) : ?>
					<tr>
						<td><?php echo esc_html__( 'Relation', 'cm_translate' ) . esc_html( '&nbsp;' . $count ); ?></td>
						<td class="form-field">
							<select
								name="relation_id<?php echo esc_attr( $count ); ?>"
								id="relation_id<?php echo esc_attr( $count ); ?>"
							>
								<option><?php echo esc_html__( 'Select relation type', 'cm_translate' ); ?></option>
								<option
									value="Single"
									<?php echo ( $relation['type'] === 'Single' ) ? 'selected' : ''; ?>
								>
									<?php echo esc_html__( 'Single', 'cm_translate' ); ?>
								</option>
								<option
									value="Married"
									<?php echo ( $relation['type'] === 'Married' ) ? 'selected' : ''; ?>
								>
									<?php echo esc_html__( 'Married', 'cm_translate' ); ?>
								</option>
								<option
									value="Other"
									<?php echo ( $relation['type'] === 'Other' ) ? 'selected' : ''; ?>
								>
									<?php echo esc_html__( 'Other', 'cm_translate' ); ?>
								</option>
							</select>
							<?php if ( $count === 1 ) : ?>
								<button class="add_more">
									<span
										class="dashicons dashicons-plus-alt"
										title="<?php echo esc_attr__( 'Add more relation', 'cm_translate' ); ?>"
									></span>
								</button>
							<?php else : ?>
								<span
									class="dashicons dashicons-dismiss"
									title="<?php echo esc_attr__( 'Remove relation', 'cm_translate' ); ?>"
								></span>
							<?php endif; ?>

							<div
								class="relation<?php echo esc_attr( $count ); ?>_more_information"
								<?php echo ( $relation['type'] === 'Single' ) ? 'style="display:none;"' : ''; ?>>
								<input
									type="text"
									class="relation<?php echo esc_attr( $count ); ?>_other"
									name="relation<?php echo esc_attr( $count ); ?>_other"
									placeholder="<?php echo esc_attr__( 'Relation name', 'cm_translate' ); ?>"
									value="<?php echo esc_attr( $relation['other'] ); ?>"
									<?php echo ( $relation['type'] === 'Married' ) ? 'style="display:none;"' : ''; ?>
								>
								<input
									type="text"
									name="relation<?php echo esc_attr( $count ); ?>_name"
									placeholder="<?php echo esc_attr__( 'Name', 'cm_translate' ); ?>"
									value="<?php echo esc_attr( $relation['name'] ); ?>"
								>
								<input
									type="text"
									name="relation<?php echo esc_attr( $count ); ?>_familyname"
									placeholder="<?php echo esc_attr__( 'Family name', 'cm_translate' ); ?>"
									value="<?php echo esc_attr( $relation['familyname'] ); ?>"
								>
								<?php echo esc_html__( 'Is alive?', 'cm_translate' ); ?>
								<input
									type="checkbox"
									name="relation<?php echo esc_attr( $count ); ?>_alive"
									<?php echo ( $relation['alive'] === '1' ) ? 'checked' : ''; ?>
								>
								<select name="relation<?php echo esc_attr( $count ); ?>_gender">
									<option>
										<?php echo esc_html__( 'Select gender', 'cm_translate' ); ?>
									</option>
									<option
										value="Male"
										<?php echo ( $relation['gender'] === 'Male' ) ? 'selected' : ''; ?>
									>
										<?php echo esc_html__( 'Male', 'cm_translate' ); ?></option>
									<option
										value="Female"
										<?php echo ( $relation['gender'] === 'Female' ) ? 'selected' : ''; ?>
									>
										<?php echo esc_html__( 'Female', 'cm_translate' ); ?></option>
								</select>
							</div>
						</td>
					</tr>
					<?php
					$count ++;
				endforeach;
			endif;
			/**
			 *  action hook to render metabox on backend
			 */
			do_action( 'cm_render_metabox' );
			?>

		</table>

		<script>
		  jQuery(document).ready(function ($) {

			$(document).on('click', '.add_more', function (e) {
			  e.preventDefault()
			  var nummer = $('.form-table tr:last-child select').attr('id').replace(/[^\d.]/g, '')
			  nummer = parseInt(nummer) + 1

			  $('.form-table').find('tbody')
				.append($('<tr>')
				  .append($('<td>')
					.append($('<label>')
					  .text('<?php _e( 'Relation', 'cm_translate' ); ?> ' + nummer)
					)
				  )
				  .append($('<td>')
					.attr('class', 'form-field')

					.append($('<select>')
					  .attr('name', 'relation_id' + nummer)
					  .attr('id', 'relation_id' + nummer)
					  .append($('<option>')
						.attr('value', '')
						.html('<?php _e( 'Select relation type', 'cm_translate' ); ?>')
					  )
					  .append($('<option>')
						.attr('value', 'Single')
						.html('<?php _e( 'Single', 'cm_translate' ); ?>')
					  )
					  .append($('<option>')
						.attr('value', 'Married')
						.html('<?php _e( 'Married', 'cm_translate' ); ?>')
					  )
					  .append($('<option>')
						.attr('value', 'Other')
						.html('<?php _e( 'Other', 'cm_translate' ); ?>')
					  )
					)
					.append($('<span>')
					  .attr('class', 'dashicons dashicons-dismiss')
					  .attr('title', '<?php _e( 'Remove relation', 'cm_translate' ); ?>')
					)

					.append($('<div>')
					  .attr('class', 'relation' + nummer + '_more_information')

					  .append($('<input>')
						  .attr('type', 'text')
						  .attr('class', 'relation' + nummer + '_other')
						  .attr('name', 'relation' + nummer + '_other')
						  .attr('placeholder', '<?php _e( 'Relation name', 'cm_translate' ); ?>')
						//.attr('style', 'display:none')
					  )
					  .append($('<input>')
						  .attr('type', 'text')
						  .attr('class', 'relation' + nummer + '_name')
						  .attr('name', 'relation' + nummer + '_name')
						  .attr('placeholder', '<?php _e( 'Name', 'cm_translate' ); ?>')
						//.attr('style', 'display:none')
					  )
					  .append($('<input>')
						  .attr('type', 'text')
						  .attr('class', 'relation' + nummer + '_familyname')
						  .attr('name', 'relation' + nummer + '_familyname')
						  .attr('placeholder', '<?php _e( 'Family name', 'cm_translate' ); ?>')
						//.attr('style', 'display:none')
					  )
					  .append('<?php _e( 'Is alive?', 'cm_translate' ); ?> ')
					  .append($('<input>')
						.attr('type', 'checkbox')
						.attr('class', 'relation' + nummer + '_alive')
						.attr('name', 'relation' + nummer + '_alive')
					  )
					  .append($('<select>')
						.attr('name', 'relation' + nummer + '_gender')
						.append($('<option>')
						  .attr('value', '')
						  .html('<?php _e( 'Select a gender', 'cm_translate' ); ?>')
						)

						.append($('<option>')
						  .attr('value', 'Male')
						  .html('<?php _e( 'Male', 'cm_translate' ); ?>')
						)
						.append($('<option>')
						  .attr('value', 'Female')
						  .html('<?php _e( 'Female', 'cm_translate' ); ?>')
						)
					  )
					)
				  )
				)
			})

			$(document).on('click', '.dashicons-dismiss', function (e) {
			  e.preventDefault()
			  if (confirm('<?php _e( 'Are you sure to delete this relation?', 'cm_translate' ); ?>')) {
				$(this).parent().parent().remove()
			  }

			})

			$(document).on('load', 'select[id^="relation_id"]', function (e) {
			  e.preventDefault()
			  var select_value = $(this).val()
			  var nummer = parseInt($(this).attr('id').replace(/[^\d.]/g, ''))

			  switch (select_value) {
				case 'Single':
				  $('.relation' + nummer + '_more_information').hide()
				  break
				case 'Married':
				  $('.relation' + nummer + '_more_information').show()
				  $('.relation' + nummer + '_other').hide()
				  break
				case 'Other':
				  $('.relation' + nummer + '_more_information').show()
				  $('.relation' + nummer + '_other').show()
				  break
				default:
				  $('.relation' + nummer + '_more_information').hide()
				  break
			  }
			})

			$(document).on('change', 'select[id^="relation_id"]', function (e) {
			  e.preventDefault()
			  var select_value = $(this).val()
			  var nummer = parseInt($(this).attr('id').replace(/[^\d.]/g, ''))

			  switch (select_value) {
				case 'Single':
				  $('.relation' + nummer + '_more_information').hide()
				  break
				case 'Married':
				  $('.relation' + nummer + '_more_information').show()
				  $('.relation' + nummer + '_other').hide()
				  break
				case 'Other':
				  $('.relation' + nummer + '_more_information').show()
				  $('.relation' + nummer + '_other').show()
				  break
				default:
				  $('.relation' + nummer + '_more_information').hide()
				  break
			  }
			})
		  })
		  <?php
			/**
			 * action hook to add js
			 */
			do_action( 'cm_backend_js' );
			?>
		</script>

		<?php
	}

	/**
	 * Checks meta value
	 *
	 * @param $field_name string //meta key
	 * @param $post_id int    //post id
	 *
	 * @return mixed|string returns field value or empty string
	 */
	public function get_field_value( $field_name, $post_id ) {
		$meta_value = get_post_meta( $post_id, $field_name, false );

		$result = '';
		if ( $meta_value ) {
			$result = current( $meta_value );
		}

		//        error_log(var_export([$field_name, $result],1));
		return $result;
	}

	/**
	 * Define fields in metabox View comments
	 */
	public function password_callback() {
		global $post;

		?>
		<label><?php echo esc_html__( 'Password', 'cm_translate' ); ?> <a id="generate"
																		  href=""><?php echo esc_html__( 'Create token', 'cm_translate' ); ?></a></label>
		<input id="password" type="text" name="password"
			   value="<?php echo $this->get_field_value( 'password', $post->ID ); ?>">

		<label><?php echo esc_html__( 'View comments', 'cm_translate' ); ?></label>
		<?php
		$permalink = get_post_permalink( $post->ID );

		// keep last slash
		//        if( substr($permalink, -1) == '/' ){
		//            $permalink = substr($permalink, 0, strlen( $permalink ) - 1 );
		//        }

		$password = $this->get_field_value( 'password', $post->ID );
		if ( $password ) {
			$permalink = $this->create_passworded_url( $permalink, $password );
		}
		?>
		<input type="text" readonly value="<?php echo $permalink; ?>">
		<a href="<?php echo $permalink; ?>"
		   target="_blank"><?php echo esc_html__( 'Link to view comments', 'cm_translate' ); ?></a>
		<br><br>
		<input type="checkbox" name="check_email" value="check_email"
			   style="width: 15px;"
		<?php
		if ( $this->get_field_value( 'check_email', $post->ID ) === 'check_email' ) {
			echo 'checked';
		}
		?>
		><?php echo esc_html__( 'Send email to family when someone condoles', 'cm_translate' ); ?>
		<label><?php echo esc_html__( 'E-mail', 'cm_translate' ); ?></label>
		<input autocomplete="new-password" type="text" name="email"
			   value="<?php echo $this->get_field_value( 'email', $post->ID ); ?>">
		<?php
	}

	/**
	 * Create URL with password to view comments
	 *
	 * @param $permalink
	 * @param $password
	 *
	 * @return string
	 */
	protected function create_passworded_url( $permalink, $password ) {
		return ( strpos( $permalink, '?' ) !== false ) ? $permalink . '&code=' . $password : $permalink . '?code=' . $password;
	}

	/**
	 * form to download coffee table list
	 * show number of
	 */
	public function coffee_table_metabox( $post ) {
		$controller       = new Coffee_Table_Controller();
		$all_participants = $controller->all_participants_by_id( $post->ID );
		$has_participants = count( $all_participants ) > 0;
		$sum_participants = $controller->get_sum_of_otherparticipants( $post->ID );
		$sum_emails       = $controller->get_sum_of_posts( $post->ID );
		?>
		<ul>
			<li><?php echo esc_html__( 'Coffee table', 'cm_translate' ); ?></li>
			<li><select id="coffee_table" name="coffee_table">
					<option value="no"><?php echo esc_html__( 'No', 'cm_translate' ); ?></option>
					<option value="yes"
						<?php
						if ( $this->get_field_value( 'coffee_table', $post->ID ) == 'yes' ) {
							echo 'selected';
						}
						?>
					><?php echo esc_html__( 'Yes', 'cm_translate' ); ?></option>
				</select></li>
			<span
				id="span_coffee_table_email"
				<?php
				if ( $this->get_field_value( 'coffee_table', $post->ID ) != 'yes' ) {
					echo 'hidden';
				}
				?>
			>
				<li><label for="coffee_table_email"><?php echo esc_html__( 'Email address', 'cm_translate' ); ?></label>
				</li>
				<li><input type="text" name="coffee_table_email" id="coffee_table_email"
						<?php
						if ( $this->get_field_value( 'coffee_table', $post->ID ) ) {
							echo " value= '" . $this->get_field_value( 'coffee_table_email', $post->ID ) . "'";
						}
						?>
					></li>
				<?php
				echo is_numeric( $sum_emails ) ? '<li><p>' . __( 'Emails: ', 'cm_translate' ) . $sum_emails . '</p></li>' : false;
				echo '<li><p>' . __( 'Participants: ', 'cm_translate' ) . $sum_participants . '</p></li>';
				if ( $has_participants ) {
					?>
					<li>
						<input type="submit" name="btn_coffee_table_csv"
							   value="<?php echo esc_attr__( 'Download CSV list', 'cm_translate' ); ?>"
						>
					</li>
					<?php
				}
				?>
			</span>
		</ul>
		</td>
		</tr>

		<?php
	}

	protected static function get_months() {
		return array(
			'Januari',
			'Februari',
			'Maart',
			'April',
			'Mei',
			'Juni',
			'Juli',
			'Augustus',
			'September',
			'Oktober',
			'November',
			'December',
		);
	}

	protected static function generate_password( $length = 10 ) {
		$characters        = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$characters_length = strlen( $characters );
		$password          = '';
		for ( $i = 0; $i < $length; $i ++ ) {
			$password .= $characters[ rand( 0, $characters_length - 1 ) ];
		}

		return $password;
	}

	protected static function normalize_month( $month_name ) {
		$month_map = array_flip( static::get_months() );

		return isset( $month_map[ $month_name ] ) ? $month_map[ $month_name ] + 1 : 1;
	}

	/**
	 * Normalize date for DOMString format for HTML5 date widget:
	 * https://developer.mozilla.org/en-US/docs/Web/HTML/Element/input/date
	 *
	 * @param $date
	 *
	 * @return string
	 */
	public static function normalize_date( $date ) {
		$format = 'Y-m-d';
		if ( $date != '' ) {
			if ( \DateTime::createFromFormat( $format, $date, Order_Type::get_timezone() ) ) {
				return $date;
			}
			list( $day, $month, $year ) = explode( ' ', $date );
			$month                      = static::normalize_month( $month );

			return sprintf( '%s-%02s-%02s', $year, $month, $day );
		} else {
			return '';
		}
	}

	/**
	 * Location metabox
	 */
	public function location_metabox( $post ) {
		$location_query_args = array(
			'post_type'      => Location_Type::POST_TYPE,
			'orderby'        => 'post_name',
			'order'          => 'ASC',
			'posts_per_page' => - 1,
		);

		$locations        = get_posts( $location_query_args );
		$current_location = get_post_meta( $post->ID, Location_Type::META_KEY, true );
		?>
		<div class="form-wrap">
			<label for="<?php echo Location_Type::META_KEY; ?>"><?php echo __( 'Location', 'cm_translate' ); ?></label>
			<div class="form-field">
				<select name="<?php echo Location_Type::META_KEY; ?>" id="<?php echo Location_Type::META_KEY; ?>"
						class="postbox">
					<option value=""><?php echo __( 'Choose a location', 'cm_translate' ); ?></option>
					<?php foreach ( $locations as $location ) : ?>
						<option
							value="<?php echo $location->ID; ?>" <?php echo selected( $current_location, $location->ID, false ); ?>><?php echo sanitize_post_field( 'title', $location->post_title, $location->ID ); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<?php
	}

	/**
	 * Orders metabox
	 */
	public function order_metabox( $post ) {
		$order_query_args = array(
			'post_type'      => Order_Type::POST_TYPE,
			'orderby'        => 'date',
			'order'          => 'DESC',
			'posts_per_page' => - 1,
			'meta_key'       => 'cm_order_deceased_id',
			'meta_value'     => $post->ID,
		);

		$order_query = new WP_Query( $order_query_args );
		?>
		<div class="form-wrap">
			<?php if ( ! $order_query->have_posts() ) : ?>
				<em><?php echo __( 'No orders have been placed for this person.', 'cm_translate' ); ?></em>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped posts">
					<thead>
					<tr>
						<th><?php echo __( 'Customer', 'cm_translate' ); ?></th>
						<th><?php echo __( 'Summary', 'cm_translate' ); ?></th>
						<th><?php echo __( 'Total', 'cm_translate' ); ?></th>
						<th><?php echo __( 'Placed at', 'cm_translate' ); ?></th>
					</tr>
					</thead>
					<tbody>
					<?php
					while ( $order_query->have_posts() ) :
						$order_query->the_post();
						$order = Order::from_id( get_the_ID() );
						?>
						<tr>
							<td><?php echo Order_Type::order_customer_link( $order ); ?></td>
							<td><?php echo $order->get_summary(); ?></td>
							<td><?php echo $order->get_total()->display( true ); ?></td>
							<td><?php the_date(); ?></td>
						</tr>
						<?php
					endwhile;
					wp_reset_postdata();
					?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Saving meta fields
	 *
	 * @param $post_id
	 * @param $post
	 */
	public function save_condolence_person( $post_id, $post ) {
		global $wpdb;

		$post_data = array();
		foreach ( $_POST as $key => $value ) {
			if ( $key == 'deceased_noncename' ) {
				$post_data[ $key ] = $value;
			} elseif ( $key == 'email' || $key == 'coffee_table_email' ) {
				$post_data[ $key ] = sanitize_email( $value );
			} else {
				$post_data[ $key ] = sanitize_text_field( $value );
			}
		}

		// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return false;
		}

		if ( ! isset( $post_data['deceased_noncename'] ) ) {
			return false;
		}

		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! wp_verify_nonce( $post_data['deceased_noncename'], plugin_basename( __FILE__ ) ) ) {
			return false;
		}

		// Check permissions to edit pages and/or posts
		if ( Custom_Post_Type::post_type() == $post_data['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		/**
		 * Update password if empty
		 */
		if ( empty( $post_data['password'] ) ) {
			$post_data['password'] = static::generate_password();
		}

		// save meta fields
		$postfields = array(
			'name',
			'familyname',
			'birthdate',
			'birthplace',
			'placeofdeath',
			'dateofdeath',
			'funeraldate',
			'funeralinformation',
			'prayervigilinformation',
			'greetinginformation',
			'residence',
			'gender',
			'masscard',
			'prayer_card',
			'password',
			'email',
			'honoraryitle',
			'coffee_table',
			'coffee_table_email',
			'check_email',
			'live_stream_url',
			'live_stream_description',
		);

		foreach ( $postfields as $field ) {
			if ( isset( $post_data[ $field ] ) ) {
				update_post_meta( $post_id, $field, $post_data[ $field ] );
			}
		}

		/**
		 * Update location field
		 */
		update_post_meta( $post_id, Location_Type::META_KEY, (int) $post_data[ Location_Type::META_KEY ] );

		/**
		 * update funeral date checkbox
		 */
		if ( isset( $post_data['show_funeraldate'] ) ) {
			update_post_meta( $post_id, 'show_funeraldate', 1 );
		} else {
			update_post_meta( $post_id, 'show_funeraldate', 0 );
		}

		/**
		 * update flowers button
		 */
		if ( isset( $post_data['flowers'] ) ) {
			update_post_meta( $post_id, 'flowers', 1 );
		} else {
			update_post_meta( $post_id, 'flowers', 0 );
		}
		/**
		 * update live-stream button
		 */
		if ( isset( $post_data['live_stream'] ) ) {
			update_post_meta( $post_id, 'live_stream', 1 );
		} else {
			update_post_meta( $post_id, 'live_stream', 0 );
		}
		if ( isset( $post_data['live_stream_embed'] ) ) {
			update_post_meta( $post_id, 'live_stream_embed', 1 );
		} else {
			update_post_meta( $post_id, 'live_stream_embed', 0 );
		}

		$relations = array();
		// save relations
		foreach ( $post_data as $key => $value ) {
			if ( strpos( $key, 'relation_id' ) === 0 ) {
				$relation_number                = filter_var( $key, FILTER_SANITIZE_NUMBER_INT );
				$current_relation               = array();
				$current_relation['type']       = $value;
				$current_relation['other']      = $post_data[ 'relation' . $relation_number . '_other' ];
				$current_relation['name']       = $post_data[ 'relation' . $relation_number . '_name' ];
				$current_relation['familyname'] = $post_data[ 'relation' . $relation_number . '_familyname' ];
				$current_relation['alive']      = ( isset( $post_data[ 'relation' . $relation_number . '_alive' ] ) && $post_data[ 'relation' . $relation_number . '_alive' ] ) ? '1' : '0';
				$current_relation['gender']     = $post_data[ 'relation' . $relation_number . '_gender' ];
				$relations[]                    = $current_relation;
			}
		}

		if ( ! empty( $relations ) ) {
			update_post_meta( $post_id, 'relations', $relations );
		}

		if ( isset( $post_data['dateofdeath'] ) && isset( $post_data['name'] ) && isset( $post_data['familyname'] ) ) {
			$date_of_death = date_i18n( get_option( 'date_format' ), strtotime( $post_data['dateofdeath'] ) );
			$post_title    = $date_of_death . ' - ' . $post_data['name'] . ' ' . $post_data['familyname'];

			$post_title_sanitize = sanitize_title( $post_title );
			$query               = 'UPDATE ' . $wpdb->posts . " SET post_title='" . $post_title . "', post_name='" . $post_title_sanitize . "' WHERE ID=" . $post_id;

			$wpdb->query( $query );

			clean_post_cache( $post_id );
		}
	}

	/**
	 * TODO automatisch versturen? voorlopig is action gehide
	 * Send email after creating/updating post
	 *
	 * @param $post_id
	 */
	public function send_mail_to_family( $post_id ) {
		$mail     = get_post_meta( $post_id, 'email', true );
		$url      = get_the_permalink( $post_id );
		$password = get_post_meta( $post_id, 'password', true );

		if ( ! empty( $mail ) ) {
			wp_mail( $mail, __( 'Condolences', 'cm_translate' ), $this->create_passworded_url( $url, $password ) );
		}

	}

	/**
	 * Load css and jquery for metabox
	 */
	public function metabox_css_jquery() {
		global $post;
		if ( is_object( $post ) ) {
			if ( $post->post_type === Custom_Post_Type::post_type() ) {
				wp_enqueue_media();

				wp_register_style(
					'metabox_css',
					CM_URL . 'assets/css/metabox.css',
					false,
					CM_VERSION
				);
				wp_enqueue_style( 'metabox_css' );

				// Load the datepicker script (pre-registered in WordPress).
				wp_enqueue_script( 'jquery-ui-datepicker' );
				wp_register_style(
					'jquery-ui',
					'https://code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css',
					false,
					CM_VERSION
				);
				wp_enqueue_style( 'jquery-ui' );

				wp_enqueue_script(
					'token_generator_js',
					CM_URL . 'assets/js/metabox/token_generator.js',
					false,
					CM_VERSION,
					true
				);

				wp_enqueue_script(
					'date_pickers_js',
					CM_URL . 'assets/js/metabox/date_pickers.js',
					array( 'jquery-ui-datepicker' ),
					CM_VERSION,
					true
				);

				wp_enqueue_script(
					'file_pickers_js',
					CM_URL . 'assets/js/metabox/file_pickers.js',
					false,
					CM_VERSION,
					true
				);
			}
		}
	}
}
