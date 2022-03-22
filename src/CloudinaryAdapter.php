<?php

namespace JasiriLabs\FlysystemCloudinary;

use Cloudinary\Api\Exception\NotFound;
use Cloudinary\Api\Exception\GeneralError;
use Cloudinary\Cloudinary;
use League\Flysystem\FilesystemAdapter;
use League\Flysystem\Config;
use League\Flysystem\FileAttributes;
use League\Flysystem\UnableToCreateDirectory;
use League\Flysystem\UnableToReadFile;
use League\Flysystem\UnableToDeleteDirectory;
use League\Flysystem\UnableToDeleteFile;
use League\Flysystem\UnableToWriteFile;
use League\Flysystem\UnableToSetVisibility;
use League\Flysystem\UnableToRetrieveMetadata;

class CloudinaryAdapter implements FilesystemAdapter
{




    public function __construct(
        public Cloudinary $cloudinary
    ) {
    }

    /**
     * 
     * Check if file exists  
     * 
     **/
    public function fileExists(string $path): bool
    {

        try {
            $this->cloudinary->adminApi()->asset($path);
        } catch (NotFound $e) {
            return false;
        }

        return true;
    }


    /**
     * 
     * Check if directory exists  
     * 
     **/
    public function directoryExists(string $path): bool
    {
        return $this->fileExists($path);
    }



    public function write(string $path, string $contents, Config $config): void
    {

        try {
            $tempFile = tmpfile();

            fwrite($tempFile, $contents);

            $this->writeStream($path, $tempFile, $config);
        } catch (GeneralError $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }


    public function writeStream(string $path,  $contents, Config $config): void
    {

        try {
            $resourceMetadata = stream_get_meta_data($contents);

            $this->cloudinary->uploadApi()->upload($resourceMetadata['uri']);
        } catch (GeneralError $e) {
            throw UnableToWriteFile::atLocation($path, $e->getMessage(), $e);
        }
    }


    public function read(string $path): string
    {

        try {
            $resource = $this->cloudinary->adminApi()->asset($path);

            $contents = file_get_contents($resource['secure_url']);

            return (string) compact('contents', 'path');
        } catch (GeneralError $e) {
        }
    }



    public function readStream(string $path)
    {
        try {
            $resource = $this->cloudinary->adminApi()->asset($path);

            $stream = fopen($resource['secure_url'], 'rb');

            return compact('stream', 'path');
        } catch (GeneralError $e) {
            throw UnableToReadFile::fromLocation($path, $e->getMessage(), $e);
        }
    }


    public function delete(string $path): void
    {
        $response = $this->cloudinary->uploadApi()->destroy($path);

        try {
            is_array($response) && ($response['result'] == 'ok');
        } catch (GeneralError $e) {
            throw UnableToDeleteFile::atLocation($path, $e->getMessage(), $e);
        }
    }


    public function deleteDirectory(string $path): void
    {
        try {
            $respose = $this->cloudinary->adminApi()->deleteAssetsByPrefix($path);
        } catch (GeneralError $e) {
            throw UnableToDeleteDirectory::atLocation($path, $e->getPrevious()->getMessage(), $e);
        }
    }


    public function createDirectory(string $path, Config $config): void
    {
        try {
            $this->cloudinary->adminApi()->createFolder($path);
        } catch (GeneralError $e) {
            throw UnableToCreateDirectory::atLocation($path, $e->getMessage());
        }
    }

    public function setVisibility(string $path, string $visibility): void
    {
        throw UnableToSetVisibility::atLocation($path, 'Adapter does not support visibility controls at the moment.');
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

        try {
            $response = $this->cloudinary->adminApi()->asset($path);

            $jsonResponse = json_encode($response);

            $timestamp = json_decode($jsonResponse, TRUE)['created_at'];
        } catch (GeneralError $e) {
            throw UnableToRetrieveMetadata::lastModified($path, $e->getMessage());
        }

        $timestamp = strtotime($timestamp);

        return new FileAttributes(
            $path,
            null,
            null,
            $timestamp
        );
    }

    public function fileSize(string $path): FileAttributes
    {

        try {
            $response = $this->cloudinary->adminApi()->asset($path);

            $response = json_encode($response);

            $size = json_decode($response, TRUE)['bytes'];
        } catch (GeneralError $e) {
            throw UnableToRetrieveMetadata::lastModified($path, $e->getMessage());
        }

        return new FileAttributes(
            $path,
            $size
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
