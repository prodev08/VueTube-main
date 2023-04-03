<?php 
/**
 * Logout of our user using credentials from firebase.
 * 
 * For now we are just removing out accessToken from the DB. Without access token we are assuming the user is logged out
 */

require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")));

if(empty($content->googleUser) || ! $content->googleUser->uid || ! $content->googleUser->accessToken ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$result = $User->logout($content->googleUser->uid);

if ($result) {
	echo json_encode(['success' => 'User Logged Out']);
	exit;
}

echo json_encode(array('error' => 'Unable to logout'));
exit;