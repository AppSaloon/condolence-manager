<?php
/*
comment-error-template.php

display comment errors in this template if cookies disabled and php does not allow passing session id in GET method
use the variable $comment_post_id to get information about the post the user was comment on
use template tags to print out information about the post such as building a link back to the post

you should be able to copy and paste this code into the main body area of your 404.php template and save it in the main directory of your theme as wpice-comment-error-template.php
*/

print "<h1 class=\"secondarypage\">Comment Error</h1>"; // print the page title

apply_filters('wpice_print_comment_form_error_list',''); // call template tag to print the error list
print '<p>Return to the post <a href="' . get_permalink($comment_post_id) . '">' . get_the_title($comment_post_id) . '</a> and try your comment again.</p>';
?>