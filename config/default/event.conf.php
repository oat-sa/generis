<?php
return new \oat\oatbox\event\EventManager([
    'listeners' => [
        'oat\\generis\\model\\data\\event\\ResourceCreated' => [
            ['oat\\generis\\model\\data\\permission\\PermissionManager', 'catchEvent']
        ]
    ]
]);
