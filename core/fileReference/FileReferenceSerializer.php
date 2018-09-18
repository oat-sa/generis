<?php

namespace oat\generis\model\fileReference;

use oat\oatbox\filesystem\FileSystemHandler;

interface FileReferenceSerializer
{
    const SERVICE_ID = 'generis/fileReferenceSerializer';

    /**
     * Serialize filesystem abstraction to a serial
     * Abstraction should be \oat\oatbox\filesystem\Directory or \oat\oatbox\filesystem\File
     *
     * @param string $abstraction
     * @return string $serial
     */
    public function serialize($abstraction);

    /**
     * Returns the file/directory serialized
     *
     * @param $serial
     * @return FileSystemHandler
     */
    public function unserialize($serial);

    /**
     * Get the \oat\oatbox\filesystem\File associated to the serial
     *
     * @param $serial
     * @return \oat\oatbox\filesystem\File
     */
    public function unserializeFile($serial);
    
    /**
     * Get the \oat\oatbox\filesystem\Directory associated to the serial
     *
     * @param $serial
     * @return \oat\oatbox\filesystem\Directory
     */
    public function unserializeDirectory($serial);

    /**
     * Delete the reference
     * @param $serial
     * @return boolean
     */
    public function cleanUp($serial);
}