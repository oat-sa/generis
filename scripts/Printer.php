<?php

namespace oat\generis\scripts;

interface Printer
{
    const SERVICE_ID = 'generis/printer';

    public function printWord();
}