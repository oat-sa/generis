<?php
/**
 * List of available commands
 *
 * @author Martijn Swinkels <martijn@taotesting.com>
 *
 * Commands can be added by initializing their class (which should extend \oat\generis\Model\ConsoleCommand)
 */
return [
    new \oat\tao\Model\Command\TaoUpdate(),
    new \oat\tao\Model\Command\TaoInstall(),
];