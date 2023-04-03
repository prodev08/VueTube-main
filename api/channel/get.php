<?php 

require_once('../api-setup.php');

if(empty($_GET['channel_id']) 
&& empty($_GET['youtube_id'])) {
	echo json_encode(array('error'=> 'Missing Information'));
	exit; 
}

if(! empty($_GET['channel_id']) ) {
	$channel = $Channel->get_channel_by_channel_id($_GET['channel_id']);
}else if(! empty($_GET['youtube_id'])){
	$channel = $Channel->get_channel_by_yt_id($_GET['youtube_id']);
}


echo json_encode($channel);
exit;