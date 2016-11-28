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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\scripts;

use oat\awsTools\AwsClient;
use oat\oatbox\action\Action;
use common_report_Report as Report;
use oat\oatbox\service\ServiceManager;

class registerAwsClient implements Action
{
    public function __invoke($params)
    {
        if(count($params) !== 4){
            return Report::createFailure('You should provide region and version');
        }

        $region = $params['region'];
        $version = $params['version'];
        $key = $params['key'];
        $secret = $params['secret'];

        $serviceManager = ServiceManager::getServiceManager();
        $awsClient = new AwsClient([
            'credentials' => [
                'key' => $key,
                'secret' => $secret,
            ],
            'region' => $region,
            'version' => $version
        ]);

        $serviceManager->register('generis/awsClient', $awsClient);

        return Report::createSuccess();
    }
}