<?php
/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\filesystem;

use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\StreamWrapper;
use League\Flysystem\FileNotFoundException;
use Psr\Http\Message\StreamInterface;
use League\Flysystem\FileExistsException;

class File extends FileSystemHandler
{
    /**
     * Get basename of $this file
     *
     * @return string
     */
    public function getBasename()
    {
        return basename($this->getPrefix());
    }

    /**
     * Get mimetype of $this file
     *
     * @return string
     */
    public function getMimeType()
    {
        try {
            $mimeType = $this->getFileSystem()->getMimetype($this->getPrefix());
            if ($mimeType == 'text/plain' && substr($this->getPrefix(), -4) == '.css') {
                $mimeType = 'text/css';
            }
            return $mimeType;
        } catch (FileNotFoundException $e) {
        }
        return false;
    }

    /**
     * Get size of $this file
     *
     * @return bool|false|int
     */
    public function getSize()
    {
        return $this->getFileSystem()->getSize($this->getPrefix());
    }

    /**
     * Get metadata of $this file
     *
     * @return array|bool
     */
    public function getMetadata()
    {
        try {
            $path = $this->getPrefix();
            if ($this->getFileSystem()->has($path)) {
                return $this->getFileSystem()->get($path)->getMetadata();
            }

        } catch (FileNotFoundException $e) {
        }
        return false;
    }

    /**
     * Write a content into $this file, if not exists
     * $mixed content has to be string, resource, or PSR Stream
     * In case of Stream, $mixed has to be seekable and readable
     *
     * @param string|Resource|StreamInterface $mixed
     * @param null $mimeType
     * @return bool
     * @throws \common_Exception
     * @throws FileExistsException
     */
    public function write($mixed, $mimeType = null)
    {
        $config = (is_null($mimeType)) ? [] : ['ContentType' => $mimeType];

        if (is_string($mixed)) {
            return $this->getFileSystem()->write($this->getPrefix(), $mixed, $config);

        } elseif (is_resource($mixed)) {
            return $this->getFileSystem()->writeStream($this->getPrefix(), $mixed, $config);

        } elseif ($mixed instanceof StreamInterface) {
            if (!$mixed->isReadable()) {
                throw new \common_Exception('Stream is not readable. Write to filesystem aborted.');
            }
            if (!$mixed->isSeekable()) {
                throw new \common_Exception('Stream is not seekable. Write to filesystem aborted.');
            }
            $mixed->rewind();

            $resource = StreamWrapper::getResource($mixed);
            if (!is_resource($resource)) {
                throw new \common_Exception('Unable to create resource from the given stream. Write to filesystem aborted.');
            }
            return $this->getFileSystem()->writeStream($this->getPrefix(), $resource, $config);

        } else {
            throw new \InvalidArgumentException('Value to be written has to be: string, resource or StreamInterface, ' .
                '"' . gettype($mixed) . '" given.');
        }
    }

    /**
     * Update a content into $this file, if exists
     * $mixed content has to be string, resource, or PSR Stream
     * In case of Stream, $mixed has to be seekable and readable
     *
     * @param $mixed
     * @param null $mimeType
     * @return bool
     * @throws \FileNotFoundException
     * @throws \common_Exception
     */
    public function update($mixed, $mimeType = null)
    {
        if (!$this->exists()) {
            throw new \FileNotFoundException('File "' . $this->getPrefix() . '" not found."');
        }

        \common_Logger::i('Writting in ' . $this->getPrefix());
        $config = (is_null($mimeType)) ? [] : ['ContentType' => $mimeType];

        if (is_string($mixed)) {
            return $this->getFileSystem()->update($this->getPrefix(), $mixed, $config);
        }

        if (is_resource($mixed)) {
            return $this->getFileSystem()->updateStream($this->getPrefix(), $mixed, $config);
        }

        if ($mixed instanceof StreamInterface) {
            if (!$mixed->isReadable()) {
                throw new \common_Exception('Stream is not readable. Write to filesystem aborted.');
            }
            if (!$mixed->isSeekable()) {
                throw new \common_Exception('Stream is not seekable. Write to filesystem aborted.');
            }
            $mixed->rewind();

            $resource = StreamWrapper::getResource($mixed);
            if (!is_resource($resource)) {
                throw new \common_Exception('Unable to create resource from the given stream. Write to filesystem aborted.');
            }
            return $this->getFileSystem()->updateStream($this->getPrefix(), $resource, $config);
        }

        throw new \InvalidArgumentException('Value to be written has to be: string, resource or StreamInterface');
    }

    /**
     * Put a content into $this file, if exists or not
     * $mixed content has to be string, resource, or PSR Stream
     * In case of Stream, $mixed has to be seekable and readable
     *
     * @param string|Resource|StreamInterface $mixed
     * @param null $mimeType
     * @return bool
     * @throws \common_Exception
     */
    public function put($mixed, $mimeType = null)
    {
        \common_Logger::i('Writting in ' . $this->getPrefix());
        $config = (is_null($mimeType)) ? [] : ['ContentType' => $mimeType];

        if (is_string($mixed)) {
            return $this->getFileSystem()->put($this->getPrefix(), $mixed, $config);
        }

        if (is_resource($mixed)) {
            return $this->getFileSystem()->putStream($this->getPrefix(), $mixed, $config);
        }

        if ($mixed instanceof StreamInterface) {
            if (!$mixed->isReadable()) {
                throw new \common_Exception('Stream is not readable. Write to filesystem aborted.');
            }
            if (!$mixed->isSeekable()) {
                throw new \common_Exception('Stream is not seekable. Write to filesystem aborted.');
            }
            $mixed->rewind();

            $resource = StreamWrapper::getResource($mixed);
            if (!is_resource($resource)) {
                throw new \common_Exception('Unable to create resource from the given stream. Write to filesystem aborted.');
            }
            return $this->getFileSystem()->putStream($this->getPrefix(), $resource, $config);
        }

        throw new \InvalidArgumentException('Value to be written has to be: string, resource or StreamInterface');
    }

    /**
     * Return content of file as string
     *
     * @return false|string
     */
    public function read()
    {
        return $this->getFileSystem()->read($this->getPrefix());
    }

    /**
     * Return content of file as PHP stream (resource)
     *
     * @return false|resource
     */
    public function readStream()
    {
        return $this->getFileSystem()->readStream($this->getPrefix());
    }

    /**
     * Return content of file as PSR-7 stream
     *
     * @return StreamInterface
     */
    public function readPsrStream()
    {
        return new Stream($this->getFileSystem()->readStream($this->getPrefix()));
    }

    /**
     * Check if $this file exists && is file
     *
     * @return bool
     */
    public function exists()
    {
        try {
            $path = $this->getPrefix();

            if ($this->getFileSystem()->has($path)) {
                $metadata = $this->getFileSystem()->getMetadata($this->getPrefix());
                return $metadata['type'] === 'file';
            }
        } catch (FileNotFoundException $e) {
        }
        return false;
    }

    /**
     * Delete $this file
     *
     * @return bool
     */
    public function delete()
    {
        try {
            return $this->getFileSystem()->delete($this->getPrefix());
        } catch (FileNotFoundException $e) {}

        return false;
    }
}