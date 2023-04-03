<?php 
require_once('../api-setup.php');

$videos = $Video->search($_GET, $DEFAULT_VID_LIMIT);

echo json_encode($videos);
exit;