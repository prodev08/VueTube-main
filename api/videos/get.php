<?php 

require_once('../api-setup.php');

if(empty($_GET['youtube_id']) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

if($_GET['token']){
	$user_id = $User->get_user_id_by_token($_GET['token']);

	$video = $Video->get_video_with_user_info($_GET['youtube_id'], $user_id);
}

if(empty($video)){
	$video = $Video->get_video_by_yt_id($_GET['youtube_id']);
}

echo json_encode($video);
exit;