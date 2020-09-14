<?php

use oat\oatbox\user\UserTimezoneService;
use oat\oatbox\user\UserTimezoneServiceInterface;

return new UserTimezoneService([
    UserTimezoneServiceInterface::OPTION_USER_TIMEZONE_ENABLED => true,
]);
