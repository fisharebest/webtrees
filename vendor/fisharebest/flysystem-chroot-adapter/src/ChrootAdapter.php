<?php
/**
 * @author    Greg Roach <fisharebest@gmail.com>
 * @copyright Copyright (c) 2019
 * @licence   MIT
 */

namespace Fisharebest\Flysystem\Adapter;

use League\Flysystem\AdapterInterface;
use League\Flysystem\Config;
use League\Flysystem\FilesystemInterface;

/**
 * Create a subtree from an existing filesystem.
 */
class ChrootAdapter implements AdapterInterface
{
    /** @var string[] Metadata attributes that may need prefixes removing */
    private static $ATTRIBUTES_WITH_PREFIX = ['dirname', 'path'];

    /** @var AdapterInterface */
    private $adapter;

    /** @var string */
    private $prefix;

    /**
     * ChrootAdapter constructor.
     *
     * @param FilesystemInterface $filesystem
     * @param string              $prefix e.g. 'some/prefix'
     */
    public function __construct(FilesystemInterface $filesystem, $prefix = '')
    {
        $this->adapter = $filesystem->getAdapter();
        $this->prefix  = trim($prefix, '/') . '/';
    }

    /**
     * Check whether a file exists.
     *
     * @param string $path
     *
     * @return array|bool|null
     */
    public function has($path)
    {
        return $this->adapter->has($this->prefix . $path);
    }

    /**
     * Read a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function read($path)
    {
        return $this->adapter->read($this->prefix . $path);
    }

    /**
     * Read a file as a stream.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function readStream($path)
    {
        return $this->adapter->readStream($this->prefix . $path);
    }

    /**
     * List contents of a directory.
     *
     * @param string $directory
     * @param bool   $recursive
     *
     * @return array
     */
    public function listContents($directory = '', $recursive = false)
    {
        $directory = trim($this->prefix . $directory, '/');
        $contents  = $this->adapter->listContents($directory, $recursive);

        return array_map([$this, 'removePrefixFromMetadata'], $contents);
    }

    /**
     * Get all the meta data of a file or directory.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMetadata($path)
    {
        $metadata = $this->adapter->getMetadata($this->prefix . $path);

        return $this->removePrefixFromMetadata($metadata);
    }

    /**
     * Get the size of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getSize($path)
    {
        return $this->adapter->getSize($this->prefix . $path);
    }

    /**
     * Get the mimetype of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getMimetype($path)
    {
        return $this->adapter->getMimetype($this->prefix . $path);
    }

    /**
     * Get the last modified time of a file as a timestamp.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getTimestamp($path)
    {
        return $this->adapter->getTimestamp($this->prefix . $path);
    }

    /**
     * Get the visibility of a file.
     *
     * @param string $path
     *
     * @return array|false
     */
    public function getVisibility($path)
    {
        return $this->adapter->getVisibility($this->prefix . $path);
    }

    /**
     * Write a new file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function write($path, $contents, Config $config)
    {
        return $this->adapter->write($this->prefix . $path, $contents, $config);
    }

    /**
     * Write a new file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function writeStream($path, $resource, Config $config)
    {
        return $this->adapter->writeStream($this->prefix . $path, $resource, $config);
    }

    /**
     * Update a file.
     *
     * @param string $path
     * @param string $contents
     * @param Config $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function update($path, $contents, Config $config)
    {
        return $this->adapter->update($this->prefix . $path, $contents, $config);
    }

    /**
     * Update a file using a stream.
     *
     * @param string   $path
     * @param resource $resource
     * @param Config   $config Config object
     *
     * @return array|false false on failure file meta data on success
     */
    public function updateStream($path, $resource, Config $config)
    {
        return $this->adapter->updateStream($this->prefix . $path, $resource, $config);
    }

    /**
     * Rename a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function rename($path, $newpath)
    {
        return $this->adapter->rename($this->prefix . $path, $this->prefix . $newpath);
    }

    /**
     * Copy a file.
     *
     * @param string $path
     * @param string $newpath
     *
     * @return bool
     */
    public function copy($path, $newpath)
    {
        return $this->adapter->copy($this->prefix . $path, $this->prefix . $newpath);
    }

    /**
     * Delete a file.
     *
     * @param string $path
     *
     * @return bool
     */
    public function delete($path)
    {
        return $this->adapter->delete($this->prefix . $path);
    }

    /**
     * Delete a directory.
     *
     * @param string $dirname
     *
     * @return bool
     */
    public function deleteDir($dirname)
    {
        return $this->adapter->deleteDir($this->prefix . $dirname);
    }

    /**
     * Create a directory.
     *
     * @param string $dirname directory name
     * @param Config $config
     *
     * @return array|false
     */
    public function createDir($dirname, Config $config)
    {
        return $this->adapter->createDir($this->prefix . $dirname, $config);
    }

    /**
     * Set the visibility for a file.
     *
     * @param string $path
     * @param string $visibility
     *
     * @return array|false file meta data
     */
    public function setVisibility($path, $visibility)
    {
        return $this->adapter->setVisibility($this->prefix . $path, $visibility);
    }

    /**
     * Strip the prefix from metadata attributes.
     *
     * @param array $metadata
     *
     * @return array
     */
    private function removePrefixFromMetadata(array $metadata)
    {
        foreach (self::$ATTRIBUTES_WITH_PREFIX as $attribute) {
            if (isset($metadata[$attribute])) {
                $metadata[$attribute] = substr($metadata[$attribute], strlen($this->prefix));
            }
        }

        return $metadata;
    }
}
