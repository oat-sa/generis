<?php
/**
 * Default config header
 */
return array(
    'length'     => 4,
    'upper'      => false,
    'number'     => true,
    'spec'       => false,
    //used for human readable generator
    'dictionary' => '/usr/share/dict/words',
    //excludes this characters from random password to prevent confusion
    'similar'    => 'iIl1Oo0',
    'chars'      => 'abcdefghijklmnopqrstuvwxyz',
    'nums'       => '0123456789',
    'syms'       => '!@#$%^&*()-+?'
);
