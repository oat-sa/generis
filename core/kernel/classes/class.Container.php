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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * Could be either a core_kernel_classes_Resource or core_kernel_classes_Literal
 *
 * @access public
 *
 * @author patrick.plichart@tudor.lu
 *
 * @package generis
 */
class core_kernel_classes_Container extends common_Object
{
    /**
     * Please serialize the value or the URI but not the resource or literal itself
     *
     * @deprecated
     *
     * @throws common_exception_NotImplemented
     */
    public function __serialize()
    {
        throw new common_exception_NotImplemented('Do not serialize resources or literals');
    }
}
