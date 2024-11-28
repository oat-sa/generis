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
 * Copyright (c) 2016-2020 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 */

namespace oat\oatbox\filesystem;

use common_Logger;
use GuzzleHttp\Psr7\Stream;
use GuzzleHttp\Psr7\StreamWrapper;
use Psr\Http\Message\StreamInterface;
use tao_helpers_File;

class File extends FileSystemHandler
{
    public function getBasename(): string
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
            $mimeType = $this->getFileSystem()->mimeType($this->getPrefix());
            $suffix =  substr($this->getPrefix(), -4);
            if ($mimeType === 'text/plain' &&  $suffix === '.css') {
                $mimeType = 'text/css';
            }

            if (in_array($suffix, ['.svg', 'svgz'])) {
                $mimeType = tao_helpers_File::MIME_SVG;
            }

            return $mimeType;
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
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
        return $this->getFileSystem()->fileSize($this->getPrefix());
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
     */
    public function write($mixed, $mimeType = null)
    {
        $config = (is_null($mimeType)) ? [] : ['ContentType' => $mimeType];

        try {
            if (is_string($mixed)) {
                $this->getFileSystem()->write($this->getPrefix(), $mixed, $config);
            } elseif (is_resource($mixed)) {
                $this->getFileSystem()->writeStream($this->getPrefix(), $mixed, $config);
            } elseif ($mixed instanceof StreamInterface) {
                if (!$mixed->isReadable()) {
                    throw new \common_Exception('Stream is not readable. Write to filesystem aborted.');
                }
                if ($mixed->isSeekable()) {
                    $mixed->rewind();
                } elseif ($mixed->eof()) {
                    throw new \common_Exception(
                        'Stream is not seekable and is already processed. Write to filesystem aborted.'
                    );
                }

                $resource = StreamWrapper::getResource($mixed);
                if (!is_resource($resource)) {
                    throw new \common_Exception(
                        'Unable to create resource from the given stream. Write to filesystem aborted.'
                    );
                }
                $this->getFileSystem()->writeStream($this->getPrefix(), $resource, $config);
            } else {
                throw new \InvalidArgumentException(sprintf(
                    'Value to be written has to be: string, resource or StreamInterface, "%s" given.',
                    gettype($mixed)
                ));
            }
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
            return false;
        }

        return true;
    }

    /**
     * Update a content into $this file, if exists
     * $mixed content has to be string, resource, or PSR Stream
     * In case of Stream, $mixed has to be seekable and readable
     *
     * @param $mixed
     * @param null $mimeType
     * @return bool
     * @throws \common_Exception
     */
    public function update($mixed, $mimeType = null)
    {
        if (!$this->exists()) {
            throw new \RuntimeException('File "' . $this->getPrefix() . '" not found."');
        }

        common_Logger::i('Writing in ' . $this->getPrefix());

        return $this->write($mixed, $mimeType);
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
        common_Logger::i('Writting in ' . $this->getPrefix());

        return $this->write($mixed, $mimeType);
    }

    /**
     * Return content of file as string
     *
     * @return false|string
     */
    public function read()
    {
        try {
            return $this->getFileSystem()->read($this->getPrefix());
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
        }

        return false;
    }

    /**
     * Return content of file as PHP stream (resource)
     *
     * @return false|resource
     */
    public function readStream()
    {
        try {
            return $this->getFileSystem()->readStream($this->getPrefix());
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
        }

        return false;
    }

    /**
     * Return content of file as PSR-7 stream
     *
     * @return StreamInterface
     */
    public function readPsrStream()
    {
        $resource = null;
        try {
            $resource = $this->getFileSystem()->readStream($this->getPrefix());
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
        }

        return new Stream($resource);
    }

    public function exists(): bool
    {
        try {
            return $this->getFileSystem()->fileExists($this->getPrefix());
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
        }
        return false;
    }

    public function delete(): bool
    {
        try {
            $this->getFileSystem()->delete($this->getPrefix());
            return true;
        } catch (FilesystemException $e) {
            $this->logWarning($e->getMessage());
        }

        return false;
    }
}
