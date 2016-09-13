<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace oat\generis\model\kernel\persistence\smoothsql\search\exception;

/**
 * Query invalid value exception
 *
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
class InvalidValueException extends \InvalidArgumentException 
    implements \common_exception_UserReadableException {
    
    /**
     * Get the human-readable message for the end-user. It is supposed
     * to be translated and does not contain any confidential information
     * about the system and its sensitive data.
     *
     * @return string A human-readable message.
     */
    public function getUserMessage()
    {
        return __("Wrong Value, try again please or contact your system administrator");
    }
    
}
