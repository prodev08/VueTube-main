<?php 

require_once('../api-setup.php');

$channels = $Channel->get_channel_list();

if($channels){
	echo json_encode(array('success' => 'Found Channels', 'channels' => $channels));
	exit;
}

echo json_encode(array('error' => 'Unable to list channels!'));
exit;