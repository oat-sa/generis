<?php

use oat\generis\model\kernel\persistence\smoothsql\search\ComplexSearchService;

return new core_kernel_persistence_smoothsql_SmoothModel([
    core_kernel_persistence_smoothsql_SmoothModel::OPTION_PERSISTENCE => 'default',
    core_kernel_persistence_smoothsql_SmoothModel::OPTION_READABLE_MODELS => [
        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_READ_ONLY_MODEL,
    ],
    core_kernel_persistence_smoothsql_SmoothModel::OPTION_WRITEABLE_MODELS => [
        core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
    ],
    // phpcs:disable Generic.Files.LineLength
    core_kernel_persistence_smoothsql_SmoothModel::OPTION_NEW_TRIPLE_MODEL => core_kernel_persistence_smoothsql_SmoothModel::DEFAULT_WRITABLE_MODEL,
    // phpcs:enable Generic.Files.LineLength
    core_kernel_persistence_smoothsql_SmoothModel::OPTION_SEARCH_SERVICE => ComplexSearchService::SERVICE_ID,
]);
