<?php

namespace App\Traits;

use GuzzleHttp\Client;

trait ConsumeInternalService
{
    public function performRequest($method, $requestUri, $formParams = [], $headers = [])
    {
        $client = new Client([
            'base_uri' => $this->baseUri,
        ]);
        
        $response = $client->request($method, $requestUri, ['form_params' => $formParams, 'headers' => $headers]);

        return $response->getBody()->getContents();
    }
}