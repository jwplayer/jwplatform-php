<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>Uploading video</title>
	<link media="all" href="css/style.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="botr/upload.js"></script>
</head>
<body>


<form id="uploadForm" action="" method="POST" enctype="multipart/form-data">
	<fieldset>
		<label>Select video</label>
		<input id="uploadFile" type="file" name="file" />
		<div id="uploadBar" style="width:480px; float:left; display:none; background:#FFF; margin:5px 0;">
        <div id="uploadProgress" style="background:#46800d; width:0px; height:18px;"></div></div>
		<small id="uploadText">You can upload any video format (WMV, AVI, MP4, MOV, FLV, ...)</small>
		<button type="submit" id="uploadButton">Upload</button>
	</fieldset>
</form>
<p><a href="test.mp4">Get example video</a> (rightclick to save)</p>


<script type="text/javascript">
$(function() {
  var filename;

  // Have the server perform an API call to create the video.
  // This can not be done beforehand, because it depends on whether
  // resumable uploads are supported.
  var data = {};
  if(BotrUpload.resumeSupported()) {
    data['resumable'] = 'resumable';
  }
  $.get("create.php", data, function(data) {
    // Attach a BotrUpload instance to the form.
    var upload = new BotrUpload(data.link, data.session_id, {
      "url": "show.php",
      params: {
        "video_key": data.media.key
      }
    });
    upload.useForm($("#uploadFile").get(0));
    $("body").append(upload.getIframe());
    upload.pollInterval = 1000;
    
    // Create a pause button if resume is available
    var pauseButton;
    if(BotrUpload.resumeSupported()) {
      pauseButton = $('<button disabled>').text('Pause');
      pauseButton.toggle(function() {
        pauseButton.text('Resume');
        upload.pause();
        return false;
      },
      function() {
        pauseButton.text('Pause');
        upload.start();
        return false;
      });
      $('#uploadButton').after(pauseButton);
    }
    
    // When the upload starts, we hide the input, show the progress and disable the button.
    upload.onStart = function() {
      filename = $("#uploadFile").val().split(/[\/\\]/).pop();
      $("#uploadFile").css('display','none');
      $("#uploadBar").css('display','block');
      $("#uploadButton").attr('disabled','disabled');
      if(pauseButton) {
        pauseButton.removeAttr('disabled');
      }
    };
    
    // During upload, we update both the progress div and the text below it.
    upload.onProgress = function(bytes, total) {
      // Round to one decimal
      var pct = Math.floor(bytes * 1000 / total) / 10;
      $("#uploadProgress").animate({'width': pct + '%'}, 400);
      $("#uploadText").html('Uploading ' + filename + ' (' + pct + '%) ...');
    };
  }, 'json');
});
</script>


</body>
</html>
