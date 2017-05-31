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
 * Copyright (c) 2017 (original work) Open Assessment Technologies SA ;
 */

/**
 * Class common_exception_RestApi
 * Note: Exception message is supposed to be shown to the end user.
 * @author Aleh Hutnikau, <hutnikau@1pt.com>
 */
class common_exception_RestApi extends common_exception_ClientException
{
    protected $userMessage;

    /**
     * common_exception_RestApi constructor.
     * @param null $message
     * @param int $code
     */
    public function __construct($message = null, $code = 0)
    {
        parent::__construct($message, $code);
        $this->userMessage = $message;
    }

    /**
     * Get the human-readable message for the end-user. It is supposed
     * to be translated and does not contain any confidential information
     * about the system and its sensitive data.
     *
     * @return string A human-readable message.
     */
    public function getUserMessage()
    {
        if ($this->userMessage === null) {
            return __("Wrong request type, try again please or contact your system administrator");
        } else {
            return $this->userMessage;
        }
    }
}