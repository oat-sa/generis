<?php

use oat\oatbox\cache\KeyValueCache;

/**
 * The default cache implementation
 */

return new KeyValueCache([
    KeyValueCache::OPTION_PERSISTENCE => 'cache'
]);
