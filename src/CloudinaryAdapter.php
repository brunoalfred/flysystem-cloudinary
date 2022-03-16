<?php

namespace JasiriLabs\FlysystemCloudinary;

use Cloudinary\Api\Exception\AlreadyExists;
use Cloudinary\Api\Exception\BadRequest;
use Cloudinary\Cloudinary;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use Cloudinary\Api\Upload\UploadApi;
use Cloudinary\Api\Metadata;
use Cloudinary\Api\Metadata\Metadata as MetadataMetadata;

class CloudinaryAdapter implements FilesystemAdapter
{




    public function __construct(
        public Cloudinary $cloudinary
    ) {
    }

    /**
     * 
     * Use filename/ to check if it exists  
     * 
     * 
     **/


    public function fileExists(string $path): bool
    {
     
        $response = $this->cloudinary->searchApi()
            ->expression('filename:' . $path)
            ->execute();
     
       $jsonResponse = json_encode($response);
     
        if (json_decode($jsonResponse, TRUE)['total_count'] != 0) {
            return true;
        } else {
            return false;
        }
        
    }


    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }



    public function write(string $path, string $contents, Config $config): void
    {

        //TODO: Do something magical           


    }


    public function writeStream(string $path, $contents, Config $config): void
    {
    }


    public function read(string $path): string
    {


        return '';
    }



    public function readStream(string $path)
    {
    }


    public function delete(string $path): void
    {
    }


    public function deleteDirectory(string $path): void
    {
    }


    public function createDirectory(string $path, Config $config): void
    {
    }

    public function setVisibility(string $path, string $visibility): void

    {
    }

    public function visibility(string $path): FileAttributes
    {
        return new FileAttributes($path);
    }


    public function mimeType(string $path): FileAttributes
    {
        return new FileAttributes(
            $path,
            null,
            null,
            null,
            $this->mimeTypeDetector->detectMimeTypeFromPath($path)
        );
    }


    public function lastModified(string $path): FileAttributes
    {

        $timestamp = '';
        return new FileAttributes(
            $path,
            null,
            null,
            $timestamp
        );
    }

    public function fileSize(string $path): FileAttributes
    {


        return new FileAttributes(
            $path,
            $response['size'] ?? null
        );
    }


    public function listContents(string $path, bool $deep): iterable
    {

        yield '';
    }


    public function move(string $source, string $destination, Config $config): void
    {
    }


    public function copy(string $source, string $destination, Config $config): void
    {
    }
}
