<?php

namespace Jwplayer;

    use Exception;

class JwplatformClient {

    /** @var string API client version */
    private $_version = '2.0.0';

    /** @var string API hostname */
    private $_host = "api.jwplayer.com";

    /** @var string JWPlayer API key secret */
    private $_secret;

    /**
     * JwplatformClient constructor.
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
        $this->advertising = new _AdvertisingClient($this);

        $this->Import = new _ImportClient($this);
        $this->Channel = new _ChannelClient($this);
        $this->Media = new _MediaClient($this);
        $this->WebhookClient = new _WebhookClient($this);
        $this->MediaProtectionRule = new _MediaProtectionRuleClient($this);
        $this->Player = new _PlayerClient($this);
        $this->Playlist = new _PlaylistClient($this);
        $this->Site = new _SiteClient($this);
        $this->Thumbnail = new _ThumbnailClient($this);
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
        $headers[] = "User-Agent: jwplatform_client-php/" . $this->_version;
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

    public function query_usage($body = null, $query_params = null) {
        return $this->request(
            "PUT",
            "/v2/query_usage/",
            $body,
            [],
            $query_params
        );
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
            null,
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
        $this->Event = new _ChannelEventClient($client);
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

class _MediaRenditionClient extends _ScopedClient {

    public function list($site_id, $media_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/media_renditions/",
            null,
            [],
            $query_params
        );
    }

    public function create($site_id, $media_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "POST",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/media_renditions/",
            $body,
            [],
            $query_params
        );
    }

    public function get($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/media_renditions/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }

    public function delete($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "DELETE",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/media_renditions/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }
}

class _OriginalClient extends _ScopedClient {

    public function list($site_id, $media_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/originals/",
            null,
            [],
            $query_params
        );
    }

    public function create($site_id, $media_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "POST",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/originals/",
            $body,
            [],
            $query_params
        );
    }

    public function get($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/originals/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }

    public function update($site_id, $media_id, $resource_id, $body, $query_params = null) {
        return $this->_client->request(
            "PATCH",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/originals/" . $resource_id . "/",
            $body,
            [],
            $query_params
        );
    }

    public function delete($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "DELETE",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/originals/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }
}

class _TextTrackClient extends _ScopedClient {

    public function list($site_id, $media_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/text_tracks/",
            null,
            [],
            $query_params
        );
    }

    public function create($site_id, $media_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "POST",
            "/v2/sites/" . $site_id . "/media/" . $media_id . "/text_tracks/",
            $body,
            [],
            $query_params
        );
    }

    public function get($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/text_tracks/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }

    public function update($site_id, $media_id, $resource_id, $body, $query_params = null) {
        return $this->_client->request(
            "PATCH",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/text_tracks/" . $resource_id . "/",
            $body,
            [],
            $query_params
        );
    }

    public function delete($site_id, $media_id, $resource_id, $query_params = null) {
        return $this->_client->request(
            "DELETE",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/text_tracks/" . $resource_id . "/",
            null,
            [],
            $query_params
        );
    }

    public function publish($site_id, $media_id, $resource_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/text_tracks/" . $resource_id . "/publish/",
            $body,
            [],
            $query_params
        );
    }

    public function unpublish($site_id, $media_id, $resource_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/media/" . $media_id. "/text_tracks/" . $resource_id . "/unpublish/",
            $body,
            [],
            $query_params
        );
    }
}

class _MediaClient extends _SiteResourceClient {

    protected $_resource_name = "media";

    public function __construct($client) {
        parent::__construct($client);
        $this->MediaRendition = new _MediaRenditionClient($client);
        $this->Original = new _OriginalClient($client);
        $this->TextTrack = new _TextTrackClient($client);
    }

    public function reupload($site_id, $resource_id, $body, $query_params = null) {
        return $this->_client->request(
            "PUT",
            $this->_path_format($this->_singular_path, $site_id, $this->_resource_name, $resource_id) . "reupload/",
            $body,
            [],
            $query_params
        );
    }

    /**
     * Upload a media file
     *
     * @param string $file_path Local path to the file to be uploaded
     * @param string $upload_url Destination URL from the media create response
     * @return string
     * @throws Exception
     */
    public function upload($file_path, $upload_url) {
        $post_data = file_get_contents($file_path);
        $expected_hash = @md5_file($file_path);

        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $upload_url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_data);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HEADER, true);
        curl_setopt($curl, CURLINFO_HEADER_OUT, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array(
            'Content-Type:',
        ));
        $response = curl_exec($curl);
        $curl_info = curl_getinfo($curl);
        curl_close($curl);

        $header_size = $curl_info['header_size'];
        $header = substr($response, 0, $header_size);
        preg_match("/ETag: \"(.*?)\"\r\n/", $header, $matches);
        if ($matches) {
            $returned_hash = $matches[1];
            if ($returned_hash !== $expected_hash) {
                throw new Exception("The local file does not match the file received by the server");
            }
        }

        $body = substr($response, $header_size);
        return $body === "" ? undefined : body;
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

class _PlayerBiddingConfigClient extends _ResourceClient {

    protected $_resource_name = "player_bidding_configs";
    protected $_collection_path = "/v2/sites/{site_id}/advertising/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/advertising/{resource_name}/{resource_id}/";
}

class _ScheduleClient extends _ResourceClient {

    protected $_resource_name = "schedules";
    protected $_collection_path = "/v2/sites/{site_id}/advertising/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/advertising/{resource_name}/{resource_id}/";
}

class _AdvertisingClient extends _ScopedClient {

    public function __construct($client) {
        parent::__construct($client);
        $this->VpbConfig = new _VpbConfigClient($client);
        $this->PlayerBiddingConfig = new _PlayerBiddingConfigClient($client);
        $this->Schedule = new _ScheduleClient($client);
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

    public function update_schedules_player_bidding_configs($site_id, $body, $query_params = null) {
        return $this->_client->request(
            "PUT",
            $this->_path_format("/v2/sites/{site_id}/advertising/update_schedules_player_bidding_configs/", $site_id),
            $body,
            [],
            $query_params
        );
    }
}

class _MediaProtectionRuleClient extends _ResourceClient {

    protected $_resource_name = "media_protection_rules";
}

class _PlayerClient extends _ResourceClient {

    protected $_resource_name = "players";
}

class _ManualPlaylistClient extends _ResourceClient {

    protected $_resource_name = "manual_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _DynamicPlaylistClient extends _ResourceClient {

    protected $_resource_name = "dynamic_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _TrendingPlaylistClient extends _ResourceClient {

    protected $_resource_name = "trending_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _ArticleMatchingPlaylistClient extends _ResourceClient {

    protected $_resource_name = "article_matching_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _SearchPlaylistClient extends _ResourceClient {

    protected $_resource_name = "search_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _RecommendationsPlaylistClient extends _ResourceClient {

    protected $_resource_name = "recommendations_playlist";
    protected $_collection_path = "/v2/sites/{site_id}/playlists/{resource_name}/";
    protected $_singular_path = "/v2/sites/{site_id}/playlists/{resource_id}/{resource_name}/";
}

class _PlaylistClient extends _ResourceClient {

    protected $_resource_name = "playlists";

    public function __construct($client) {
        parent::__construct($client);
        $this->ManualPlaylist = new _ManualPlaylistClient($client);
        $this->DynamicPlaylist = new _DynamicPlaylistClient($client);
        $this->TrendingPlaylist = new _TrendingPlaylistClient($client);
        $this->ArticleMatchingPlaylist = new _ArticleMatchingPlaylistClient($client);
        $this->SearchPlaylist = new _SearchPlaylistClient($client);
        $this->RecommendationsPlaylist = new _RecommendationsPlaylistClient($client);
    }
}

class _SiteProtectionRuleClient extends _ScopedClient {

    public function get($site_id, $query_params = null) {
        return $this->_client->request(
            "GET",
            "/v2/sites/" . $site_id . "/site_protection_rule/",
            null,
            [],
            $query_params
        );
    }

    public function update($site_id, $body, $query_params = null) {
        return $this->_client->request(
            "PATCH",
            "/v2/sites/" . $site_id . "/site_protection_rule/",
            $body,
            [],
            $query_params
        );
    }
}

class _SiteClient extends _ScopedClient {

    public function __construct($client) {
        parent::__construct($client);
        $this->SiteProtectionRule = new _SiteProtectionRuleClient($client);
    }

    public function remove_tag($site_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/remove_tag/",
            $body,
            [],
            $query_params
        );
    }

    public function rename_tag($site_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/rename_tag/",
            $body,
            [],
            $query_params
        );
    }

    public function query_usage($site_id, $body = null, $query_params = null) {
        return $this->_client->request(
            "PUT",
            "/v2/sites/" . $site_id . "/query_usage/",
            $body,
            [],
            $query_params
        );
    }
}

class _ThumbnailClient extends _ResourceClient {

    protected $_resource_name = "thumbnails";
}

?>
