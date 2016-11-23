<form method="post" enctype="multipart/form-data" id="cm_form" action="<?php echo $current_url; ?>">
    <div class="cm_form_wrapper">
        <ul class="cm_form_list">
            <li id="form_surname"
                class="field_required">
                <label class="form_label" for="form_surname"><?php _e('surname', 'cm_translate'); ?><span
                        class="form_required">*</span></label>
                <div class="form_medium"><input name="form_surname" id="form_surname"
                                                type="text" value="" class="medium"
                                                tabindex="1" aria-required="true"
                                                aria-invalid="false">
                </div>
            </li>
            <li id="form_name"
                class="field_required">
                <label class="form_label" for="form_name"><?php _e('name', 'cm_translate'); ?><span
                        class="form_required">*</span></label>
                <div class="form_medium"><input name="form_name" id="form_name"
                                                type="text" value="" class="medium"
                                                tabindex="2" aria-required="true"
                                                aria-invalid="false">
                </div>
            </li>
            <li id="form_address" class="field_required">
                <label class="form_label" for="form_address"><?php _e('address', 'cm_translate'); ?></label>
                <div class="form_large full_address">
                            <span class="street_number">
                                <input type="text" name="street_number" id="street_number" value="" tabindex="3">
                                <label for="street_number"
                                       id="street_number"><?php _e('street + number', 'cm_translate'); ?></label>
                            </span>
                    <span class="city">
                                    <input type="text" name="icity" id="city" value="" tabindex="4">
                                    <label for="city" id="city"><?php _e('city', 'cm_translate'); ?></label>
                            </span>
                    <span class="zip">
                                    <input type="text" name="zip" id="zip" value="" tabindex="5">
                                    <label for="zip" id="zip"><?php _e('zipcode', 'cm_translate'); ?></label>
                                </span>
                    <div class="clear"></div>
                </div>
            </li>
            <li id="form_email"
                class="field_required">
                <label class="form_label" for="form_email"><?php _e('email', 'cm_translate'); ?><span
                        class="form_required">*</span></label>
                <div class="form_medium"><input name="form_email" id="form_email"
                                                type="text" value="" class="medium"
                                                tabindex="6" aria-required="true"
                                                aria-invalid="false">
                </div>
            </li>
            <li id="form_phone"
                class="field_required">
                <label class="form_label" for="form_phone"><?php _e('Phone', 'cm_translate'); ?><span
                        class="form_required">*</span></label>
                <div class="form_medium"><input name="form_phone" id="form_phone"
                                                type="text" value="" class="medium"
                                                tabindex="7" aria-required="true"
                                                aria-invalid="false">
                </div>
            </li>
            <li id="title" class="description"><h2
                    class="title"><?php _e('presence coffee table', 'cm_translate'); ?></h2></li>
            <li id="form_present"
                class="field_required">
                <label class="form_label" for="form_present"><?php _e('Will be present', 'cm_translate'); ?><span
                        class="form_required">*</span></label>
                <div class="form_large radio">
                    <ul class="gfield_radio" id="input_3_9">
                        <li class="field_yes"><input name="field_yes" type="radio" value="pressent-yes"
                                                     id="field_yes" tabindex="8">
                            <label for="field_yes"
                                   id="field_yes"><?php _e('will be present', 'cm_translate'); ?></label></li>
                        <li class="field_no"><input name="field_no" type="radio" value="pressent-no"
                                                    id="field_no" tabindex="9">
                            <label for="field_no"
                                   id="field_no"><?php _e('will not be present', 'cm_translate'); ?></label></li>
                    </ul>
                </div>
            </li>


            <li id="form_person" class="field_required"
                style="display: none;"><label class="form_label"
                                              for="form_person"><?php _e('Number of persons', 'cm_translate'); ?></label>
                <div class="form_medium"><input name="form_person" id="form_person"
                                                type="text" value="" class="medium"
                                                tabindex="10" aria-invalid="false">
                    <div class="instruction "><?php _e('Please enter a value between', 'cm_translate'); ?>
                        <strong>0</strong> <?php _e('and', 'cm_translate'); ?>
                        <strong>20</strong>.
                    </div>
                </div>
            </li>
        </ul>
    </div>
    <div class="footer">
        <input type="submit" id="btn-form" class="btn" value="Submit" tabindex="11">
    </div>
</form>