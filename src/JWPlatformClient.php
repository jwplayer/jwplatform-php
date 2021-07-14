<?php

namespace Jwplayer;

class JwPlatformClient {

    /** @var string API client version */
    private $_version = '2.0.0';

    /** @var string API hostname */
    private $_host = "api.jwplayer.com";

    /** @var string JWPlayer API key secret */
    private $_secret;

    /**
     * JwplatformAPI constructor.
     *
     * @param string $secret
     * @param string $host
     */
    public function __construct($secret = '', $host = '') {
        $this->_secret = $secret;
        if ($host) {
            $this->_host = $host;
        }

        $this->analytics = new _AnalyticsClient($this);
        $this->Import = new _ImportClient($this);
        $this->Channel = new _ChannelClient($this);
        $this->Media = new _MediaClient($this);
        $this->WebhookClient = new _WebhookClient($this);
        $this->advertising = new _AdvertisingClient($this);
    }

    /**
     * Make an API request without modifications
     *
     * @param string $method HTTP request method
     * @param string $url HTTP request URL
     * @param string $body HTTP request body
     * @param array $headers HTTP request headers
     * @return mixed
     */
    public function raw_request($method, $url, $body = null, $headers = []) {
        $url = 'https://' . $this->_host . $url;

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, $method);
        if ($body !== null) {
            curl_setopt($curl, CURLOPT_POSTFIELDS, $body);
        }
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_URL, $url);
        $response = curl_exec($curl);
        curl_close($curl);

        $unserialized_response = @unserialize($response);

        return $unserialized_response ? $unserialized_response : $response;
    }

    /**
     * Make an API request with some convenient defaults
     *
     * @param string $method HTTP request method
     * @param string $path Resource or endpoint to request
     * @param array $body HTTP request body which will be JSON encoded
     * @param array $headers Additional HTTP request headers
     * @param array $query_params Additional query parameters to add to the URI
     * @return mixed
     */
    public function request($method, $path, $body = null, $headers = [], $query_params = null) {
        $headers[] = "User-Agent: ";
        $headers[] = "Authorization: Bearer " . $this->_secret;
        $headers[] = "Content-Type: application/json";

        if ($body !== null) {
            $body = json_encode($body);
        }
        if ($query_params !== null) {
            $path += "?" . http_build_query($query_params);
        }

        return $this->raw_request($method, $path, $body, $headers);
    }
}

class _ScopedClient {

    protected $_client;

    public function __construct($client) {
        $this->_client = $client;
    }
}

class _ResourceClient extends _ScopedClient {

    protected $_resource_name = null;
    protected $_collection_path = "/v2/{resource_name}/";
    protected $_singular_path = "/v2/{resource_name}/{resource_id}/";

    protected function _path_format($path, $site_id = null, $resource_name = null, $resource_id = null) {
        $path = str_replace("{site_id}", $site_id, $path);
        $path = str_replace("{resource_name}", $resource_name, $path);
        $path = str_replace("{resource_id}", $resource_id, $path);
        return $path;
    }

    public function list($site_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            $this->_path_format($this->_collection_path, $site_id, $this->_resource_name),
            null,
            [],
            $query_params
        );
    }

    public function create($site_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "POST",
            $this->_path_format($this->_collection_path, $site_id, $this->_resource_name),
            $body,
            [],
            $query_params
        );
    }

    public function get($site_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            $this->_path_format($this->_singular_path, $site_id, $this->_resource_name, $resource_id),
            null,
            [],
            $query_params
        );
    }

    public function update($site_id, $resource_id, $body, $query_params = null) {
        return $this->_client->request(
            "PATCH",
            $this->_path_format($this->_singular_path, $site_id, $this->_resource_name, $resource_id),
            $body,
            [],
            $query_params
        );
    }

    public function delete($site_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "DELETE",
            $this->_path_format($this->_singular_path, $site_id, $this->_resource_name, $resource_id),
            $body,
            [],
            $query_params
        );
    }
}

class _SiteResourceClient extends _ResourceClient {

    protected $_collection_path = "/v2/sites/{site_id}/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/{resource_name}/{resource_id}/";
}

class _AnalyticsClient extends _ScopedClient {

    public function query($site_id, $body, $query_params = null) {
        return $this->_client->request(
            'POST',
            $path = '/v2/sites/'.$site_id.'/analytics/queries/',
            $body = $body,
            $headers = [],
            $query_params
        );
    }
}

class _ImportClient extends _SiteResourceClient {

    protected $_resource_name = "imports";
}

class _ChannelClient extends _SiteResourceClient {

    protected $_resource_name = "channels";

    public function __construct($client) {
        parent::__construct($client);
        $this->_Event = new _ChannelEventClient($client);
    }
}

class _ChannelEventClient extends _ScopedClient {

    public function list($site_id, $channel_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/channels/" . $channel_id . "/events/",
            null,
            [],
            $query_params
        );
    }

    public function get($site_id, $channel_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/channels/" . $channel_id . "/events/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }

    public function request_master($site_id, $channel_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/channels/" . $channel_id . "/events/" . $resource_id . "/request_master/",
            null,
            [],
            $query_params
        );
    }

    public function clip($site_id, $channel_id, $resource_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/channels/" . $channel_id . "/events/" . $resource_id . "/clip/",
            $body,
            [],
            $query_params
        );
    }
}

class _MediaClient extends _SiteResourceClient {

    protected $_resource_name = "media";

    public function reupload($site_id, $resource_id, $body, $query_params = null) {
        return $this->_client->request(
            "PUT",
            $this->_path_format($this->_singular_path, $site_id, $this->_resource_name, $resource_id) . "reupload/",
            $body,
            [],
            $query_params
        );
    }
}

class _WebhookClient extends _ResourceClient {

    protected $_resource_name = "webhooks";
}

class _VpbConfigClient extends _ResourceClient {

    protected $_resource_name = "vpb_configs";
    protected $_collection_path = "/v2/sites/{site_id}/advertising/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/advertising/{resource_name}/{resource_id}/";
}

class _AdvertisingClient extends _ScopedClient {

    public function __construct($client) {
        parent::__construct($client);
        $this->_VpbConfig = new _VpbConfigClient($client);
    }

    public function update_schedules_vpb_config($site_id, $body, $query_params = null) {
        return $this->_client->request(
            "PUT",
            $this->_path_format("/v2/sites/{site_id}/advertising/update_schedules_vpb_config/", $site_id),
            $body,
            [],
            $query_params
        );
    }
}

?>
