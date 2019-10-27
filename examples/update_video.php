<?php

require_once('vendor/autoload.php');

$jwplatform_api = new Jwplayer\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

# Insert the key of the video to update.
$video_key = 'INSERT VIDEO KEY';

# If the form has been submitted, we place all properties in an array and save it.
# We only push properties that have a value, and we convert the date to a timestamp.
if(isset($_POST['video_key'])) {
	$array = array();
	foreach ($_POST as $key => $value) {
		if($key == 'date') {
			$date = explode('/',$value);
			$array[$key] = mktime(0,0,0,$date[1],$date[0],$date[2]);
		} else {
			$array[$key] = $value;
		}
	}
	$response = $jwplatform_api->call("/videos/update",$array);
	if ($response['status'] == "error") { die(print_r($response)); }
}


# Grab the current properties for this video, so it can be printed in the form.
$response = $jwplatform_api->call("/videos/show",array('video_key'=>$video_key));
if ($response['status'] == "error") { die(print_r($response)); }


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
		<input type="hidden" name="video_key" value="<?=$response['video']['key']?>" />
		<label>Title</label>
		<input type="text" name="title" value="<?=$response['video']['title']?>" />
		<label>Tags</label>
		<input type="text" name="tags" value="<?=$response['video']['tags']?>" />
		<small>Separate multiple tags with a comma.</small>
		<label>Description</label>
		<textarea name="description"><?=$response['video']['description']?></textarea>
		<label>Date</label>
		<input type="text" name="date" id="dateField" value="<?=date('d/m/Y',$response['video']['date'])?>" />
		<small>The date must be in DD/MM/YYYY format.</small>
		<label>Link</label>
		<input type="text" name="link" value="<?=$response['video']['link']?>" />
		<small>The link is usually a webpage with more info about this video.</small>
	</fieldset>
	<fieldset>
		<button type="submit">save changes</button>
	</fieldset>
</form>

<p><a href="http://content.jwplatform.com/previews/<?=$response['video']['key']?>-ALJ3XQCI" target="_blank">Preview this video</a></p>


</body>
</html>
