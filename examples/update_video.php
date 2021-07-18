<?php

// require_once('./init.php');
require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformClient('INSERT API SECRET');
$media_id = 'INSERT MEDIA ID';
$site_id = 'INSERT SITE ID';

# If the form has been submitted, we place all properties in an array and save it.
if(isset($_POST['media_id'])) {
    $params = array();
    $params['metadata'] = array();
    foreach ($_POST as $key => $value) {
        $params['metadata'][$key] = $value;
	}
    $response = json_encode($jwplatform_api->Media->update($site_id, $media_id, $params));
    $media = json_decode(json_decode(trim($response), true), true);
    if (array_key_exists('errors', $media)) {
        die(print_r($media));
    }
}

# Grab the current properties for this video, so it can be printed in the form.
$response = json_encode($jwplatform_api->Media->get($site_id, $media_id));
$media = json_decode(json_decode(trim($response), true), true);
if (array_key_exists('errors', $media)) {
    die(print_r($media));
}

# Now print the form.
?>


<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<title>Updating video properties</title>
	<style type="text/css">
        body {
        	margin: 10px;
        	padding: 0;
        	color: #000;
        	font: 12px/16px Arial, sans-serif;
        }
        h2 {
        	margin-top: 50px;
        }
        small {
        	font-style: italic;
        	font-size: 12px;
        	color: #333;
        }
        fieldset {
        	background-color: #eee;
        	padding: 8px 0 8px 10px;
        	border:0;
        	margin-bottom: 1px;
        }
        input, select, button, textarea {
        	margin: 5px 0;
        	display: block;
        	float: left;
        	width: 480px;
        	border: 2px solid #CCC;
        	padding: 3px 6px;
        }
        textarea {
            height: 60px;
        }
        label {
        	line-height: 30px;
        	display: block;
        	float: left;
        	clear: left;
        	width: 120px;
        }
        button, fieldset small {
        	display: block;
        	clear: both;
        	margin-left: 120px;
        }
        a {
        	color: #1369bf;
        	margin-left: 10px;
        }
    </style>

</head>
<body>


<form method="post" action="./">
	<fieldset>
		<input type="hidden" name="video_key" value="<?=$media['id']?>" />
		<label>Title</label>
		<input type="text" name="title" value="<?=$media['metadata']['title']?>" />
		<label>Description</label>
		<textarea name="description"><?=$media['metadata']['description']?></textarea>
	</fieldset>
	<fieldset>
		<button type="submit">save changes</button>
	</fieldset>
</form>

<p><a href="http://content.jwplatform.com/previews/<?=$media['id']?>-ALJ3XQCI" target="_blank">Preview this video</a></p>


</body>
</html>
