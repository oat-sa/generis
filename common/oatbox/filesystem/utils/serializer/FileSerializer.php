<?php

namespace oat\oatbox\filesystem\utils\serializer;

interface FileSerializer
{
    const SERVICE_ID = 'generis/FileSerializer';

    public function serialize($filesystemId, $filepath);

    public function unserialize($fsUri, $filePath, $fileName);

    public function unserializeDirectory($fsUri, $path);
}