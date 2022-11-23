<?php

namespace TinkoffAuth\Services\Http;

class Request
{
    private $curl;
    private string $domain = '';
    private array $headers = [];

    public function __construct($domain = '')
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($this->curl, CURLOPT_AUTOREFERER, true);
        curl_setopt($this->curl, CURLOPT_HEADER, true);
        curl_setopt($this->curl, CURLOPT_TIMEOUT, 10);
        curl_setopt($this->curl, CURLOPT_MAXREDIRS, 10);
        $this->domain = $domain ?? '';

        $this->headers[] = 'cache-control: no-cache';
    }

    public function basic($username, $password)
    {
        $this->pushHeader("Authorization: Basic " . base64_encode($username . ':' . $password));
    }

    public function bearer($token)
    {
        $this->pushHeader("Authorization: Bearer " . $token);
    }

    public function pushHeader($value)
    {
        $this->headers[] = $value;
    }

    public function get($url, array $query): Response
    {
        return $this->request($url . '?' . http_build_query($query), 'get');
    }

    public function post($url, $body): Response
    {
        return $this->request($url, 'post', $body);
    }

    public function request($url, $method = 'GET', $body = null): Response
    {
        if (strtolower($method) === 'post') {
            curl_setopt($this->curl, CURLOPT_POST, true);
            curl_setopt($this->curl, CURLOPT_POSTFIELDS, http_build_query($body));
            $this->headers[] = 'content-type: application/x-www-form-urlencoded';
        }

        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $this->headers);

        if ($this->domain) {
            $url = rtrim($this->domain, '/') . '/' . $url;
        }
        curl_setopt($this->curl, CURLOPT_URL, $url);

        $response = curl_exec($this->curl);

        $headerSize = curl_getinfo($this->curl, CURLINFO_HEADER_SIZE);
        $headers    = substr($response, 0, $headerSize);
        $body       = substr($response, $headerSize);

        $response = new Response();
        $response->setHeaders($headers);
        $response->setBody($body);

        return $response;
    }
}