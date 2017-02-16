<?php
/**
 * Created by PhpStorm.
 * User: christophe
 * Date: 14/02/17
 * Time: 15:56
 */

namespace oat\oatbox\notification\exception;


class NotListedNotification extends \Exception
{

    public function __construct()
    {
        $message = 'unable to search into notification.';
        $code    = 0;
        parent::__construct($message, $code);
    }

}