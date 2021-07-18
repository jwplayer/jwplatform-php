<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

// set these environment variables
$secret = $_ENV['JWPLATFORM_API_SECRET'];
$site_id = $_ENV['JWPLATFORM_SITE_ID'];

$jwplatform_api = new Jwplayer\JwplatformClient($secret);

$target_file = 'examples/test.mp4';
$params = array();
$params['metadata'] = array();
$params['metadata']['title'] = 'PHP API Test Upload';
$params['metadata']['description'] = 'Video description here';
$params['upload'] = array();
$params['upload']['method'] = 'direct';

// Create the example media
$create_response = json_encode($jwplatform_api->Media->create($site_id, $params));

print_r($create_response);
print("\n");

$decoded = json_decode(json_decode(trim($create_response), true), true);
$upload_link = $decoded['upload_link'];

// Upload the media file
$upload_response = $jwplatform_api->Media->upload($target_file, $upload_link);

print_r($upload_response);
print("\n");

?>
