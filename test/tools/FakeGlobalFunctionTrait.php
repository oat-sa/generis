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
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA;
 *
 *
 */

namespace oat\generis\test\tools;


use phpmock\MockBuilder;

trait FakeGlobalFunctionTrait
{
    /**
     * Returns the faked global function mock. (not working in global context)
     *
     * @param string $functionName       The global function name to mock. (time, json_decode, etc.)
     * @param string $contextClassName   The context of the mock. (the class where the global function is used)
     * @param mixed  $returnValue        The return value of the global function.
     *
     * @return \phpmock\Mock
     *
     * @throws \ReflectionException
     */
    public function getFakeFunctionMock($functionName, $contextClassName, $returnValue)
    {
        $nameSpace = (new \ReflectionClass($contextClassName))->getNamespaceName();

        $builder = new MockBuilder();
        $builder->setNamespace($nameSpace)
            ->setName($functionName)
            ->setFunction(
                function () use ($returnValue) {
                    return $returnValue;
                }
            )
        ;

        return $builder->build();
    }
}
