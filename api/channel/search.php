<?php 
require_once('../api-setup.php');

$channels = $Channel->search($_GET, $DEFAULT_VID_LIMIT);

echo json_encode( $channels, JSON_UNESCAPED_UNICODE );
//echo json_last_error_msg();
exit;
