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
<<<<<<< HEAD
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
=======
 * Copyright (c) 2016-2017 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
>>>>>>> a71b171b1e09a059082dfe144c25a1624d5466ad
 * 
 */

namespace oat\oatbox\extension;

use oat\oatbox\action\Action;
use oat\oatbox\log\LoggerServiceTrait;
use oat\oatbox\service\ServiceManagerAwareInterface;
use oat\oatbox\service\ServiceManagerAwareTrait;
use Psr\Log\LoggerAwareInterface;

/**
 * abstract base for extension actions
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractAction implements Action, ServiceManagerAwareInterface, LoggerAwareInterface
{
    use ServiceManagerAwareTrait;
    use LoggerServiceTrait;
}
