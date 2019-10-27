# JW Platform API Client

The JWPlatform PHP library provides convenient access to the
[JW Platform](https://www.jwplayer.com/products/jwplatform/)
Management API from applications written in the PHP language.

Visit [JW Player Developer site](https://developer.jwplayer.com/jw-platform/)
for more information about JW Platform API.

## Requirements

PHP 5.6.0 and later.

## Install

### Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require jw-player/jwplatform-php
```

To use the bindings, use Composer's [autoload](https://getcomposer.org/doc/01-basic-usage.md#autoloading):

```php
require_once('vendor/autoload.php');
```

### Manual Installation

If you do not wish to use Composer, you can download the
[latest release](https://github.com/jwplayer/jwplatform-php/releases).
Then, to use the bindings, include the `init.php` file.

```php
require_once('/path/to/jwplatform-php/init.php');
```

## Dependencies

The bindings require the following extensions in order to work properly:

- [`curl`](https://secure.php.net/manual/en/book.curl.php), although you can use your own non-cURL client if you prefer

If you use Composer, these dependencies should be handled automatically. If you install manually, you'll want to make sure that these extensions are available.

## Usage

Example of uploading a file:

```php
$jwplatform_api = new Jwplayer\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');

$target_file = 'examples/test.mp4';
$params = array();
$params['title'] = 'PHP API Test Upload';
$params['description'] = 'Video description here';

// Create video metadata
$create_response = json_encode($jwplatform_api->call('/videos/create', $params));
$decoded = json_decode(trim($create_response), TRUE);
$upload_link = $decoded['link'];

$upload_response = $jwplatform_api->upload($upload_link, $target_file);

print_r($upload_response);
```

## Development

Get [Composer][composer]. For example, on Mac OS:

```bash
brew install composer
```

Install dependencies:

```bash
composer install
```

## License

JW Platform API library is distributed under the
[MIT license](https://github.com/jwplayer/jwplatform-php/blob/master/LICENSE).

[composer]: https://getcomposer.org/
[curl]: http://curl.haxx.se/docs/caextract.html
