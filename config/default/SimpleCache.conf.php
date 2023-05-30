<?php

/**
 * The default cache implementation
 */

use oat\oatbox\cache\KeyValueCache;

return new KeyValueCache([
    KeyValueCache::OPTION_PERSISTENCE => 'cache'
]);
