<?php

namespace Jwplayer;

    use Exception;

class JwplatformAPI {

        /** @var string API current version */
        private $_version = '1.7.0';

        /** @var array API urls */
        private const urls = [
            'v1' => 'https://api.jwplatform.com/v1',
            'v2' => 'https://api.jwplayer.com/v2',
            'upload' => 'https://cdn.jwplayer.com'
        ];

        /** @var string HTTP Library used can be either curl or fopen */
        private $_library;

        /** @var string API base url */
        private $_url;

        /** @var string JwPlayer account key */
        private $_key;

        /** @var string JwPlayer account secret */
        private $_secret;

        /** @var string JwPlayer reporting API Key */
        private $_reportingAPIKey = null;

        /**
         * JwplatformAPI constructor.
         *
         * @param string $key
         * @param string $secret
         * @param string $reportingAPIKey
         */
        public function __construct($key, $secret, $reportingAPIKey = '') {
            $this->_key = $key;
            $this->_secret = $secret;
            $this->_reportingAPIKey = $reportingAPIKey;

            // Determine which HTTP library to use:
            // check for cURL, else fall back to file_get_contents
            if (function_exists('curl_init')) {
                $this->_library = 'curl';
            } else {
                $this->_library = 'fopen';
            }
        }

        /**
         * Returns API version
         *
         * @return string
         */
        public function getVersion(): string {
            return $this->_version;
        }

        /**
         * RFC 3986 complient rawurlencode()
         * Only required for phpversion() <= 5.2.7RC1
         * See http://www.php.net/manual/en/function.rawurlencode.php#86506
         *
         * @param integer|float|string|boolean|array $input
         * @return array|string|string[]
         */
        private function _urlencode($input) {
            if (is_array($input)) {
                return array_map(array('_urlencode'), $input);
            } else if (is_scalar($input)) {
                return str_replace('+', ' ', str_replace('%7E', '~', rawurlencode($input)));
            } else {
                return '';
            }
        }

        /**
         * Sign API call arguments
         *
         * @param array $args
         * @return string
         */
        private function _sign($args): string {
            ksort($args);
            $sbs = "";
            foreach ($args as $key => $value) {
                if ($sbs != "") {
                    $sbs .= "&";
                }
                // Construct Signature Base String
                $sbs .= $this->_urlencode($key) . "=" . $this->_urlencode($value);
            }

            // Add shared secret to the Signature Base String and generate the signature
            $signature = sha1($sbs . $this->_secret);

            return $signature;
        }

        /**
         * Add required api_* arguments
         *
         * @param array $args
         * @return array
         */
        private function _args($args): array {
            $args['api_nonce'] = str_pad(mt_rand(0, 99999999), 8, STR_PAD_LEFT);
            $args['api_timestamp'] = time();

            $args['api_key'] = $this->_key;

            if (!array_key_exists('api_format', $args)) {
                // Use the serialised PHP format,
                // otherwise use format specified in the call() args.
                $args['api_format'] = 'php';
            }

            // Add API kit version
            $args['api_kit'] = 'php-' . $this->_version;

            // Sign the array of arguments
            $args['api_signature'] = $this->_sign($args);

            return $args;
        }

        /**
         * Construct call URL
         *
         * @param strin $call
         * @param array $args
         * @return string
         */
        public function call_url($call, $args = []): string {
            $url = $this->_url . $call . '?' . http_build_query($this->_args($args), "", "&");
            return $url;
        }

        /**
         * Construct call URL v2
         *
         * @param string $call
         * @return array
         * @throws Exception
         */
        public function call_urlv2($call): array {
            if ($this->_reportingAPIKey) {
                $url['headers'] = [
                    'Authorization: ' . $this->_reportingAPIKey,
                    'Content-Type: application/json'
                ];
            } else {
                throw new Exception('Missing param token (reportingAPIKey)');
            }

            $url['url'] = $this->_url . $call;
            return $url;
        }

        /**
         * Make an API call
         *
         * @param string $call relative path to data  such as /sites/xxxxXXxx/analytics/queries/
         * @param array $args
         * @param string $apiVersion
         * @return bool|false|mixed|string
         */
        public function call($call, $args = [], $apiVersion = 'v1') {
            $this->_url = self::urls[$apiVersion];
            $url = '';

            switch ($apiVersion) {
                case 'v1':
                    $url = $this->call_url($call, $args);
                    break;
                case 'v2':
                    try {
                        $requestParams = $this->call_urlv2($call);
                        $url = $requestParams['url'];
                    } catch (Exception $e) {
                        echo 'An error occured during curl headers initialization : ' .$e->getMessage();
                    }
                    break;
                case 'upload':
                    break;
            }

            $response = null;
            switch($this->_library) {
                case 'curl':
                    $curl = curl_init();
                    if ($apiVersion === 'v2') {
                        curl_setopt($curl, CURLOPT_HTTPHEADER, $requestParams['headers']);
                        curl_setopt($curl, CURLOPT_POST, true);
                        curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($args));
                    }
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    $response = curl_exec($curl);
                    curl_close($curl);
                    break;
                default:
                    $response = file_get_contents($url);
            }

            $unserialized_response = @unserialize($response);

            return $unserialized_response ? $unserialized_response : $response;
        }

        /**
         * Upload a file
         *
         * @param string $file_path
         * @param array $upload_link
         * @param string $api_format
         * @return mixed|string
         */
        public function upload($file_path, $upload_link = [], $api_format = "php") {
            $url = $upload_link['protocol'] . '://' . $upload_link['address'] . $upload_link['path'] .
                "?key=" . $upload_link['query']['key'] . '&token=' . $upload_link['query']['token'] .
                "&api_format=" . $api_format;

            // A new variable included with curl in PHP 5.5 - CURLOPT_SAFE_UPLOAD - prevents the
            // '@' modifier from working for security reasons (in PHP 5.6, the default value is true)
            // http://stackoverflow.com/a/25934129
            // http://php.net/manual/en/migration56.changed-functions.php
            // http://comments.gmane.org/gmane.comp.php.devel/87521
            if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50500) {
              $post_data = array("file"=>"@" . $file_path);
            } else {
                if (!filter_var($file_path, FILTER_SANITIZE_URL)) {
                    $post_data = array("file" => new \CURLFile($file_path));
                } else {
                    $temp = tmpfile();
                    fwrite($temp, file_get_contents($file_path));
                    fseek($temp, 0);
                    $localFilePath = stream_get_meta_data($temp)['uri'];
                    $post_data = array("file" => new \CURLFile($localFilePath));
                }
            }

            $response = null;
            switch($this->_library) {
                case 'curl':
                    $curl = curl_init();
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_URL, $url);
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
                    $response = curl_exec($curl);
                    $err_no = curl_errno($curl);
                    curl_close($curl);
                    break;
                default:
                    $response = "Error: No cURL library";
            }

            if ($err_no == 0) {
                return unserialize($response);
            } else {
                return "Error #" . $err_no . ": " . curl_error($curl);
            }
        }
    }
?>
