<?php

require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$target_file = 'examples/test.mp4';
$params = array();
$params['title'] = 'PHP API Test Upload';
$params['description'] = 'Video description here';

// Create the example video
$create_response = json_encode($jwplatform_api->call('/videos/create', $params));

print_r($create_response);

$decoded = json_decode(trim($create_response), TRUE);
$upload_link = $decoded['link'];

$upload_response = $jwplatform_api->upload($upload_link, $target_file);

print_r($upload_response);

?>
