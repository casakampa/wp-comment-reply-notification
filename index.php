<?php
/**
 * Plugin Name: WP Comment Reply Notification
 * Description: Sends an email to the author of a comment when someone posts a reply. If the author of a posts replies to its own post, no reply will be send.
 * Version:     2.0
 * License:     GPLv2
 */
 
add_filter( 'wp_mail_from', function( $from_email ) {
return 'wordpress@example.com';
});

add_filter( 'wp_mail_from_name', function( $from_name ) {
return 'Name of the website';
});

// Set Content Type for this email to text/html
add_filter( 'wp_mail_content_type', function( $content_type ) {
return 'text/html';
});

// Set Mail Charset for this email to UTF-8
add_filter( 'wp_mail_charset', function( $charset ) {
return 'UTF-8';
});

add_action( 'wp_set_comment_status', 'mvdk_comment_notification', 99, 2 );

function mvdk_comment_notification( $comment ){
	$commentdata = get_comment( $comment );
		
	if( $commentdata->comment_approved == '1' && $commentdata->comment_parent > 0 ){
		$parent = get_comment( $commentdata->comment_parent );
			if( $parent->comment_author_email != $commentdata->comment_author_email ){ // Not the same author!

			$subject = 	'[' . get_bloginfo() . '] ' . esc_html__('Er is een reactie geplaatst op jouw bericht','mvdk');
			$mailcontent =	'<p>' . esc_html__('Hallo','mvdk') . ' ' . $parent->comment_author . ',</p>
					<p>' . $commentdata->comment_author . ' ' . esc_html__('heeft gereageerd op jouw bericht', 'mvdk' ) . '.</p>
					<p>' . esc_html__('Bekijk de reactie en reageer','mvdk') . ': <a href="' . get_comment_link( $parent->comment_ID ) . '">' . get_comment_link( $parent->comment_ID ) . '</a></p>
					<p>Met vriendelijke groet,<br>Name of the sender</p>';

				wp_mail( $parent->comment_author_email, $subject, $mailcontent );
			}
		return false;
	}
}