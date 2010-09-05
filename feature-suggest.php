<?php
/*
Plugin Name: Feature Suggest
Description: Allow anyone to submit and vote on feature requests using an AJAX rating system and custom post types. Based on Tutorialzine's <a href="http://tutorialzine.com/2010/08/ajax-suggest-vote-jquery-php-mysql/">Feature Suggest App</a> tutorial.
Author: Sailbird Media
Version: 0.91
Author URI: http://sailbirdmedia.com
*/

$textdomain = 'fs';
if ( ! defined( 'ABSPATH' ) ) die( __("Can't load this file directly", $textdomain ) ); 

require_once('classes.php'); // the helper classes
require_once('ajax.php'); // the ajax handlers
require_once('cpt.php'); // the custom post type declaration

// initialization function
// takes care of internationalization, javascript and css files
function fs_init() {
	global $textdomain;
	// load the plugin textdomain to allow internationalization
	$textdomain_path = plugin_basename( dirname( __FILE__ ) . '/translations' );
	load_plugin_textdomain( $textdomain, '', $textdomain_path );

	// embed the javascript files
	wp_enqueue_script( 'fs-infield-label', plugin_dir_url( __FILE__ ) . 'js/jquery.infieldlabel.js', array( 'jquery' ), '');
	wp_enqueue_script( 'fs-global-javascript', plugin_dir_url( __FILE__ ) . 'js/global.js', array( 'jquery' ), '', true );

	// declare the URL to the file that handles the AJAX request (wp-admin/admin-ajax.php)
	wp_localize_script( 'fs-global-javascript', 'FsAjax', array( 
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'fsNonce' => wp_create_nonce( 'fsajax-nonce' ),
		)
	);

	// register our stylesheet
	wp_register_style( 'fs-stylesheet', plugin_dir_url( __FILE__ ) . 'css/styles.css');
	wp_enqueue_style( 'fs-stylesheet' );
	
	// and create the custom post type
	fs_create_cpt();
}

// hook in the initialization code
add_action('init', fs_init);

// this function adds the [featuresuggest] shortcode
$shortcode = 'featuresuggest';
function fs_shortcode($atts, $content = null) {
	$atts = shortcode_atts(array(
		'num' => '10',
		), $atts);
	return fs_display($atts);
}

// hook in the shortcode
add_shortcode($shortcode, 'fs_shortcode');

// this function outputs the plugin markup, including the submit form
function fs_display($atts) { ?>

<!-- The generated suggestion list comes here -->
<ul class="suggestions">
<?php
$ip = sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));

$suggestion_query = new WP_Query('posts_per_page='.$atts['num'].'&post_type=suggestion&meta_key=rating&orderby=meta_value_num');

// a loop
if ( $suggestion_query->have_posts() ) : while ( $suggestion_query->have_posts() ) : $suggestion_query->the_post();
global $post;

// check if user voted on this suggestion
$vote = new Vote(array( 'id' => $post->ID, 'ip' => $ip ) );

$suggestion = new Suggestion(array(
	'id' => $post->ID,
	'title' => $post->post_title,
	'has_voted' => $vote->exists(),
	'permalink' => get_permalink($post->ID),
	'rating' => $vote->rating
	)
);

echo (string)$suggestion;

endwhile; else:

endif;

// reset query
wp_reset_query();
?>
</ul>

<form id="suggest" action="" method="post">
	<p><label for="suggestionTitle"><?php _e('Suggestion title', 'fs'); ?></label>
		<input name="suggestionTitle" type="text" id="suggestionTitle" class="rounded" autocomplete="off" /></p>
	<p><label for="suggestionContent" class="suggestionContent-label"><?php _e('Please describe your suggestion some...', 'fs'); ?></label>
		<textarea name="suggestionContent" id="suggestionContent"></textarea></p>
	<p><input type="submit" value="Submit" id="submitSuggestion" /></p>
</form>

<?php
}