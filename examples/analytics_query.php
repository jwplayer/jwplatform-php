<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

// set these environment variables
$secret = $_ENV['JWPLATFORM_API_SECRET'];
$site_id = $_ENV['JWPLATFORM_SITE_ID'];

$jwplatform_api = new Jwplayer\JwplatformClient($secret);

$params = array();
$params['start_date'] = '2019-12-01';
$params['end_date'] = '2019-12-31';
$params['dimensions'] = array('device_id');
$params['include_metadata'] = 1;
$params['metrics'] = array(array('operation' => 'sum', 'field' => 'embeds'));
$params['sort'] = array(array('field' => 'embeds', 'order' => 'DESCENDING'));

// Query analytics
$response = json_encode($jwplatform_api->analytics->query($site_id, $params));
$decoded = json_decode(trim($response), TRUE);

print_r($decoded);

?>
