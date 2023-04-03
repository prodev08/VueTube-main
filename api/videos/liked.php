<?php 
/**
 * Save a video as liked by the user
 */
require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")));

if(empty($content->googleUser->uid)
|| empty($content->video_id) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$user_id = $User->get_user_id_by_uid($content->googleUser->uid);

if ($user_id) {
	$result = $User->toggle_like_video($content->video_id);

	if($result){
		echo json_encode(array('success' => 'Video Liked!'));
	}else{
		echo json_encode(array('error' => 'Unable to like video!'));
	}
	exit;
}else{
	echo json_encode(array('error' => 'Invalid Token'));
	exit;
}