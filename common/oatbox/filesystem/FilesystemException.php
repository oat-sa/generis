<?php

namespace oat\oatbox\filesystem;

use League\Flysystem\FilesystemException as FlyFilesystemException;
use RuntimeException;

class FilesystemException extends RuntimeException implements FlyFilesystemException
{
}
