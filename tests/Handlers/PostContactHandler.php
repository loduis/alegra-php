<?php

namespace Alegra\Tests\Handlers;

use Alegra\Contact;
use Psr\Http\Message\RequestInterface;

class PostContactHandler
{
    public function __invoke(RequestInterface $request, array &$options)
    {
        $params = $options['json'];

        // Email invalid
        if (array_has($params, 'email') && !filter_var($params['email'], FILTER_VALIDATE_EMAIL)) {
            return 400;
        }

        // type unknow
        if (array_has($params, 'type') && count($params['type']) == 1) {
            $type = current($params['type']);
            if (!in_array($type, [Contact::TYPE_CUSTOMER, Contact::TYPE_SUPPLIER])) {
                $type = null;
            }
            $params['type'] = (array) $type;
            $options['json'] = $params;
        }
    }
}
