<?php

/**
 * Default config header created during install
 */

return new oat\oatbox\mutex\LockService(array(
    'persistence_class' => 'oat\\oatbox\\mutex\\NoLockStorage'
));
