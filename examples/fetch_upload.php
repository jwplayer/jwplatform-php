<?php

require_once('jwplatform/api.php');

use jwplayer\jwplatform as jwplatform;

$jwplatform_api = new jwplatform\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$params = array();
$params['title'] = 'Fetch Upload Test';
$params['description'] = 'Use the JW Platform API to upload videos';
$params['download_url'] = 'Full URL to the video I want to upload';

$response = json_encode($jwplatform_api->call('/videos/create', $params));

print_r($response);

?>
