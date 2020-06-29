<?php

/**
 * Copyright 2016 Open Assessment Technologies SA
 *
 * This file is part of the Tao AWS tools.
 *
 * This program is free software: you can redistribute it and/or modify it under the terms of the GNU
 * General Public License as published by the Free Software Foundation, either version 3 of the License,
 * or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even
 * the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General
 * Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this package.
 * If not, see http://www.gnu.org/licenses/.
 *
 */

namespace oat\oatbox\filesystem\wrapper;

use oat\oatbox\service\ConfigurableService;
use League\Flysystem\AdapterInterface;
use oat\oatbox\filesystem\utils\FlyWrapperTrait;
use oat\oatbox\log\LoggerAwareTrait;
use Google\Cloud\Storage\StorageClient;
use Superbalist\Flysystem\GoogleStorage\GoogleStorageAdapter;

/**
 *
 * @author Joel Bout
 */
class GoogleStorageWrapper extends ConfigurableService implements AdapterInterface
{
    use FlyWrapperTrait;
    use LoggerAwareTrait;
    
    const OPTION_BUCKET = 'bucket';

    const OPTION_CLIENT_CONFIG = 'clientConfig';

    private $adapter;
    
    /**
     * @return StorageClient
     */
    private function getClient()
    {
        return new StorageClient($this->getOption(self::OPTION_CLIENT_CONFIG));
    }
    
    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        if (is_null($this->adapter)) {
            $client = $this->getClient();
            $bucket = $client->bucket($this->getOption('bucket'));
            $adapter = new GoogleStorageAdapter($client, $bucket);
            $this->adapter = $adapter;
        }
        return $this->adapter;
    }
}
