<?php

namespace Illuminate\Api\Testing\Handlers;

use GuzzleHttp\Psr7;
use Psr\Http\Message\ResponseInterface;

class ResponseHandler
{
    private $response;

    public function __construct(ResponseInterface $response)
    {
        $this->response = $response;
    }

    public function toArray()
    {
        return json_decode((string) $this->response->getBody(), true);
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function setBody(array $body)
    {
        $body = json_encode($body);

        return $this->response = $this->response->withBody(Psr7\stream_for($body));
    }
}
