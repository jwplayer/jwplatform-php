# JW Platform API Client

The JWPlatform PHP library provides convenient access to the
[JW Platform](https://www.jwplayer.com/products/jwplatform/)
Management API from applications written in the PHP language.

Visit [JW Player Developer site](https://developer.jwplayer.com/jwplayer/reference#introduction-to-api-v2)
for more information about JW Platform API.

## Requirements

PHP 5.6.0 and later.

## Install

### Composer

You can install the bindings via [Composer](http://getcomposer.org/). Run the following command:

```bash
composer require jwplayer/jwplatform
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

Please refer to our [documentation](https://developer.jwplayer.com/) for all API functionality.

### Client setup

```php
$jwplatform_api = new Jwplayer\JwplatformClient('INSERT API SECRET');
```

### Get video metadata

```php
$media_id = 'INSERT MEDIA ID';
$site_id = 'INSERT SITE ID';
$response = $jwplatform_api->Media->get($site_id, $media_id);
```

### V1 Client

The V1 Client remains available for use but is deprecated. We strongly recommend using the V2 Client when possible.

```php
$jwplatform_api = new Jwplayer\v1\JwplatformAPI('INSERT API KEY', 'INSERT API SECRET');
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
