<?php

namespace oat\oatbox\session;

use PHPSession;

trait LegacySessionUtils
{
    public function hasSessionAttribute($name)
    {
        return PHPSession::singleton()->hasAttribute($name);
    }

    public function getSessionAttribute($name)
    {
        return PHPSession::singleton()->getAttribute($name);
    }

    public function setSessionAttribute($name, $value)
    {
        PHPSession::singleton()->setAttribute($name, $value);
        return $this;
    }

    public function removeSessionAttribute($name)
    {
        PHPSession::singleton()->removeAttribute($name);
        return $this;
    }

    public function clearSession($global = true)
    {
        PHPSession::singleton()->clear($global);
        return $this;
    }
}