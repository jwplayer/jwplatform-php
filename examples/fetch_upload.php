<?php

require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$params = array();
$params['title'] = 'Fetch Upload Test';
$params['description'] = 'Use the JW Platform API to upload videos';
$params['download_url'] = 'Full URL to the video I want to upload';

$response = json_encode($jwplatform_api->call('/videos/create', $params));

print_r($response);

?>
