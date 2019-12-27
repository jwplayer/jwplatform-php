<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

// set these environment variables
$jwplatform_api_key = $_ENV['JWPLATFORM_API_KEY'];
$jwplatform_api_secret = $_ENV['JWPLATFORM_API_SECRET'];

$jwplatform_api = new Jwplayer\JwplatformAPI($jwplatform_api_key, $jwplatform_api_secret);

$target_file = 'examples/test.mp4';
$params = array();
$params['title'] = 'PHP API Test Upload';
$params['description'] = 'Video description here';

// Create the example video
$create_response = json_encode($jwplatform_api->call('/videos/create', $params));

print_r($create_response);

$decoded = json_decode(trim($create_response), TRUE);
$upload_link = $decoded['link'];

$upload_response = $jwplatform_api->upload($target_file, $upload_link);

print_r($upload_response);

?>
