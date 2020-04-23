<?php

namespace cm\includes\form;

use cm\includes\coffee_table\Coffee_Table_Controller;
use cm\includes\model\Order;
use cm\includes\register\Custom_Post_Type;
use cm\includes\register\Location_Type;
use cm\includes\register\Order_Type;
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
		add_meta_box( 'wpt_condolence_person_location', __( 'Information about deceased', 'cm_translate' ), array( $this, 'deceased_callback' ), Custom_Post_Type::post_type(), 'normal', 'high' );
		add_meta_box( 'wpt_condolence_person_location_linked_location', __( 'Location', 'cm_translate' ), array( $this, 'location_metabox' ), Custom_Post_Type::post_type(), 'side', 'default' );
		add_meta_box( 'wpt_condolence_person_location_side', __( 'View comments', 'cm_translate' ), array( $this, 'password_callback' ), Custom_Post_Type::post_type(), 'side', 'default' );
		add_meta_box( 'wpt_condolence_person_location_orders', __( 'Orders', 'cm_translate' ), array( $this, 'order_metabox' ), Custom_Post_Type::post_type(), 'normal', 'default' );
		add_meta_box( 'wpt_condolence_person_location_side_down', __( 'Coffee table', 'cm_translate' ), array( $this, 'coffee_table_metabox' ), Custom_Post_Type::post_type(), 'side', 'default' );
	}

	/**
	 * Define fields in metabox Information about departed soul
	 */
	public function deceased_callback() {
		global $post;

		// Use nonce for verification
		wp_nonce_field( plugin_basename( __FILE__ ), 'deceased_noncename' );
		?>

      <table class="form-table">
          <tr>
              <td><?php _e( 'Gender', 'cm_translate' ); ?></td>
              <td class="form-field">
                  <select name="gender">
                      <option><?php _e( 'Select gender', 'cm_translate' ); ?></option>
                      <option value="Male" <?php echo ($this->get_field_value( 'gender', $post->ID ) == 'Male') ? 'selected' : ''; ?>><?php _e( 'Male', 'cm_translate' ); ?></option>
                      <option value="Female" <?php echo ($this->get_field_value( 'gender', $post->ID ) == 'Female') ? 'selected' : ''; ?>><?php _e( 'Female', 'cm_translate' ); ?></option>
                  </select>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Name', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="text" name="name"
                                            value="<?php echo $this->get_field_value( 'name', $post->ID ); ?>"></td>
          </tr>
          <tr>
              <td><?php _e( 'Family name', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="text" name="familyname"
                                            value="<?php echo $this->get_field_value( 'familyname', $post->ID ); ?>">
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Honorary title', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="text" name="honoraryitle"
                                            value="<?php echo $this->get_field_value( 'honoraryitle', $post->ID ); ?>">
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Birthplace', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="text" name="birthplace"
                                            value="<?php echo $this->get_field_value( 'birthplace', $post->ID ); ?>">
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Birthdate', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="date" name="birthdate"
                                            value="<?php echo self::normalize_date($this->get_field_value( 'birthdate', $post->ID )); ?>">
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Place of death', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="text" name="placeofdeath"
                                            value="<?php echo $this->get_field_value( 'placeofdeath', $post->ID ); ?>">
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Date of death', 'cm_translate' ); ?></td>
              <td class="form-field"><input type="date" name="dateofdeath"
                                            value="<?php echo self::normalize_date($this->get_field_value( 'dateofdeath', $post->ID )); ?>">
              </td>
          </tr>
          <tr>
              <td><label for="funeraldate"><?php _e( 'Funeral date', 'cm_translate' ); ?></label></td>
              <td class="form-field">
                  <input type="date" name="funeraldate" id="funeraldate" value="<?php echo self::normalize_date($this->get_field_value( 'funeraldate', $post->ID )); ?>" />
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Funeral information' ); ?></td>
              <td class="form-field"><textarea rows="3" name="funeralinformation"
                  ><?php echo $this->get_field_value( 'funeralinformation', $post->ID ); ?></textarea></td>
          </tr>
          <tr>
              <td><?php _e( 'Prayer Vigil information', 'cm_translate' ); ?></td>
              <td class="form-field"><textarea rows="3"
                                               name="prayervigilinformation"><?php echo $this->get_field_value( 'prayervigilinformation', $post->ID ); ?></textarea>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Greeting information', 'cm_translate' ); ?></td>
              <td class="form-field"><textarea rows="3"
                                               name="greetinginformation"><?php echo $this->get_field_value( 'greetinginformation', $post->ID ); ?></textarea>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Residence', 'cm_translate' ); ?></td>
              <td class="form-field"><textarea rows="3"
                                               name="residence"><?php echo $this->get_field_value( 'residence', $post->ID ); ?></textarea>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Mass card', 'cm_translate' ); ?></td>
              <td class="form-field">
                  <input id="upload_media" type="text" name="masscard"
                         value="<?php echo $this->get_field_value( 'masscard', $post->ID ); ?>">
                  <input id="upload_media_button" class="button" type="button"
                         value="<?php _e( 'Choose mass card', 'cm_translate' ); ?>"/>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Flowers', 'cm_translate' ); ?></td>
              <td class="form-field">
                  <input id="flowers" type="checkbox" name="flowers"
                         value="1" <?php echo ( $this->get_field_value( 'flowers', $post->ID ) == '0' ) ? '' : 'checked'; ?>>
                  <p class="description"><?php
				      printf(
					      __( 'If enabled, a list of products and an order form will appear below a condolence page. Note, if you use a custom template file, you\'ll need to add these shortcodes: %s and %s.', 'cm_translate' ),
					      '[cm_products]',
					      '[cm_order_form]'
				      ); ?></p>
              </td>
          </tr>
          <tr>
              <td><?php _e( 'Additional buttons', 'cm_translate' ); ?></td>
              <td class="form-field">
                  <input id="cm_additional_btn" type="checkbox" name="cm_additional_btn"
                         value="1" <?php echo ( $this->get_field_value( 'cm_additional_btn', $post->ID ) == '0' ) ? '' : 'checked'; ?>>
              </td>
          </tr>
          <tr>
              <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="2">&nbsp;</td>
          </tr>
          <tr>
              <td colspan="2"><b><?php _e( 'Relations', 'cm_translate' ); ?></b>
                  <hr>
              </td>
          </tr>
				<?php
				$relations = get_post_meta( $post->ID, 'relations', false );

				if ( !$relations ) {
					?>
            <tr>
                <td valign="top"><?php _e( 'Relation 1', 'cm_translate' ); ?></td>
                <td class="form-field">
                    <select name="relation_id1" id="relation_id1">
                        <option><?php _e( 'Select relation type', 'cm_translate' ); ?></option>
                        <option value="Single"><?php _e( 'Single', 'cm_translate' ); ?></option>
                        <option value="Married"><?php _e( 'Married', 'cm_translate' ); ?></option>
                        <option value="Other"><?php _e( 'Other', 'cm_translate' ); ?></option>
                    </select>
                    <button class="add_more"><span class="dashicons dashicons-plus-alt"
                                                   title="<?php _e( 'Add more relation', 'cm_translate' ); ?>"></span>
                    </button>
                    <div class="relation1_more_information">
                        <input type="text" class="relation1_other" name="relation1_other"
                               placeholder="<?php _e( 'Relation name', 'cm_translate' ); ?>">
                        <input type="text" name="relation1_name" placeholder="<?php _e( 'Name', 'cm_translate' ); ?>">
                        <input type="text" name="relation1_familyname"
                               placeholder="<?php _e( 'Family name', 'cm_translate' ); ?>">
											<?php _e( 'Is alive?', 'cm_translate' ); ?> <input type="checkbox" name="relation1_alive">
                        <select name="relation1_gender">
                            <option>Select gender</option>
                            <option value="Male"><?php _e( 'Male', 'cm_translate' ); ?></option>
                            <option value="Female"><?php _e( 'Female', 'cm_translate' ); ?></option>
                        </select>
                    </div>
                </td>
            </tr>
					<?php
				} else {
					$count     = 1;
					$relations = current( $relations );
					foreach ( $relations as $relation ) {
						?>
              <tr>
                  <td valign="top"><?php echo __( 'Relation ', 'cm_translate' ) . $count; ?></td>
                  <td class="form-field">
                      <select name="relation_id<?php echo $count; ?>" id="relation_id<?php echo $count; ?>">
                          <option><?php _e( 'Select relation type', 'cm_translate' ); ?></option>
                          <option value="Single" <?php echo ($relation['type'] == 'Single') ? 'selected' : ''; ?>><?php _e( 'Single', 'cm_translate' ); ?></option>
                          <option value="Married" <?php echo ($relation['type'] == 'Married') ? 'selected' : ''; ?>><?php _e( 'Married', 'cm_translate' ); ?></option>
                          <option value="Other" <?php echo ($relation['type'] == 'Other') ? 'selected' : ''; ?>><?php _e( 'Other', 'cm_translate' ); ?></option>
                      </select>
										<?php
										if ( $count == 1 ) {
											?>
                        <button class="add_more"><span class="dashicons dashicons-plus-alt"
                                                       title="<?php _e( 'Add more relation', 'cm_translate' ); ?>"></span>
                        </button> <?php
										} else {
											?> <span class="dashicons dashicons-dismiss"
                               title="<?php _e( 'Remove relation', 'cm_translate' ); ?>"></span> <?php
										}
										?>


                      <div class="relation<?php echo $count; ?>_more_information" <?php echo ($relation['type'] == 'Single') ? 'style="display:none;"' : ''; ?>>
                          <input type="text" class="relation<?php echo $count; ?>_other"
                                 name="relation<?php echo $count; ?>_other"
                                 placeholder="<?php _e( 'Relation name', 'cm_translate' ); ?>"
                                 value="<?php echo $relation['other']; ?>" <?php echo ($relation['type'] == 'Married') ? 'style="display:none;"' : ''; ?>>
                          <input type="text" name="relation<?php echo $count; ?>_name"
                                 placeholder="<?php _e( 'Name', 'cm_translate' ); ?>"
                                 value="<?php echo $relation['name']; ?>">
                          <input type="text" name="relation<?php echo $count; ?>_familyname"
                                 placeholder="<?php _e( 'Family name', 'cm_translate' ); ?>"
                                 value="<?php echo $relation['familyname']; ?>">
												<?php _e( 'Is alive?', 'cm_translate' ); ?> <input type="checkbox"
                                                                           name="relation<?php echo $count; ?>_alive" <?php echo ($relation['alive'] == '1') ? 'checked' : ''; ?>>
                          <select name="relation<?php echo $count; ?>_gender">
                              <option><?php _e( 'Select gender', 'cm_translate' ); ?></option>
                              <option value="Male" <?php echo ($relation['gender'] == 'Male') ? 'selected' : ''; ?>><?php _e( 'Male', 'cm_translate' ); ?></option>
                              <option value="Female" <?php echo ($relation['gender'] == 'Female') ? 'selected' : ''; ?>><?php _e( 'Female', 'cm_translate' ); ?></option>
                          </select>
                      </div>
                  </td>
              </tr>
						<?php
						$count ++;
					}
				}
				/**
				 *  action hook to render metabox on backend
				 */
				do_action( 'conman_render_metabox' );
				?>

      </table>

      <script>
				jQuery(document).ready(function ($) {
					$('#upload_media_button').click(function (e) {

						e.preventDefault();

						//Extend the wp.media object
						custom_uploader = wp.media.frames.file_frame = wp.media({
							title: '<?php _e( 'Choose mass card', 'cm_translate' ); ?>',
							button: {
								text: '<?php _e( 'Choose mass card', 'cm_translate' ); ?>'
							},
							multiple: false
						});

						//When a file is selected, grab the URL and set it as the text field's value
						custom_uploader.on('select', function () {
							attachment = custom_uploader.state().get('selection').first().toJSON();
							console.log(attachment);
							jQuery('#upload_media').val(attachment.url);
						});

						//Open the uploader dialog
						custom_uploader.open();

					});

					$(document).on('click', '.add_more', function (e) {
						e.preventDefault();
						var nummer = $(".form-table tr:last-child select").attr('id').replace(/[^\d.]/g, '');
						nummer = parseInt(nummer) + 1;

						$(".form-table").find('tbody')
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
														.append('<?php _e( 'Is alive?' ); ?> ')
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
					});

					$(document).on('click', '.dashicons-dismiss', function (e) {
						e.preventDefault();
						if (confirm('<?php _e( 'Are you sure to delete this relation?', 'cm_translate' ); ?>')) {
							$(this).parent().parent().remove();
						}

					});

					$(document).on('load', 'select[id^="relation_id"]', function (e) {
						e.preventDefault();
						var select_value = $(this).val();
						var nummer = parseInt($(this).attr('id').replace(/[^\d.]/g, ''));

						switch (select_value) {
							case 'Single':
								$('.relation' + nummer + '_more_information').hide();
								break;
							case 'Married':
								$('.relation' + nummer + '_more_information').show();
								$('.relation' + nummer + '_other').hide();
								break;
							case 'Other':
								$('.relation' + nummer + '_more_information').show();
								$('.relation' + nummer + '_other').show();
								break;
							default:
								$('.relation' + nummer + '_more_information').hide();
								break;
						}
					});

					$(document).on('change', 'select[id^="relation_id"]', function (e) {
						e.preventDefault();
						var select_value = $(this).val();
						var nummer = parseInt($(this).attr('id').replace(/[^\d.]/g, ''));

						switch (select_value) {
							case 'Single':
								$('.relation' + nummer + '_more_information').hide();
								break;
							case 'Married':
								$('.relation' + nummer + '_more_information').show();
								$('.relation' + nummer + '_other').hide();
								break;
							case 'Other':
								$('.relation' + nummer + '_more_information').show();
								$('.relation' + nummer + '_other').show();
								break;
							default:
								$('.relation' + nummer + '_more_information').hide();
								break;
						}
					});
				});
				<?php
				/**
				 * action hook to add js
				 */
				do_action( 'conman_backend_js' ); ?>
      </script>

		<?php
	}

	/**
	 * Checks meta value
	 *
	 * @param $field_name meta key
	 * @param $post_id    post id
	 *
	 * @return mixed|string returns field value or empty string
	 */
	public function get_field_value( $field_name, $post_id ) {
		$meta_value = get_post_meta( $post_id, $field_name, false );

    $result = '';
		if ( $meta_value ) {
			$result= current( $meta_value );
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
      <label><?php _e( 'Password', 'cm_translate' ); ?> <a id="generate"
                                                           href=""><?php _e( 'Create token', 'cm_translate' ); ?></a></label>
      <input id="password" type="text" name="password"
             value="<?php echo $this->get_field_value( 'password', $post->ID ); ?>">

      <label><?php _e( 'View comments', 'cm_translate' ); ?></label>
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
      <a href="<?php echo $permalink ?>" target="_blank"><?php _e( 'Link to view comments', 'cm_translate' ); ?></a>
      <br><br>
      <input type="checkbox" name="check_email" value="check_email"
             style="width: 15px;" <?php if ( $this->get_field_value( 'check_email', $post->ID ) === 'check_email' ) {
			echo 'checked';
		} ?>><?php _e( 'Send email to family when someone condones', 'cm_translate' ); ?>
        <label><?php _e( 'E-mail', 'cm_translate' ); ?></label>
        <input type="text" name="email" value="<?php echo $this->get_field_value( 'email', $post->ID ); ?>">
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
		return (strpos( $permalink, '?' ) !== false) ? $permalink . '&code=' . $password : $permalink . '?code=' . $password;
	}

	/**
	 * form to download coffee table list
	 * show number of
	 */
	public function coffee_table_metabox( $post ) {
		$controller       = new Coffee_Table_Controller();
		$sum_participants = $controller->get_sum_of_otherparticipants( $post->ID );
		$sum_emails       = $controller->get_sum_of_posts( $post->ID );
		?>
      <ul>
          <li><?php _e( 'Coffee table', 'cm_translate' ); ?></li>
          <li><select id="coffee_table" name="coffee_table">
                  <option value="no"><?php _e( 'No', 'cm_translate' ); ?></option>
                  <option value="yes" <?php if ( $this->get_field_value( 'coffee_table', $post->ID ) == 'yes' ) {
										echo "selected";
									} ?> ><?php _e( 'Yes', 'cm_translate' ); ?></option>
              </select></li>
          <span id="span_coffee_table_email" <?php if ( $this->get_field_value( 'coffee_table', $post->ID ) != 'yes' ) {
						echo "hidden";
					} ?> >
            <li><label for="coffee_table_email"><?php _e( 'Email address', 'cm_translate' ); ?></label></li>
            <li> <input type="text" name="coffee_table_email" id="coffee_table_email"
                    <?php if ( $this->get_field_value( 'coffee_table', $post->ID ) ) {
											echo " value= '" . $this->get_field_value( 'coffee_table_email', $post->ID ) . "'";
										} ?> ></li>
            <li><input type="submit" name="btn_coffee_table_csv"
                       value="<?php _e( 'Download CSV list', 'cm_translate' ); ?>"></li>
                <?php echo is_numeric( $sum_emails ) ? '<li><p>' . __( 'Emails: ', 'cm_translate' ) . $sum_emails . '</p></li>' : false;
								echo '<li><p>' . __( 'Participants: ', 'cm_translate' ) . $sum_participants . '</p></li>';
								?>
            </span>
      </ul>
      </td>
      </tr>

		<?php
	}

	protected static function get_months() {
		return array(
			"Januari",
			"Februari",
			"Maart",
			"April",
			"Mei",
			"Juni",
			"Juli",
			"Augustus",
			"September",
			"Oktober",
			"November",
			"December"
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
    public static function normalize_date($date) {
	    $format = 'Y-m-d';
	    if ( $date != '' ) {
            if(\DateTime::createFromFormat($format, $date, Order_Type::get_timezone())) {
                return $date;
            }
            list($day, $month, $year) = explode(' ', $date);
            $month = static::normalize_month($month);

            return sprintf("%s-%02s-%02s", $year, $month, $day);
        }

	    else {
	        return "";
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
          <label for="<?= Location_Type::META_KEY ?>"><?= __( 'Location', 'cm_translate' ) ?></label>
          <div class="form-field">
              <select name="<?= Location_Type::META_KEY ?>" id="<?= Location_Type::META_KEY ?>" class="postbox">
                  <option value=""><?= __( 'Choose a location', 'cm_translate' ) ?></option>
								<?php foreach ( $locations as $location ): ?>
                    <option value="<?= $location->ID ?>" <?= selected( $current_location, $location->ID, false ) ?>><?= sanitize_post_field( 'title', $location->post_title, $location->ID ) ?></option>
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
				'posts_per_page' => -1,
                'meta_key'       => 'cm_order_deceased_id',
                'meta_value'     => $post->ID,
		);

		$order_query = new WP_Query( $order_query_args );?>
      <div class="form-wrap">
          <?php if(!$order_query->have_posts()):?>
            <em><?= __('No orders have been placed for this person.', 'cm_translate')?></em>
          <?php else: ?>
          <table class="wp-list-table widefat fixed striped posts">
              <thead>
                <tr>
                    <th><?=__('Customer', 'cm_translate')?></th>
                    <th><?=__('Summary', 'cm_translate')?></th>
                    <th><?=__('Total', 'cm_translate')?></th>
                    <th><?=__('Placed at', 'cm_translate')?></th>
                </tr>
              </thead>
              <tbody>
                <?php while($order_query->have_posts()): $order_query->the_post(); $order = Order::from_id(get_the_ID());?>
                <tr>
                    <td><?= Order_Type::order_customer_link($order)?></td>
                    <td><?= $order->get_summary()?></td>
                    <td><?= $order->get_total()->display(true)?></td>
                    <td><?php the_date()?></td>
                </tr>
                <?php endwhile; wp_reset_postdata();?>
              </tbody>
          </table>
          <?php endif;?>
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

		// Verify if this is an auto save routine. If it is our form has not been submitted, so we dont want
		// to do anything
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['deceased_noncename'] ) )
			return;

		// Verify this came from the our screen and with proper authorization,
		// because save_post can be triggered at other times
		if ( ! wp_verify_nonce( $_POST['deceased_noncename'], plugin_basename( __FILE__ ) ) ) {
			return;
		}


		// Check permissions to edit pages and/or posts
		if ( Custom_Post_Type::post_type() == $_POST['post_type'] ) {
			if ( ! current_user_can( 'edit_page', $post_id ) || ! current_user_can( 'edit_post', $post_id ) ) {
				return $post_id;
			}
		}

		/**
		 * Update password if empty
		 */
		if ( empty( $_POST['password'] ) ) {
			$_POST['password'] = static::generate_password();
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
            'password',
            'email',
            'honoraryitle',
            'coffee_table',
            'coffee_table_email',
            'check_email'
		);

		foreach ( $postfields as $field ) {
			update_post_meta( $post_id, $field, $_POST[$field] );
		}

		/**
		 * Update location field
		 */
		update_post_meta( $post_id, Location_Type::META_KEY, (int) $_POST[Location_Type::META_KEY] );

		/**
		 * update flowers button
		 */
		if ( isset( $_POST['flowers'] ) ) {
			update_post_meta( $post_id, 'flowers', $_POST['flowers'] );
		} else {
			update_post_meta( $post_id, 'flowers', 0 );
		}
		/**
		 * update additional buttons metabox
		 */
		if ( isset( $_POST['cm_additional_btn'] ) ) {
			update_post_meta( $post_id, 'cm_additional_btn', $_POST['cm_additional_btn'] );
		} else {
			update_post_meta( $post_id, 'cm_additional_btn', 0 );
		}

		$relations = array();
		// save relations
		foreach ( $_POST as $key => $value ) {
			if ( strpos( $key, 'relation_id' ) === 0 ) {
				$relation_number                = filter_var( $key, FILTER_SANITIZE_NUMBER_INT );
				$current_relation               = array();
				$current_relation['type']       = $value;
				$current_relation['other']      = $_POST['relation' . $relation_number . '_other'];
				$current_relation['name']       = $_POST['relation' . $relation_number . '_name'];
				$current_relation['familyname'] = $_POST['relation' . $relation_number . '_familyname'];
				$current_relation['alive']      = ($_POST['relation' . $relation_number . '_alive']) ? '1' : '0';
				$current_relation['gender']     = $_POST['relation' . $relation_number . '_gender'];
				$relations[]                    = $current_relation;
			}
		}

		if ( !empty( $relations ) ) {
			update_post_meta( $post_id, 'relations', $relations );
		}

		if ( isset( $_POST['dateofdeath'] ) && isset( $_POST['name'] ) && isset( $_POST['familyname'] ) ) {
			$arraymaand = static::get_months();

			$date       = $_POST['dateofdeath'];
			$pieces     = explode( "-", $date );
			$num        = intval( $pieces[1] );
			$month      = $arraymaand[$num - 1];
			$post_title = $pieces[2] . ' ' . $month . ' ' . $pieces[0] . ' - ' . $_POST['name'] . ' ' . $_POST['familyname'];

			$post_title_sanitize = sanitize_title( $post_title );

			$query = "UPDATE " . $wpdb->posts . " SET post_title='" . $post_title . "', post_name='" . $post_title_sanitize . "' WHERE ID=" . $post_id;

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

		if ( !empty( $mail ) ) {
			wp_mail( $mail, __( 'Condolences', 'cm_translate' ), $this->create_passworded_url( $url, $password ) );
		}

	}

	/**
	 * Load css and jquery for metabox
	 */
	public function metabox_css_jquery() {
		global $post;
		if ( is_object( $post ) ) {
			if ( $post->post_type == Custom_Post_Type::post_type() ) {
				wp_register_style( 'metabox_css', CM_URL . 'css/metabox.css', false, '1.0.0' );
				wp_enqueue_style( 'metabox_css' );

				wp_register_script( 'metabox_js', CM_URL . 'js/metabox.js', array(), false, true );
				wp_localize_script( 'metabox_js', 'metabox', array( 'ajaxUrl' => get_admin_url() . 'admin-ajax.php' ) );
				wp_enqueue_script( 'metabox_js' );
				wp_enqueue_media();

				wp_enqueue_script( 'jquery-datepicker', 'http://jquery-ui.googlecode.com/svn/trunk/ui/jquery.ui.datepicker.js', array( 'jquery', 'jquery-ui-core' ) );
				wp_enqueue_style( 'jquery-style', 'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.2/themes/smoothness/jquery-ui.css' );
			}
		}
	}
}
