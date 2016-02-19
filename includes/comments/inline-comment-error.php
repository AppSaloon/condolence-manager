<?php

namespace cm\includes\comments;


use cm\includes\register\Custom_Post_Type;

class Inline_Comment_Error {

    // private properties
    private $error_code = 'comment_err'; // error code for wp_error to identify errors coming from comment form
    private $session_form_data = 'wpice_comment_form_data'; // name of session variable to store comment form $_POST data
    private $session_wp_error = 'wpice_wp_error'; // name of session variable to store wp_error

    // public properties, might be useful to other plug-in developers
    public $text_domain = 'wp_inline_comment_errors'; // for localization
    public $comment_err; // assigned to a WP_Error in _construct
    public $comment_post_id; // holds the id of the post for the comment, not set until comment form submitted
    public $pre_comment_on_post_priority = 1; // priority of filter

    // user configurable properties
    /*
    array of parameters to configure plug-in behavior
    user can set with 'set' action and get the array with 'get' filter
    */
    public $config_options = array(

        /*
        FORM RELATED PROPERTIES ==================================================================================
        set conditions for displaying $_POST data and required mark in author, email, url and comments fields
        functions executed from comment_form_field_{fieldname} use these conditions to add or remove values from these fields
        default to display no post data, show wordpress required mark and do not show wpice required mark
        note that wordpress required mark only works for WordPress generated form, custom HTML form may not parse the same as the
        wordpress generated form elements
        */
        /*
        'show_post_data' => true | false
        true - displays $_POST data in form fields after submission contains errors
        false - do not automatically display $_POST data
        */
        'show_post_data' => true,

        /*
        'req_mark_location' => 'after-field' | 'after-label' | 'none'
        after-field - display required mark after field
        after-label - display required mark after label tag text
        none - do not display the required mark
        */
        'req_mark_location' => 'after-label',

        /*
        'req_mark' => '<span class="comment-form-required">*</span>'
        html for required mark
        */
        'req_mark' => '<span class="comment-form-required">*</span>',

        /*
        'hide_wp_req_mark' => true | false
        true - removes wordpress generated required mark from author and email field html
        false - does not remove required mark
        */
        'hide_wp_req_mark' => false,

        /*
        'auto_display_errors' => null
        automatically display the comment error list using the specified comment form action,
        such as comment_form_top
        set to null in class and 'comment_form_top' in plug-in
        */
        'auto_display_errors' => 'comment_form_top',

        /*
        OTHER PROPERTIES ==========================================================
        */

        /*
        name of URL fragment identifier and anchor tag for scroll down to form feature,
        only used when plug-in configured to automatically display errors
        */
        'anchor_fragment_name' => 'goto_error_message',

        /*
        path to comment error template to display errors if cookies disabled and session get not supported
        */
        'error_template_path' => 'comment-error-template.php',
    );
    /*
    associative array that stores default error messages, index is name of field and value is error message
    */
    public $default_error_msgs = array();

    /*
    associative array that stores error messages, index is name of field and value is error message
    */
    public $custom_error_msgs = array();

    // names of custom filters
    // DOCUMENT EACH FILTER
    public $author_validation_filter = 'wpice_validate_commentform_author'; // wpdb/$_POST field is 'author', WordPress label is 'name'
    public $email_validation_filter = 'wpice_validate_commentform_email';
    public $url_validation_filter = 'wpice_validate_commentform_url'; // wpdb/$_POST field is 'url', WordPress label is 'website'
    public $comment_validation_filter = 'wpice_validate_commentform_comment';
    public $metafield_validation_filter = 'wpice_validate_commentform_metafield';
    public $get_redirect_url = 'wpice_get_redirect_url'; // passes url to and returns url from end user function to modify url before redirect
    protected $check_post_type = true;

    /**
     * Construct the object
     */
    public function __construct(){
        // public property is an WP_Error object, accumulate errors from all user defined filters using this property
        $this->comment_err = new \WP_Error();

        // init event to enable sessions, which takes place after wordpress resets all php global values
        add_action('init', array($this,'start_session'), 2);

        add_action('init', array($this,'set_messages_on_init'), 3); // call on init to merge with default messages

        // comment form posts comment data to wp-comments-post.php
        // 'pre_comment_on_post' filter executes after comment form submits and before main script of wp-comments-post.php runs.
        // use this filter to intercept comment data for validation before wp-comments-post.php attempts to validate and save comment
        add_filter( 'pre_comment_on_post', array($this,'validate_comment_formfields'),$this->pre_comment_on_post_priority);

        if( $this->check_post_type ){
            // The 'wp_die_handler' filter executes before the core wp_die() function.   Use this filter to intercept error messages
            // and terminate the script before wp-comments-post.php runs.  Stores $_POST and error data in a session variable.
            add_filter('wp_die_handler', array($this,'get_comment_err_die_handler'));

            // use 'template_redirect' action to call function to get $_POST and error data from session variable
            add_action('template_redirect', array($this,'convert_values_from_session'));

            // these TEMPLATE TAGS called from apply_filters('filter name','params')
            add_filter('wpice_get_comment_form_errors_as_list', array( $this, 'get_comment_form_errors_as_list') ); // returns list
        }
    } // END public function __construct

    /**
     * start session
     * store $_POST from comment form and error data  in session for reuse on template that diplays comment form
     *
     * @since 1.0
     */
    public static function start_session(){
        if(!session_id()) {
            session_start();
        }
    } // END start_session


    /**
     * set custom comment error message
     *
     * @since 1.0
     * @param    array    $user_custom_errs    associative array, index is field name, value is error message
     */
    function set_messages($user_custom_errs){
        if(is_array($user_custom_errs) == true){ // user provides some errors save in custom error messages property
            $this->custom_error_messages = $user_custom_errs;
        }
    } // END set_messages

    /**
     * merge custom error messages with default messages on init.  Due to localization, default messagses are not set until init.
     * Wait until init to merge messages, in case user has not provided all possible custom messages, use some defaults with
     * custom messages.
     *
     * @since 1.1
     */
    function set_messages_on_init(){
        if(is_array($this->custom_error_messages) == true){
            $this->custom_error_messages = array_merge($this->default_error_messages,$this->custom_error_messages);
        } else {
            $this->custom_error_messages = $this->default_error_messages;
        }
    }


    /**
     * get error message set by set_messages, does not return error messages created within validation functions
     *
     * @since 1.0
     * @return   array   associative array, index is field name, value is error message
     */
    function get_defined_messages(){
        return $this->custom_error_messages;
    } // END get_defined_messages


    /**
     * pass comment form field and error message, store a comment_err message
     * store the error code, message in the messages array, and  $_POST field name in the data array
     *
     * @since 1.0
     * @param    string    $form_field_name    comment form field, should be the same as the HTML tag name attribute
     * @param    string    $error_message    error message associated with the field
     */
    function store_comment_error($form_field_name,$error_message){
        $this->comment_err->add($this->error_code, $error_message); // add error message to wp_error object
        $this->comment_err->error_data[$this->error_code][] = $form_field_name; // add comment form field name to object data array
    } // END store_comment_error

    /**
     * The 'validate_comment_formfields' function executes user defined filters and stores any errors in a WP_Error object.
     * If there are no user defined fliters then use default validation and error messages.  Exectuted from 'pre_comment_on_post'
     * WordPress comment form filter.  The pre_comment_on_post assignment is in the __construct function.
     *
     * If there are errors, the function passes the WP_Error object to wp_die(), which will then call the comment_err_die_handler
     *
     * @since 1.0
     * @param    object    $commentdata    comment form field data
     * @return   object    $commentdata    pass comment form field data back to filter, or call wp_die() with no return value
     */
    public function validate_comment_formfields( $commentdata ) {
        if( get_post_type( $commentdata ) != Custom_Post_Type::POST_TYPE){
            $this->check_post_type = false;
            return $commentdata;
        }
        /*
        validation for author/name, email and ur/website field
        If 'Comment author must fill out name and e-mail' is checked in Discussion Settings then require validation for author and email
        use default message if no custom message

        If user is logged in, then skip this validation because user has already provided name, email and url,
        follows same check as wp-comments-post.php
        */
        $user = wp_get_current_user();
        if(!$user->exists()){ // only validate these fields if the user is not logged in

            // run custom validation for author field
            if(has_filter($this->author_validation_filter)){ // check for user function
                $error_message = apply_filters($this->author_validation_filter,''); // execute the custom filter
                if($error_message != ''){ // has an error message
                    $this->store_comment_error('author',$error_message);
                }

                // does not have user function but still required by discussion settings, so create a default message
            } else if(get_option('require_name_email')){
                // check author field
                if(!isset($_POST['author']) || trim($_POST['author']) == ''){ // missing or empty author field
                    $this->store_comment_error('author',$this->custom_error_messages['author']); // use stored message
                }
            }

            // run custom validation on email field
            if(has_filter($this->email_validation_filter)){
                $error_message = apply_filters($this->email_validation_filter,'');
                if($error_message != ''){
                    $this->store_comment_error('email',$error_message);
                }

                // does not have user function but still required by discussion settings, so create a default message
            } else if(get_option('require_name_email')){
                // check for missing or malformed email, use wordpress is_email function
                if(!isset($_POST['email']) || (function_exists('is_email') && !is_email($_POST['email']))){
                    $this->store_comment_error('email',$this->custom_error_messages['email']); // use stored message
                }
            }

            // custom validation for url field
            if(has_filter($this->url_validation_filter)){ // check for user function
                $error_message = apply_filters($this->url_validation_filter,''); // execute the custom filter
                if($error_message != ''){ // has an error message
                    $this->store_comment_error('url',$error_message);
                }
            }
        }

        // user must provide comment regardless of logged in or not
        // validation for comment field
        if(has_filter($this->comment_validation_filter)){ // check for user function
            $error_message = apply_filters($this->comment_validation_filter,''); // execute the custom filter
            if($error_message != ''){ // has an error message
                $this->store_comment_error('comment',$error_message);
            }
        } else { // does not have user function but still required, so use a default message
            // check comment field
            if(!isset($_POST['comment']) || trim($_POST['comment']) == ''){
                $this->store_comment_error('comment',$this->custom_error_messages['comment']);
            }
        }

        // user may need to provide correct input for meta field regardless of logged in or not, such as captcha field
        // validation for custom comment meta fields
        if(has_filter($this->metafield_validation_filter)){ // execute all custom field validation functions, each will return an error object
            $error_messages = array();
            $error_messages = apply_filters($this->metafield_validation_filter,$error_messages); // store each error message in an array
            foreach ($error_messages as $field_name => $message){ // loop through array
                $this->store_comment_error($field_name,$message); // store error messages and field name in wp error
            }
        }

        // check for errors
        // if there are errors then execute the wp_die function, which then calls the 'comment_err_die_handler'
        if(count($this->comment_err->get_error_messages()) > 0){  // comment form has errors
            wp_die($this->comment_err, 'Comment Form Error');  // pass the error object to wp_die, which will execute the custom wp_die_handler
        } else { // no errors
            // pass comment data on to wp-comments-post.php to attempt to save the comment
            return $commentdata;
        }
    } // END validate_comment_formfields

    /**
     * Use get_comment_err_die_handler intermediary function in case there is a need to remove this filter with remove_filter()
     *
     * @since 1.0
     */
    public function get_comment_err_die_handler(){return array($this,'comment_err_die_handler');}

    /**
     * This err handler function checks for a wp_error object that has code = 'comment_err',
     * then stores the post data and error in a session
     * then redirects the user back to the original post
     * then die() to prevent remainder of script on wp-comments-post.php from executing
     *
     * If user has cookies disabled, attempt to pass session ID in GET
     * If user has cookies disabled and php configured for cookies only sessions, then load error template
     * Custom error template can use $comment_post_id or $redirect_url to build links back to the post
     * If plug-in cannot find custom error template then use WordPress default error display
     *
     * @since 1.0
     * @param    object    $message    WP_Error object
     * @param    string    $title      Default title for the page, passed on to the WordPress _default_wp_die_handler()
     * @param    array     $args       passed on to the WordPress _default_wp_die_handler()
     */

    public function comment_err_die_handler($message, $title='Comment Form Errors', $args=array()) {
        // check for comment_err error code
        if(is_wp_error($message) && $message->get_error_code() == $this->error_code){  // is a comment error
            // save HTTP POST values from form and wp_error object in session
            $_SESSION[$this->session_form_data] = $_POST;  // copy post data to sessoin variable
            $_SESSION[$this->session_wp_error] = $message; // store wp_error object in session

            $this->comment_post_id = $_POST['comment_post_ID'];
            $post_url = get_permalink($this->comment_post_id); // 'comment_post_ID' field from comment form contains original post id
            $redirect_url = $post_url;

            // cookies disabled but PHP can support passing session id with GET
            if(!isset($_COOKIE['PHPSESSID']) && ini_get('session.use_only_cookies') == 0){
                $redirect_url = $post_url . '?' . htmlspecialchars(SID); // add session id to url
            }

            // allow user defined filter to change url before redirect
            if(has_filter($this->get_redirect_url)){
                $redirect_url = apply_filters($this->get_redirect_url, $redirect_url);
            }

            // redirect to original post
            // check for session support
            // when session is disabled show custom error template or use WordPress default error display
            if(isset($_COOKIE['PHPSESSID'])){ // php using cookies for session id
                wp_safe_redirect($redirect_url); // redirect to original post, session id in COOKIE
            } else if(ini_get('session.use_only_cookies') == 0){ // cookies disabled, php supports session id passed in GET
                wp_safe_redirect($redirect_url); // pass session id through GET

                // session is disabled because cookies disabled, and php configured to only use cookies for session, no support for GET
                // display comment error in custom template or WordPress default error display
            } else {
                _default_wp_die_handler($message,$title, $args); // allow WordPress to use default message display instead
            }

            die(); // end script to prevent remainder of wp-comments-post.php from executing and saving the comment
        } else { // not a comment_err, use default wordpress error message display
            _default_wp_die_handler($message,$title, $args); // use default message page instead
        }
    } // END get_comment_err_die_handler

    /**
     * This function copies session variable with form data into $_POST
     * use 'template_redirect' action to call this function
     *
     * @since 1.0
     */
    public function convert_values_from_session(){
        if(comments_open()){ // any content that allows comments
            global $post;
            $current_post_id = $post->ID; // get the post id of the current page or post
            $stored_post_id = $_SESSION[$this->session_form_data]['comment_post_ID']; // get the post id stored in session

            // form has been submitted and redirect to correct post
            if(isset($_SESSION[$this->session_form_data]) && $stored_post_id == $current_post_id) {
                $_POST = $_SESSION[$this->session_form_data]; // copy data back into _POST array
                unset($_SESSION[$this->session_form_data]); // delete the session variable

                // user may have navigated to another post without correcting comment form
                // session data carries over to all pages and may display error from a previous comment form on the newly viewed post
                // delete any previous session data stored by this script to avoid displaying error from another post
            } else {
                // delete the session variables
                unset($_SESSION[$this->session_form_data]);
                unset($_SESSION[$this->session_wp_error]);
            }
        }
    } // END convert_values_from_session


    /**
     * returns the error object from the $_SESSION variable or false if the variable does not exist or is not a error object
     *
     * @since 1.0
     * @return WP_error object or false
     */
    public function get_comment_form_error_obj(){
        if(isset($_SESSION[$this->session_wp_error]) && is_wp_error($_SESSION[$this->session_wp_error])){
            return $_SESSION[$this->session_wp_error];
        } else {
            return false;
        }
    } // END get_comment_form_error_obj

    /**
     * combine error object arrays into a single associative array with $_POST field name as index and error message as value
     * return empty array if there are no errors
     * can also be used as a Template tag, using apply_filters('get_comment_form_error_array','');
     *
     * @since 1.0
     * @return 		array 	associative array with $_POST field name as index and error message as value or empty array
     */
    public function get_comment_form_error_array(){
        $error_obj = $this->get_comment_form_error_obj();
        if($error_obj == false) { return array(); } // return empty array if there are no errors

        // create associative array from fiel names as index and error message as value
        $error_array = array_combine($error_obj->get_error_data($this->error_code),$error_obj->get_error_messages());

        return $error_array;
    } // END get_comment_form_error_array

    /**
     * returns an HTML formatted list of errors or empty string if no errors
     * style sheet class multiple errors 'ul.comment-form-errors', or for one error 'ul.comment-form-errors comment-form-single-error'
     * can also be used as a Template tag

     * $user_args = array( 		'type' 					=> 'ul', // default to 'ul'
    'class' 				=> 'comment-form-errors',
    // add selector when only one error present
    'single-err-selector' 	=> 'comment-form-single-error',
    // function appends field name to end of prefix so each li has a unique class
    'li-class-prefix' 		=> 'comment-form-',
    'before-list' 			=> '',
    'after-list' 			=> '',
    );

     * @since 1.0
     * @param		array 		$user_args 		associative array that sets the formatting of the HMTL list
     * @return 		string 						HTML formatted list of errors or empty string for no errors
     */

    public function get_comment_form_errors_as_list($user_args = array()){
        $default_args = array( 	'type' 					=> 'ul', // default to 'ul'
            'class' 				=> 'comment-form-errors',
            // add selector when only one error present
            'single-err-selector' 	=> 'comment-form-single-error',
            // function appends field name to end of prefix so each li has a unique class
            'li-class-prefix' 		=> 'comment-form-error-',
            'before-list' 			=> '',
            'after-list' 			=> '',
        );

        // if auto_display_errors is set, then add a message before the list and enclose in <div> tags with
        // div class="comment-form-error-box"
        if(!is_null($this->config_options['auto_display_errors'])){
            $default_args['before-list'] = '<div class="comment-form-error-box"><!-- wp inline errors -->' . "\n"; // open div
            // initial text of error message
            $default_args['before-list'] .= sprintf(__('%1$sPlease correct the following problems:%2$s','cm_translate'),'<span>','</span>');
            // close the div
            $default_args['after-list'] = '</div><!-- end comment-form-error-box -->';
        }

        // overwrite default values with user values, if user_args is not empty
        $list_args = (empty($user_args)) ? $default_args : array_merge($default_args, $user_args);

        // get the errors as an associative array or empty array
        $errors = $this->get_comment_form_error_array();
        if(empty($errors)){ return ''; } // return '' empty string, no errors

        if(count($errors) > 0){ // has errors
            $list_elements = '';
            foreach($errors as $field_name => $message){ // loop through associative array
                $li_class = $list_args['li-class-prefix'] . $field_name; // build unique class for each field error
                $list_elements .= "\n\t\t<li class=\"" . $li_class . '">' . $field_name . '</li>'; // build each li element
            }

            // build css class
            $css_class = (count($errors) == 1) ? $list_args['class'] . ' ' . $list_args['single-err-selector'] : $list_args['class'];

            // create error list
            $error_html = "\n\t<" . $list_args['type']  . ' class="' . $css_class . '">' . $list_elements .
                "\n\t</" . $list_args['type'] . ">\n"; // format list

            // add before string
            if(!empty($list_args['before-list'])){
                $error_html = $list_args['before-list'] . "\n" . $error_html;
            }

            // append after string
            if(!empty($list_args['after-list'])){
                $error_html = $error_html  . "\n" . $list_args['after-list'];
            }
        } else { // no errors
            $error_html = ''; // return empty string
        }

        return $error_html;
    } // END get_comment_form_errors_as_list


} // END class WP_Inline_Comment_Errors
?>