<?php $subject = __('Coffee table', 'cm_translate');
$subject .= ' ' . $dead_man_name . ' ' . $dead_man_surname  ?>
<h3><?php _e('Presence coffee table', 'cm_translate'); ?></h3>
<ul>
    <li><?php _e('Name', 'cm_translate'); ?>: <?php  echo $participant->name; ?></li>
    <li><?php _e('Surname', 'cm_translate'); ?>: <?php  echo $participant->surname; ?></li>
    <li><?php _e('Address', 'cm_translate'); ?>: <?php  echo $participant->address; ?></li>
    <li><?php _e('email', 'cm_translate'); ?>: <?php  echo $participant->email; ?></li>
    <li><?php _e('telephone', 'cm_translate'); ?>: <?php  echo $participant->telephone; ?></li>
    <?php if($participant->otherparticipants === 0){ ?>
        <li><?php _e('I won\'t be able to make it to the coffee table', 'cm_translate'); ?></li>
    <?php }else{ ?>
        <li><?php _e('I will be present with', 'cm_translate'); ?>: <?php  echo $participant->otherparticipants; ?></li>
    <?php } ?>
</ul>

