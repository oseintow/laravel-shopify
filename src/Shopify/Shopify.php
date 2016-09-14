<?php

namespace Oseintow\Shopify;

use GuzzleHttp\Client;

class Shopify{

    private $key;
    private $secret;
    private $shopDomain;
    private $accessToken;
    private $headers = [];

    public function __construct($key = '', $secret = '')
    {
        $this->key = $key;
        $this->secret= $secret;
    }

    /*
     * Set Shop Domain Url;
     */
    public function setShopDomain($shopDomain)
    {
        $url = parse_url($shopDomain);
        $this->shopDomain = isset($url['host']) ? $url['host'] : $this->removeProtocol($shopDomain);

        return $this;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    private function baseUrl()
    {
        return "https://{$this->shopDomain}/";
    }

    // Get the URL required to request authorization
    public function getAuthorizeUrl($scope = [] || '', $redirect_url='')
    {
        if(is_array($scope)) $scope = implode(",", $scope);
        $url = "https://{$this->shopDomain}/admin/oauth/authorize?client_id={$this->key}&scope=" . urlencode($scope);
        if ($redirect_url != '') $url .= "&redirect_uri=" . urlencode($redirect_url);

        return $url;
    }

    public function getAccessToken($code)
    {
        $uri = "admin/oauth/access_token";
        $payload = ["client_id" => $this->key, 'client_secret' => $this->secret, 'code' => $code];
        $response = $this->makeRequest('POST', $uri, $payload);

        if (isset($response['access_token']))
            return $response['access_token'];
        return '';
    }

    private function setXShopifyAccessToken()
    {
        return ['X-Shopify-Access-Token' => $this->accessToken];
    }

    public function addHeader($key, $value)
    {
        array_merge($this->headers, [$key => $value]);

        return $this;
    }

    public function removeHeaders(){
        $this->headers = [];

        return $this;
    }

    public function __call($method, $args)
    {
        list($uri, $params) = [ltrim($args[0],"/"), $args[1] ?? []];
        $headers  = in_array($method, ['post','put']) ? ["Content-Type" => "application/json; charset=utf-8"] : [];
        $headers  = array_merge($headers, $this->setXShopifyAccessToken());
        $response = $this->makeRequest($method, $uri, $params, $headers);

        return $response;
    }

    private function makeRequest($method, $uri, $params = [], $headers = [])
    {
        $client = new Client(['base_uri' => $this->baseUrl(), 'timeout'  => 60.0,]);
        $query = in_array($method, ['get','delete']) ? "query" : "json";
        $response = $client->request(strtoupper($method), $uri, [
                'headers' => array_merge($headers, $this->headers),
                $query => $params
            ]);

        \Log::info($method);
        \Log::info($params);
        \Log::info(array_merge($headers, $this->headers));


        $stream = $response->getBody();
        \Log::info($stream);
        return $stream->getContents();
    }

    public function removeProtocol($url){
        $disallowed = ['http://', 'https://','http//','ftp://','ftps://'];
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }

}