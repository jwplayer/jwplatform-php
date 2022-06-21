<?php

require_once('vendor/autoload.php');

function get_parts_count($file) {
    // 5 MB is the minimum size of an upload part, per https://developer.jwplayer.com/jwplayer/docs/stream-multipart-resumable-upload
    $minimum_part_size = 5 * 1024 * 1024; 
    $size = filesize($file);
    return (floor($size / $minimum_part_size) + 1);
}

$secret = $_ENV('JWPLATFORM_API_SECRET');
$site_id = $_ENV('JWPLATFORM_SITE_ID');

$jwplatform_api = new Jwplayer\JwplatformClient($secret);

$target_file = 'examples/test.mp4';
$params = array();
$params['metadata'] = array();
$params['metadata']['title'] = 'PHP API Test Upload';
$params['metadata']['description'] = 'Video description here';
$params['upload'] = array();
$params['upload']['method'] = 'multipart';

$create_response = json_encode($jwplatform_api->Media->create($site_id, $params));

print_r($create_response);
print("\n");

$decoded = json_decode(json_decode(trim($create_response), true), true);
$upload_id = $decoded['upload_id'];
$upload_token = $decoded['upload_token'];
$media_id = $decoded['id'];

$parts_count = get_parts_count($target_file);
$upload_parts_url = "https://api.jwplayer.com/v2/uploads/$upload_id/parts?page=1&page_length=$parts_count";
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => $upload_parts_url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer ' . $upload_token,
    ),
));

$upload_parts_response = curl_exec($curl);
curl_close($curl);

$upload_parts_response_object = json_decode($upload_parts_response);
print_r($upload_parts_response_object);
print("\n");

$upload_url_parts = $upload_parts_response_object->parts;
// 5 MB is the minimum size of an upload part, per https://developer.jwplayer.com/jwplayer/docs/stream-multipart-resumable-upload
$buffer = (1024 * 1024) * 5; 
if (count($upload_url_parts) > 0) {
    $file_handle = fopen($target_file, 'rb');
    foreach($upload_url_parts as $upload_url_parts_key => $upload_url_parts_value) {
        $upload_bytes_url = $upload_url_parts_value -> upload_link;
        $file_part = fread($file_handle, $buffer);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $upload_bytes_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $file_part);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:'
        ));
        $upload_bytes_response = curl_exec($curl);
        print_r($upload_bytes_response);
        print("\n");
    }
    fclose($file_handle);

    $complete_upload_url = "https://api.jwplayer.com/v2/uploads/$upload_id/complete";
    $curl = curl_init();
    curl_setopt_array($curl, array(
        CURLOPT_URL => $complete_upload_url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'PUT',
        CURLOPT_HTTPHEADER => array(
            'Authorization: Bearer ' . $upload_token,
        ),
    ));
    $complete_upload_response = curl_exec($curl);
    curl_close($curl);
    $complete_upload_response_object = json_decode($complete_upload_response);
    print_r($complete_upload_response_object);
    print("\n");
}

?>