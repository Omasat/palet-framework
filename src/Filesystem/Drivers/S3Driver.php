<?php

declare(strict_types=1);

namespace Palet\Framework\Filesystem\Drivers;

use Palet\Framework\Contracts\Filesystem\StorageDriverInterface;
use RuntimeException;
use InvalidArgumentException;
// Aws\S3\S3Client is required for this driver

class S3Driver implements StorageDriverInterface
{
    protected $client;
    protected string $bucket;
    protected string $prefix;

    /**
     * @param mixed $client An instance of Aws\S3\S3Client
     */
    public function __construct($client, string $bucket, string $prefix = '')
    {
        if (!class_exists('Aws\S3\S3Client') || !($client instanceof \Aws\S3\S3Client)) {
            throw new RuntimeException("Aws\S3\S3Client is required to use S3Driver.");
        }

        $this->client = $client;
        $this->bucket = $bucket;
        $this->prefix = trim($prefix, '/');
    }

    protected function getPath(string $path): string
    {
        $path = trim($path, '/');
        return $this->prefix ? $this->prefix . '/' . $path : $path;
    }

    public function exists(string $path): bool
    {
        return $this->client->doesObjectExist($this->bucket, $this->getPath($path));
    }

    public function read(string $path): ?string
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            return (string) $result['Body'];
        } catch (\Exception $e) {
            return null;
        }
    }

    public function readStream(string $path)
    {
        try {
            $result = $this->client->getObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            return $result['Body']->detach();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function write(string $path, string $contents, array $options = []): bool
    {
        try {
            $args = array_merge([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path),
                'Body' => $contents
            ], $options);
            
            $this->client->putObject($args);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function writeStream(string $path, $resource, array $options = []): bool
    {
        if (!is_resource($resource)) {
            throw new InvalidArgumentException('Provided data must be a valid resource stream.');
        }

        try {
            $args = array_merge([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path),
                'Body' => $resource
            ], $options);
            
            $this->client->putObject($args);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function append(string $path, string $data): bool
    {
        if ($this->exists($path)) {
            return $this->write($path, $this->read($path) . $data);
        }
        return $this->write($path, $data);
    }

    public function prepend(string $path, string $data): bool
    {
        if ($this->exists($path)) {
            return $this->write($path, $data . $this->read($path));
        }
        return $this->write($path, $data);
    }

    public function copy(string $from, string $to): bool
    {
        try {
            $this->client->copyObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($to),
                'CopySource' => urlencode($this->bucket . '/' . $this->getPath($from))
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function move(string $from, string $to): bool
    {
        if ($this->copy($from, $to)) {
            return $this->delete($from);
        }
        return false;
    }

    public function delete(string|array $paths): bool
    {
        $paths = is_array($paths) ? $paths : func_get_args();
        $objects = [];

        foreach ($paths as $path) {
            $objects[] = ['Key' => $this->getPath($path)];
        }

        try {
            $this->client->deleteObjects([
                'Bucket' => $this->bucket,
                'Delete' => [
                    'Objects' => $objects
                ]
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function createDirectory(string $path): bool
    {
        return $this->write(rtrim($path, '/') . '/', '');
    }

    public function deleteDirectory(string $directory): bool
    {
        try {
            $this->client->deleteMatchingObjects($this->bucket, $this->getPath($directory) . '/');
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function size(string $path): int
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            return (int) $result['ContentLength'];
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function lastModified(string $path): int
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            return strtotime((string) $result['LastModified']);
        } catch (\Exception $e) {
            return 0;
        }
    }

    public function mimeType(string $path): string
    {
        try {
            $result = $this->client->headObject([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            return (string) $result['ContentType'];
        } catch (\Exception $e) {
            return 'application/octet-stream';
        }
    }

    public function visibility(string $path): string
    {
        try {
            $result = $this->client->getObjectAcl([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path)
            ]);
            
            foreach ($result['Grants'] as $grant) {
                if (isset($grant['Grantee']['URI']) && $grant['Grantee']['URI'] === 'http://acs.amazonaws.com/groups/global/AllUsers' && $grant['Permission'] === 'READ') {
                    return 'public';
                }
            }
            return 'private';
        } catch (\Exception $e) {
            return 'private';
        }
    }

    public function setVisibility(string $path, string $visibility): bool
    {
        try {
            $this->client->putObjectAcl([
                'Bucket' => $this->bucket,
                'Key' => $this->getPath($path),
                'ACL' => $visibility === 'public' ? 'public-read' : 'private'
            ]);
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    public function listFiles(string $directory, bool $recursive = false): array
    {
        // A minimal implementation, complex prefix/delimiter mapping left out for simplicity
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => $this->getPath($directory) ? $this->getPath($directory) . '/' : ''
        ]);
        
        $files = [];
        if (isset($result['Contents'])) {
            foreach ($result['Contents'] as $object) {
                $files[] = $object['Key'];
            }
        }
        return $files;
    }

    public function listDirectories(string $directory, bool $recursive = false): array
    {
        $result = $this->client->listObjectsV2([
            'Bucket' => $this->bucket,
            'Prefix' => $this->getPath($directory) ? $this->getPath($directory) . '/' : '',
            'Delimiter' => '/'
        ]);
        
        $dirs = [];
        if (isset($result['CommonPrefixes'])) {
            foreach ($result['CommonPrefixes'] as $prefix) {
                $dirs[] = $prefix['Prefix'];
            }
        }
        return $dirs;
    }

    public function url(string $path): string
    {
        return $this->client->getObjectUrl($this->bucket, $this->getPath($path));
    }

    public function temporaryUrl(string $path, \DateTimeInterface $expiration, array $options = []): string
    {
        $command = $this->client->getCommand('GetObject', array_merge([
            'Bucket' => $this->bucket,
            'Key' => $this->getPath($path)
        ], $options));

        $request = $this->client->createPresignedRequest($command, $expiration);
        return (string) $request->getUri();
    }
}
