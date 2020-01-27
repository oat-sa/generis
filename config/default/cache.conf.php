<?php

/**
 * The default cache implementation
 */

return new common_cache_KeyValueCache([
    common_cache_FileCache::OPTION_PERSISTENCE => 'cache'
]);
