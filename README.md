JW Platform API Client
======================

A PHP client library for accessing [JW Platform](https://www.jwplayer.com/products/jwplatform/) API.

Visit [JW Player Developer site](https://developer.jwplayer.com/jw-platform/)
for more information about JW Platform API.

Install
-------

_Currently the API adapter is not installable via compose._

Just copy `jwplatform/api.php` over to your directory.


Usage
-----

Example of uploading a file:

```php
<?php

require_once('jwplatform/api.php');

use jwplayer\jwplatform as jwplatform;

$jwplatform_api = new jwplatform\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$target_file = 'examples/test.mp4';
$params = array();
$params['title'] = 'PHP API Test Upload';
$params['description'] = 'Video description here';

$create_response = json_encode($jwplatform_api->call('/videos/create', $params));

print_r($create_response);

$decoded = json_decode(trim($create_response), TRUE);
$upload_link = $decoded['link'];

$upload_response = $jwplatform_api->upload($upload_link, $target_file);

print_r($upload_response);

?>
```

License
-------

JW Platform API library is distributed under the [MIT license](https://github.com/jwplayer/jwplatform-php/blob/master/LICENSE).
