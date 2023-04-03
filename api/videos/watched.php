<?php 
/**
 * Save a video as liked by the user
 */

require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")));

if(empty($content->video_id) || ! is_numeric($content->video_id)
|| empty($content->channel_id) || ! is_numeric($content->channel_id) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

if(! empty($content->googleUser->uid)){
	$user_id = $User->get_user_id_by_uid($content->googleUser->uid);

	if ($user_id) {
		$result = $User->save_history($content->video_id);
	}
}

$Video->record_view($content->video_id);

$Channel->record_view($content->channel_id);

// NOTE: Not really caring if this is successful atm.
echo json_encode(['success' => 1]);
exit;