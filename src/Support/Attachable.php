<?php

namespace Alegra\Support;

use finfo;
use SplFileObject;
use UnexpectedValueException;
use Illuminate\Support\Arr;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

/**
 * adds the ability to appeal to be sent by email
 *
 * @method bool attach(string $file)
 * @method bool static attach($id, string $file)
 */
trait Attachable
{
    protected static function macroAttachHandler($resource, $file)
    {
        return static::upload($resource, 'file', $file);
    }

    protected static function upload($resource, $name, $file)
    {
        $options = [
            'multipart' => static::prepareFile($name, $file)
        ];

        if (static::isResource($resource)) {
            $resource = $resource->id;
        }
        $response = static::requestToArray(
            'POST',
            $resource . '/attachment',
            $options
        );

        return new Attachment($response);
    }

    protected static function prepareFile($name, $file)
    {
        if (!file_exists($file)) {
            throw new FileNotFoundException('Not exists file: ' . $file);
        }
        $file = new SplFileObject($file);
        if ($name === 'file' && !static::isImage($file)) {
            throw new UnexpectedValueException('This file is not an image: ' . $file);
        }

        return [
            'name' => $name,
            'contents' => $file,
            'filename' => $file->getFilename()
        ];
    }

    protected static function isImage($file)
    {
        if (!$file instanceof SplFileObject) {
            return false;
        }
        $mime = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $mime->file($file->getRealPath());

        return strpos($mimeType, 'image/') === 0;
    }
}
