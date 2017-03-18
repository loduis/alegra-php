<?php

namespace Alegra\Tests\Handlers;

use Alegra\PriceList;
use Alegra\Seller;
use Alegra\Contact;
use Illuminate\Support\Arr;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Illuminate\Api\Testing\Handlers\ResponseHandler;

class PostContactHandler
{
    public static function requestHandle(RequestInterface $request, array &$options)
    {
        $params = $options['json'];

        // Email invalid
        if (array_has($params, 'email') && !filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return 400;
        }

        // type unknow
        if (array_has($params, 'type') && count($params['type']) == 1) {
            $types = (array) $params['type'];
            foreach ($types as $index => $type) {
                if (!in_array($type, [Contact::TYPE_CLIENT, Contact::TYPE_PROVIDER])) {
                    unset($types[$index]);
                }
            }
            $params['type'] = $types;
            $options['json'] = $params;
        }
    }

    public static function responseHandle(ResponseInterface $response)
    {
        $handler = new ResponseHandler($response);
        $body = $handler->toArray();

        if ($sellerId = Arr::get($body, 'seller.id')) {
            $body['seller'] = Seller::get($sellerId)->toArray();
            $handler->setBody($body);
        }

        if ($priceListId = Arr::get($body, 'priceList.id')) {
            $body['priceList'] = PriceList::get($priceListId)->toArray();
            $handler->setBody($body);
        }

        return $handler->getResponse();
    }
}
