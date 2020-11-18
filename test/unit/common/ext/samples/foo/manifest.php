<?php

return [
    'name' => 'foo',
    'label' => 'Foo',
    'description' => 'Manifest file sample',
    'version' => '0.0.1',
    'author' => 'Open Assessment Technologies, CRP Henri Tudor',
    'requires' => [],
    'install' => [],
    'update' => \oat\generis\test\unit\common\ext\samples\UpdaterMock::class,
];
