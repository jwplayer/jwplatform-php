<?php

require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformClient('INSERT API SECRET');
$site_id = 'INSERT SITE ID';

$params = array();
$params['metadata'] = array();
$params['metadata']['title'] = 'Fetch Upload Test';
$params['metadata']['description'] = 'Use the JW Platform API to upload videos';
$params['upload'] = array();
$params['upload']['method'] = 'fetch';
$params['upload']['download_url'] = 'Full URL to the video I want to upload';

$response = json_encode($jwplatform_api->Media->create($site_id, $params));

print_r($response);

?>
