<?php

use oat\generis\model\kernel\uri\Bin2HexUriProvider;

/**
 * Default config header
 *
 * To replace this add a file generis/conf/header/uriProvider.conf.php
 */

return new Bin2HexUriProvider([
    Bin2HexUriProvider::OPTION_NAMESPACE => LOCAL_NAMESPACE . '#'
]);
