<?php
/**
 * WARNING:
 * We expose the /videos/create API call using this file.
 *
 * Doing so is not recommended, as it allows anybody to upload videos to
 * your account. Therefore, it is important to add an authentication
 * check here.
 */

require_once('jwplatform/api.php');

use jwplayer\jwplatform as jwplatform;

$jwplatform_api = new jwplatform\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$params = array();
if(isset($_GET['resumable'])) {
  $params['resumable'] = 'True';
}

// Do the API call and send the result back to the client.
echo json_encode($jwplatform_api->call('/videos/create', $params));
?>
