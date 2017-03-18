<?php

namespace Alegra;

use GuzzleHttp\Psr7;
use Illuminate\Support\Arr;
use Illuminate\Api\Http\Restable;
use Illuminate\Api\Http\Resource as ApiResource;

/**
 * Base resource
 */
abstract class Resource extends ApiResource
{
    protected static $filterWith = Support\Filter::class;

    use Restable;

    public static function first()
    {
        return static::all(['limit' => 1])->first();
    }

    final public static function onDeletedResource($response)
    {
        $data = (array) json_decode($response->getBody(), true);

        Arr::forget($data, ['code', 'message']);

        $data = json_encode($data);

        return $response->withBody(Psr7\stream_for($data));
    }

    /**
     * Save the current resource.
     *
     * @return $this
     */
    public function save()
    {
        return $this->store($this->id === null ? 'POST' : 'PUT', $this->id);
    }
}
