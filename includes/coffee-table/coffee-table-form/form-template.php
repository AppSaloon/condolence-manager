<?php
?>
<div style="padding: 30px;">
    <p id="ct_form_message"></p>
<form id="ct_form" action="">
    <label for="ct_name">Name</label>
    <input id="ct_name"  type="text" required>
    <label for="ct_surname">Surname</label>
    <input id="ct_surname" type="text" required>
    <label for="ct_street">Street</label>
    <input id="ct_street"  type="text">
    <label for="ct_city">City</label>
    <input id="ct_city" type="text">
    <label for="ct_zipcode">Zip Code</label>
    <input id="ct_zipcode" type="number">
    <label for="ct_email">Email</label>
    <input id="ct_email" type="email" required>
    <label for="ct_gsm">Telephone</label>
    <input id="ct_gsm"  type="number">
    <input id="ct_post_id" value="<?php echo $post->ID; ?>" type="hidden">
    <label for="ct_more_people">Someone with You?</label>
    <input id="ct_more_people" type="number" min="1">
    <input id="ct_form_btn" type="submit">
</form>
</div>
