<?php

use oat\oatbox\cache\GcpTokenCacheItemPool;

return new GcpTokenCacheItemPool(
    [
        GcpTokenCacheItemPool::OPTION_PERSISTENCE => 'gcpTokenKeyValue',
        GcpTokenCacheItemPool::OPTION_ENABLE_DEBUG => false,
        GcpTokenCacheItemPool::OPTION_DISABLE_WRITE => true,
        GcpTokenCacheItemPool::OPTION_TOKEN_CACHE_KEY => 'GCP-TOKEN-SANCTUARY:GOOGLE_AUTH_PHP_GCE',
    ]
);
