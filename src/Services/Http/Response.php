<?php

namespace TinkoffAuth\Services\Http;

class Response
{
    private string $headersRaw;
    private string $bodyRaw;

    private array $bodyJSON;
    private array $headers;

    public function setBody($body)
    {
        $this->bodyRaw  = $body;
        $this->bodyJSON = $this->jsonDecodeBody($body);
    }

    public function setHeaders($headers)
    {
        if (is_array($headers)) {
            $this->headers = $headers;
        }
        if (is_string($headers)) {
            $this->headersRaw = $headers;
            $this->headers    = $this->parseHeaders($headers);
        }
    }

    public function json(): array
    {
        return $this->bodyJSON ?? [];
    }

    public function body(): string
    {
        return $this->bodyRaw ?? '';
    }

    public function headers(): array
    {
        return $this->headers ?? [];
    }

    public function headersRaw(): string
    {
        return $this->headersRaw ?? '';
    }

    private function parseHeaders($headers): array
    {
        if ( ! is_string($headers)) {
            return [];
        }

        $headersArray = [];
        $headers      = explode("\n", $headers);
        foreach ($headers as $header) {
            if (strpos($header, 'HTTP/') !== false) {
                continue;
            }

            $headerPieces = explode(':', $header);
            if (count($headerPieces) !== 2) {
                continue;
            }

            $headersArray[$headerPieces[0]] = $headerPieces[1];
        }

        return $headersArray;
    }

    private function jsonDecodeBody($body): array
    {
        $canBeDecoded = json_decode($body);
        if ( ! $canBeDecoded) {
            return [];
        }

        return json_decode($body, true);
    }
}