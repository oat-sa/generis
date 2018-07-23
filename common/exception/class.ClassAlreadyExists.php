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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA;
 *
 */

/**
 * Class common_exception_ClassAlreadyExists
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class common_exception_ClassAlreadyExists extends common_Exception
{

    /** @var  \core_kernel_classes_Class */
    private $class;

    /**
     * common_exception_ClassAlreadyExists constructor.
     * @param core_kernel_classes_Class $class
     * @param null $message
     */
    public function __construct(\core_kernel_classes_Class $class, $message = null)
    {
        $this->class = $class;
        if ($message === null) {
            $message = 'Class already exists. Class uri: ' .$class->getUri();
        }
        parent::__construct($message);

    }

    /**
     * @return core_kernel_classes_Class
     */
    public function  getClass()
    {
        return $this->class;
    }
} 