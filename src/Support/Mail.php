<?php

namespace Alegra\Support;

use Illuminate\Api\Http\Resource;

/**
 * User Friendly email handler.
 */
class Mail
{
    /**
     * Instance of the resource
     *
     * @var Resource
     */
    private $resource;

    /**
     * Params for call resource request
     *
     * @var array
     */
    private $params = [];

    /**
     * Create instance of Mail
     *
     * @param Resource $resource
     */
    public function __construct(Resource $resource)
    {
        $this->resource = $resource;
    }

    /**
     * Lists of email for send the transaction.
     *
     * @param  array[string] $emails
     * @return $this
     */
    public function to(...$emails)
    {
        $this->params['emails'] = $emails;

        return $this;
    }

    /**
     * Enable copy for send to owner
     *
     * @return $this
     */
    public function copyMe()
    {
        $this->params['sendCopyToUser'] = true;

        return $this;
    }

    /**
     * Send an email with the original resource.
     *
     * @return bool
     */
    public function send()
    {
        $handler = get_class($this->resource);

        return $handler::send($this->resource, $this->params);
    }

    /**
     * Send an email with the copy of the resource.
     *
     * @return bool
     */
    public function sendAsCopy()
    {
        $this->params['invoiceType'] = 'copy';

        return $this->send();
    }
}
