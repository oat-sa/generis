<?php

namespace oat\oatbox\filesystem\utils\serializer;

interface FileSerializer
{
    const SERVICE_ID = 'generis/fileSerializer';

    /**
     * Serialize filesystem abstraction to a serial
     *
     * @param string $abstraction
     * @return string $serial
     */
    public function serialize($abstraction);

    /**
     * Unserialize a serial, filename to a \oat\oatbox\filesystem\File
     *
     * @param $serial
     * @return \oat\oatbox\filesystem\File
     */
    public function unserialize($serial);

    /**
     * Serialize a serial, path to a \oat\oatbox\filesystem\Directory
     *
     * @param $serial
     * @return \oat\oatbox\filesystem\Directory
     */
    public function unserializeDirectory($serial);
}