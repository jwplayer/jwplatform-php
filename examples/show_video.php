<?php

require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

# Do the API call to retrieve the video status
$video_key = $_GET['video_key'];
$response = $jwplatform_api->call('/videos/show', array('video_key'=>$video_key));
if ($response['status'] == 'error') { die(print_r($response)); }


# Print the page. We use a refresh of 10 seconds to see if the video is ready.
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <title>Video uploaded</title>
    <link media="all" href="css/style.css" type="text/css" rel="stylesheet">
    <meta http-equiv="refresh" content="10">
</head>
<body>


<form>
    <div id="preview">
        <a href="http://content.jwplatform.com/previews/<?php echo $video_key?>-ALJ3XQCI"
            style="background:url(http://content.jwplatform.com/thumbs/<?php echo $video_key?>-120.jpg)"
           target="_blank">preview video</a>
    </div>
    <fieldset>

<?php
# Print all the properties
foreach($response['video'] as $key =>$val) {
    if($val) {
        echo "<label>".$key."</label><p><strong>".$val."</strong></p>\n";
    }
}
?>

    </fieldset>
</form>
<p><a href="./">Upload another video</a></p>

</body>
</html>
