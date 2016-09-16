<?php

namespace Oseintow\Shopify;

use GuzzleHttp\Client;
use Oseintow\Shopify\Exceptions\ShopifyApiException;


class Shopify{

    protected $key;
    protected $secret;
    protected $shopDomain;
    protected $accessToken;
    protected $requestHeaders = [];
    protected $responseHeaders = [];
    protected $client;
    protected $responseStatusCode;
    protected $reasonPhrase;

    public function __construct(Client $client, $key = '', $secret = '')
    {
        $this->key = $key;
        $this->secret= $secret;
        $this->client = $client;
    }

    /*
     * Set Shop  Url;
     */
    public function setShopUrl($shopUrl)
    {
        $url = parse_url($shopUrl);
        $this->shopDomain = isset($url['host']) ? $url['host'] : $this->removeProtocol($shopUrl);

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

        return $response ?? '';
    }

    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;

        return $this;
    }

    private function setXShopifyAccessToken()
    {
        return ['X-Shopify-Access-Token' => $this->accessToken];
    }

    public function addHeader($key, $value)
    {
        $this->requestHeaders = array_merge($this->requestHeaders, [$key => $value]);

        return $this;
    }

    public function removeHeaders()
    {
        $this->requestHeaders = [];

        return $this;
    }

    /*
     *  $args[0] is for route uri and $args[1] is either request body or query strings
     */
    public function __call($method, $args)
    {
        list($uri, $params) = [ltrim($args[0],"/"), $args[1] ?? []];
        $response = $this->makeRequest($method, $uri, $params, $this->setXShopifyAccessToken());

        return (is_array($response)) ? $this->convertResponseToCollection($response) : $response;
    }

    private function convertResponseToCollection($response)
    {
        return collect(json_decode(json_encode($response)));
    }

    private function makeRequest($method, $uri, $params = [], $headers = [])
    {
        $query = in_array($method, ['get','delete']) ? "query" : "json";
        $response = $this->client->request(strtoupper($method), $this->baseUrl().$uri, [
                'headers' => array_merge($headers, $this->requestHeaders),
                $query => $params,
                'timeout' => 60.0,
                'http_errors' => false
            ]);

        $this->parseResponse($response);
        $responseBody = $this->responseBody($response);

        if(isset($responseBody['errors']) || $response->getStatusCode() >= 400){
            throw new ShopifyApiException(
                $responseBody['errors'] ?? $response->getReasonPhrase(),
                $response->getStatusCode()
            );
        }

        return (is_array($responseBody) && (count($responseBody) > 0)) ? array_shift($responseBody) : $responseBody;
    }

    private function parseResponse($response)
    {
        $this->parseHeaders($response->getHeaders());
        $this->setStatusCode($response->getStatusCode());
        $this->setReasonPhrase($response->getReasonPhrase());
    }

    private function setStatusCode($code)
    {
        $this->responseStatusCode = $code;
    }

    public function getStatusCode()
    {
        return $this->responseStatusCode;
    }

    private function setReasonPhrase($message)
    {
        $this->reasonPhrase = $message;
    }

    public function getReasonPhrase()
    {
        return $this->reasonPhrase;
    }

    private function parseHeaders($headers)
    {
        foreach ($headers as $name => $values) {
            $this->responseHeaders = array_merge($this->responseHeaders, [$name => implode(', ', $values)]);
        }
    }

    public function getHeaders()
    {
        return $this->responseHeaders;
    }

    public function getHeader($header)
    {
        return $this->hasHeader($header) ? $this->responseHeaders[$header] : '';
    }

    public function hasHeader($header)
    {
        return array_key_exists($header, $this->responseHeaders);
    }

    private function responseBody($response)
    {
        return json_decode($response->getBody(), true);
    }

    public function removeProtocol($url)
    {
        $disallowed = ['http://', 'https://','http//','ftp://','ftps://'];
        foreach($disallowed as $d) {
            if(strpos($url, $d) === 0) {
                return str_replace($d, '', $url);
            }
        }

        return $url;
    }

}