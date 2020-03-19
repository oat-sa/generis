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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 *
 * Content type accepted can't be satisfied
 * @access public
 * @author Gyula Szucs, <gyula@taotesting.com>
 * @package generis
 */
class common_exception_ValidationFailed extends common_exception_BadRequest
{
    /**
     * Name of the failed field.
     *
     * @var string
     */
    private $field;

    /**
     * common_exception_ValidationFailed constructor.
     *
     * @param string $field
     * @param string|null $message
     * @param int $code
     */
    public function __construct($field, $message = null, $code = 0)
    {
        $this->field = $field;

        if (!$message) {
            $message = sprintf("Validation for field '%s' has failed.", $field);
        }

        parent::__construct($message, $code);
    }

    /**
     * @return string
     */
    public function getField()
    {
        return $this->field;
    }

    /**
     * @return string
     */
    public function getUserMessage()
    {
        return __("Validation for field '%s' has failed.", $this->field);
    }
}
