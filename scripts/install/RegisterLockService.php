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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\generis\scripts\install;

use common_report_Report as Report;
use oat\oatbox\extension\InstallAction;
use oat\oatbox\mutex\LockService;
use Symfony\Component\Lock\Store\PdoStore;
use oat\oatbox\service\ServiceNotFoundException;

/**
 * Class RegisterLockService
 * @package oat\generis\scripts\install
 */
class RegisterLockService extends InstallAction
{
    /**
     * @param $params
     * @return Report
     * @throws \common_Exception
     * @throws \common_exception_FileReadFailedException
     * @throws \common_exception_InconsistentData
     * @throws \common_exception_NotImplemented
     * @throws \oat\oatbox\service\exception\InvalidServiceManagerException
     */
    public function __invoke($params)
    {
        try {
            $service = $this->getServiceManager()->get(LockService::SERVICE_ID);
        } catch (ServiceNotFoundException $e) {
            $service = new LockService([
                LockService::OPTION_PERSISTENCE_CLASS => PdoStore::class,
                LockService::OPTION_PERSISTENCE_OPTIONS => 'default',
            ]);
            $this->getServiceManager()->register(LockService::SERVICE_ID, $service);
        }

        $service->install();
        return new Report(Report::TYPE_SUCCESS, 'LockService service is registered');
    }
}
