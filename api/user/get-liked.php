<?php 
/**
 * Return the liked videos of a particular user
 */

require_once('../api-setup.php');

if(empty($_GET['token'])){
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$user_id = $User->get_user_id_by_token($_GET['token']);

if ($user_id) {

	$videos = $User->get_liked($user_id, $_GET);

	echo json_encode($videos);
	exit;

} else {
	echo json_encode(array('error' => 'Invalid Token'));
	exit;
}