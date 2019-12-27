<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

// set these environment variables
$jwplatform_api_key = $_ENV['JWPLATFORM_API_KEY'];
$jwplatform_api_secret = $_ENV['JWPLATFORM_API_SECRET'];
$reporting_api_key = $_ENV['JWPLATFORM_REPORTING_API_KEY'];

$jwplatform_api = new Jwplayer\JwplatformAPI($jwplatform_api_key, $jwplatform_api_secret, $reporting_api_key);

$params = array();
$params['start_date'] = '2019-12-01';
$params['end_date'] = '2019-12-31';
$params['dimensions'] = array('device_id');
$params['include_metadata'] = 1;
$params['metrics'] = array(array('operation' => 'sum', 'field' => 'embeds'));
$params['sort'] = array(array('field' => 'embeds', 'order' => 'DESCENDING'));

// Query analytics
$response = json_encode($jwplatform_api->call('/sites/'.$jwplatform_api_key.'/analytics/queries', $params, 'v2'));
$decoded = json_decode(trim($response), TRUE);

print_r($decoded);

?>
