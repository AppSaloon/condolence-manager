<?php

namespace appsaloon\cm\controller;

use appsaloon\cm\register\Custom_Post_Type;

class Comment_Sanitizer {
	public function __construct() {
		add_filter( 'preprocess_comment', array($this, 'validate_comment_formfields'), 1, 1);
	}

	public function validate_comment_formfields( $comment ) {
		if ( get_post_type( $comment['comment_post_ID'] ) == Custom_Post_Type::post_type() ) {
			$comment['comment_author'] = sanitize_text_field($comment['comment_author'] ?? '');
			$comment['comment_author_email'] = sanitize_email($comment['comment_author_email'] ?? '');
			$comment['comment_author_url'] = sanitize_text_field($comment['comment_author_url'] ?? '');
			$comment['comment_content'] = sanitize_text_field($comment['comment_content'] ?? '');
		}
		return $comment;
	}
}