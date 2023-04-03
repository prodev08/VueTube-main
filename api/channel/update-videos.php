<?php 

require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")), true);

if(empty($content['channel']['channel_id']) || empty($content['channel']['youtube_id']) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$youtube_id = $content['channel']['youtube_id'];
$channel_id = $content['channel']['channel_id'];

$items = $Channel->get_channel_videos($youtube_id, 10);

$videos = $Video->channel_items_to_videos($items, $channel_id);

if(! empty($videos)){

	$Video->insert_videos($videos);

	// for now we are just assuming things went as expected.
	echo json_encode(array('success' => 'Videos Updated!'));
	exit;
}


echo json_encode(array('error' => 'Unable to add channel!'));
exit;