<?php 
/**
 * Get videos related to a partcular channel.
 * 
 * TODO: Update this to query by style / topic
 */
require_once('../api-setup.php');

if(empty($_GET['video_id']) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$video = $Video->get_video_info($_GET['video_id'], 'channel_id');

$channel_ids = $Channel->get_related_channels($video->channel_id);

if(! empty($channel_ids)){

	$videos = $Video->get_videos_in_channels($channel_ids);

	echo json_encode($videos);
	exit;
}


echo json_encode([]);
exit;