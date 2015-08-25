<?php
/**
 * Default config header
 *
 * To replace this add a file generis/conf/header/FsManager.conf.php
 */

return new common_cache_FileCache(array(
    common_cache_FileCache::OPTION_PERSISTENCE => 'cache'
));
