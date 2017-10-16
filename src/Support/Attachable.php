<?php

namespace Alegra\Support;

use finfo;
use SplFileObject;
use Illuminate\Support\Arr;

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
        $files = static::fileOptions($file);

        $options = [
            'multipart' => $files
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

    protected static function fileOptions($file)
    {
        $name = 'file';
        $filename = "$name.txt";
        if (file_exists($file)) {
            $mime = new finfo(FILEINFO_MIME_TYPE);
            $mimeType = $mime->file($file);
            if (strpos($mimeType, 'image/') !== false) {
                $name = 'image';
            }
            $file = new SplFileObject($file);
            $filename = $file->getFilename();
        }

        return [
            'name' => $name,
            'contents' => $file,
            'filename' => $filename
        ];
    }
}
