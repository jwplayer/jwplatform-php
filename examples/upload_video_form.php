<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

// set these environment variables
$jwplatform_api_key = 'APIKEY';
$jwplatform_api_secret = 'APISECRET';

$jwplatform_api = new Jwplayer\JwplatformAPI($jwplatform_api_key, $jwplatform_api_secret);

$params = array();
$params['title'] = 'PHP API Test Upload';
$params['description'] = 'Video description here';

// Create the example video
$create_response = json_encode($jwplatform_api->call('/videos/create', $params));

$decoded = json_decode(trim($create_response), TRUE);
$upload_link = $decoded['link'];

$url = 'https://' . $upload_link['address'] . $upload_link['path'] .
"?key=" . $upload_link['query']['key'] . '&token=' . $upload_link['query']['token'] .
"&api_format=xml";

?>
​
<form enctype="multipart/form-data" action=<?php echo $url?> method="POST">
    Send this file: <input name="file" type="file" />
    <input type="submit" value="Send File" />
</form>