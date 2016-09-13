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
    public function setShopDomin($shopDomain)
    {
        $this->shopDomain = $shopDomain;
        return $this;
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    private function baseUrl()
    {
        return "https://{$this->shopDomain}/";
    }

    private function setXShopifyAccessToken()
    {
        $this->addHeader(['X-Shopify-Access-Token' => $this->accessToken]);
    }

    public function addHeader($key, $value)
    {
        array_push($this->headers, [$key => $value]);
    }

    public function __call($method, $resource, $params = [])
    {
        $resource = ltrim($resource,"/");
        in_array($method, ['post','put']) ? $this->addHeader("Content-Type", "application/json; charset=utf-8") : '';
        $response = $this->makeRequest(strtoupper($method), $resource, $params);

        return $response;
    }

    private function makeRequest($method, $resource, $params)
    {
        $this->setXShopifyAccessToken();
        $client = new Client(['base_uri' => $this->baseUrl()]);
        $payload = in_array($method, ['get','delete']) ? 'query' : 'json';
        $response = $client->request($method, $resource, [ $payload => $params, 'headers' => $this->headers]);

        return $response;
    }

}