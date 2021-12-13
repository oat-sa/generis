<?php

use oat\oatbox\user\UserLanguageService;

return new UserLanguageService([
    UserLanguageService::OPTION_LOCK_DATA_LANGUAGE => true,
    UserLanguageService::OPTION_DEFAULT_LANGUAGE => 'nb-NO',
]);
