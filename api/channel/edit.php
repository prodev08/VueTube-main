<?php 

require_once('../api-setup.php');

$content = json_decode(trim(file_get_contents("php://input")), true);

if(empty($content['channel']['title']) || empty($content['channel']['youtube_id']) ) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit;
}

$channel_id = $Channel->update_channel($content['channel']);

// If this channel already exists let's just return that channel_id and call it done
if($channel_id){
	echo json_encode(array('success' => 'Channel Updated!'));
	exit;
}



echo json_encode(array('error' => 'Unable to add channel!'));
exit;