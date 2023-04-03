<?php 
/**
 * Get a list of videos from a single channel based off of that channel's id
 */
require_once('../api-setup.php');

if(empty($_GET['channel_id']) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$videos = $Video->get_video_list($_GET, $DEFAULT_VID_LIMIT);

echo json_encode($videos);
exit;