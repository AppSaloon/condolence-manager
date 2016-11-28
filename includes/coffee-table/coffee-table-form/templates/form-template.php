<div id="ct_div" hidden>
    <p id="ct_form_message" class="ct_form"></p>
    <form class="form-horizontal" id="ct_form">
        <div class="form-group">
            <label class="col-sm-2" for="ct_name"><?php _e('Name', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_name" placeholder="<?php _e('Name', 'cm_translate'); ?>"
                       required>
            </div>
            <label class="col-sm-2" for="ct_surname"><?php _e('Surname', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_surname"
                       placeholder="<?php _e('Surname', 'cm_translate'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2" for="ct_street"><?php _e('Street', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_street"
                       placeholder="<?php _e('Street', 'cm_translate'); ?>">
            </div>
            <label class="col-sm-2" for="ct_str_number"><?php _e('Number', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_str_number"
                       placeholder="<?php _e('Number', 'cm_translate'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2" for="ct_city"><?php _e('City', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_city" placeholder="<?php _e('City', 'cm_translate'); ?>">
            </div>
            <label class="col-sm-2" for="ct_zipcode"><?php _e('Zipcode', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="text" class="form-control" id="ct_zipcode"
                       placeholder="<?php _e('Zipcode', 'cm_translate'); ?>"
                       pattern="(?i)^[a-z0-9][a-z0-9\- ]{0,10}[a-z0-9]$">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2" for="ct_country"><?php _e('Country', 'cm_translate'); ?>:</label>
            <div class="col-sm-10">
                <input type="text" class="form-control" id="ct_country"
                       placeholder="<?php _e('Country', 'cm_translate'); ?>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2" for="ct_email"><?php _e('Email', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="email" class="form-control" id="ct_email"
                       placeholder="<?php _e('Email', 'cm_translate'); ?>" required>
            </div>
            <label class="col-sm-2" for="ct_gsm"><?php _e('Phone', 'cm_translate'); ?>:</label>
            <div class="col-sm-4">
                <input type="tel" class="form-control" id="ct_gsm" placeholder="<?php _e('Phone', 'cm_translate'); ?>"
                       pattern="(?i)^[a-z0-9][a-z0-9\- ]{0,10}[a-z0-9]$">
            </div>
        </div>
        <div class="description">
            <div class="col-sm-offset-2 col-sm-10">
            <h2 class="title"><?php _e('Presence coffee table', 'cm_translate'); ?>?</h2>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <div class="radio">
                    <label><input name="yes_no_radio" type="radio" onclick='jQuery("#form_person").show()'
                                  value="pressent-yes" id="field_yes"><?php _e('Will be present', 'cm_translate'); ?>
                    </label>
                    <label><input name="yes_no_radio" type="radio" onclick='jQuery("#form_person").hide()'
                                  value="pressent-no" id="field_no"><?php _e('Will not be present', 'cm_translate'); ?>
                    </label>
                </div>
            </div>
        </div>
        <div class="form-hidden">
            <div>
                <input type="hidden" class="form-control" id="ct_post_id" value="<?php echo $post->ID; ?>">
            </div>
        </div>
        <div class="form-group" id="form_person" style="display: none;">
            <label class="col-sm-2"
                   for="ct_more_people"><?php _e('How many people will attend', 'cm_translate'); ?>?</label>
            <div class="col-sm-10">
                <input type="number" class="form-control" id="ct_more_people"
                       placeholder="<?php _e('min 1', 'cm_translate'); ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-default" id="ct_form_btn">Submit</button>
            </div>
        </div>
    </form>
</div>