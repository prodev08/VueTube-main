<?php 
/**
 * Login in our user using credentials from firebase.
 * 
 * Our sessions are permanent so accessToken should be good until the user signs out through firebase. When they log in again we should have a new access token
 */

require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")));

if(empty($content->googleUser) || ! $content->googleUser->uid
|| ! $content->googleUser->email || ! $content->googleUser->refreshToken
|| ! $content->googleUser->accessToken || ! $content->googleUser->idToken ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$user_id = $User->get_user_id_by_uid($content->googleUser->uid);

if(! $user_id){
	$result = $User->insert_user($content->googleUser);
}else{
	$result = $User->update_user($content->googleUser);
}

if ($result) {
	echo json_encode(['success' => 'User Logged In']);
	exit;
}

echo json_encode(array('error' => 'Unable to login'));
exit;