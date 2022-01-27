<?php

/**
 * Default config header
 */

return [
    'generator'  => [
        'chars'      => 'abcdefghijklmnopqrstuvwxyz',
        'nums'       => '0123456789',
        'syms'       => '!@#$%^&*()-+?',
        //excludes this characters from random password to prevent confusion
        'similar'    => 'iIl1Oo0',
        //used for human readable generator
        'dictionary' => '/usr/share/dict/words'
    ]
];
