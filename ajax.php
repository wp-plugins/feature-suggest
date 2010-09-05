<?php
// this function is the AJAX entry point
function fs_ajax_request() {
	// verify the nonce
	$nonce = $_GET['fsNonce'];
	if ( ! wp_verify_nonce( $nonce, 'fsajax-nonce' ) ) die ( 'Busted!' );

	// then call the appropiate method
	$type = $_GET['type'];
	switch ( $type )
	{
		case 'suggest':
			fs_ajax_suggest();
		break;
		case 'vote':
			fs_ajax_vote();
		break;
		default:
			die( 'Busted!' );
		break;
	}
}
// both logged in and not logged in users can send these AJAX requests
add_action( 'wp_ajax_nopriv_fs_ajax', 'fs_ajax_request' );
add_action( 'wp_ajax_fs_ajax', 'fs_ajax_request' );

// this function handles the ajax request to add a new suggestion
function fs_ajax_suggest() {
	// sanity check the title and content
	$title = htmlspecialchars(strip_tags($_GET['title']));
	if(mb_strlen($title,'utf-8')<3) exit;

	$content = htmlspecialchars(esc_attr($_GET['content']));

	// create the Suggestion object and save the post
	$suggestion = new Suggestion( array( 'title' => $title, 'content' => $content ) );
	$suggestion->save();

	// generate the response
	$response = json_encode(array(
		'html'	=> (string)($suggestion)
	));

	// response output
	header( "Content-Type: application/json" );
	echo $response;

	exit;
}

// this function handles the ajax request to vote
function fs_ajax_vote() {
	// get the parameters
	$v = (int)$_GET['vote'];
	$id = (int)$_GET['id'];
	$ip = sprintf('%u',ip2long($_SERVER['REMOTE_ADDR']));

	// sanity check the vote value
	if($v != -1 && $v != 1){ exit; }

	// checking to see whether such a suggest post exists
	$s = get_post($id);
	if(!$s) { exit; }

	// create the Vote object and check if the current user has voted
	$vote = new Vote(array(	'v'  => $v, 'id' => $id, 'ip' => $ip ) );

	// update the vote Meta fields and save the vote nonce
	if( ! $vote->exists() ) {
		$vote->updateMeta();
		$vote->save();
	}

	exit;
}

?>