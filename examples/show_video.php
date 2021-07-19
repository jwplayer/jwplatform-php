<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformClient('INSERT API SECRET');
$media_id = 'INSERT MEDIA ID';
$site_id = 'INSERT SITE ID';

# Do the API call to retrieve the video status
$response = json_encode($jwplatform_api->Media->get($site_id, $media_id));
$media = json_decode(json_decode(trim($response), true), true);
if (array_key_exists('errors', $media)) {
    die(print_r($media));
}


# Print the page. We use a refresh of 10 seconds to see if the video is ready.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Video Example</title>
</head>
<body>


<form>
    <div id="preview">
        <a href="http://content.jwplatform.com/previews/<?=$media_id?>-ALJ3XQCI"
            style="background:url(http://content.jwplatform.com/thumbs/<?=$media_id?>-120.jpg)"
           target="_blank">preview video</a>
    </div>
    <fieldset>

<?php
# Print all the properties
foreach($media['metadata'] as $field => $value) {
    echo "<label>".$field."</label><p><strong>".$value."</strong></p>\n";
}
?>

    </fieldset>
</form>

</body>
</html>
