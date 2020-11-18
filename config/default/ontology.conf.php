<?php

use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;
use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;

return new \core_kernel_persistence_smoothsql_SmoothModel([
    SmoothModel::OPTION_PERSISTENCE => 'default',
    SmoothModel::OPTION_READABLE_MODELS =>
        [SmoothModel::DEFAULT_WRITABLE_MODEL, SmoothModel::DEFAULT_READ_ONLY_MODEL],
    SmoothModel::OPTION_WRITEABLE_MODELS =>
        [SmoothModel::DEFAULT_WRITABLE_MODEL],
    SmoothModel::OPTION_NEW_TRIPLE_MODEL =>
        SmoothModel::DEFAULT_WRITABLE_MODEL,
    SmoothModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID
]);