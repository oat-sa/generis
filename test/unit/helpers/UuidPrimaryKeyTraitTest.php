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
 * Copyright (c) 2019 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author "Julien Sébire, <julien@taotesting.com>"
 * @license GPLv2
 * @package generis
 *
 */

namespace oat\generis\test\unit\helpers;

use oat\generis\Helper\UuidPrimaryKeyTrait;
use oat\generis\test\TestCase;
use phpmock\MockBuilder;

class UuidPrimaryKeyTraitTest extends TestCase
{
    use UuidPrimaryKeyTrait;

    public function testGetUniquePrimaryKey()
    {
        $builder = new MockBuilder();
        $builder->setNamespace('oat\generis\Helper')
            ->setName('uniqid')
            ->setFunction(
                function () {
                    return 'a supposed uuid';
                }
            );
        $mock = $builder->build();
        $mock->enable();

        $this->assertEquals('diuu desoppus a', $this->getUniquePrimaryKey());

        $mock->disable();
    }
}
