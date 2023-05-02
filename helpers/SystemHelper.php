<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2018 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 */

namespace oat\generis\Helper;

class SystemHelper
{
    /**
     * Returns the maximum size for fileuploads in bytes.
     *
     * @return int the upload file limit
     */
    public static function getFileUploadLimit()
    {
        $limits = [
            self::toBytes(ini_get('upload_max_filesize')),
            self::toBytes(ini_get('post_max_size')),
        ];

        if (ini_get('memory_limit') !== '-1') {
            $limits[] = self::toBytes(ini_get('memory_limit'));
        }

        return min($limits);
    }

    /**
     * Returns whenever or not Tao is installed on windows
     *
     * @return boolean
     */
    public static function isWindows()
    {
        return strtoupper(substr(PHP_OS, 0, 3)) == 'WIN';
    }

    /**
     * Returns the Operating System running TAO as a String.
     *
     * The returned value can be AFAIK:
     *
     * * 'WINNT' (Win XP, Windows NT, ...)
     * * 'Linux'
     * * 'FreeBSD'
     * * 'Darwin' (Mac OS X)
     * * 'CYGWIN_NT-5.1'
     * * 'HP-UX'
     * * 'IRIX64'
     * * 'NetBSD'
     * * 'OpenBSD'
     * * 'SunOS'
     * * 'Unix'
     * * 'WIN32'
     * * 'Windows'
     *
     * @return string the operating system that runs the script
     */
    public static function getOperatingSystem()
    {
        $returnValue = PHP_OS;

        return (string) $returnValue;
    }

    /**
     * Get the size in bytes of a PHP variable given as a string.
     *
     * @param string $phpSyntax the PHP syntax to describe the variable
     *
     * @return int the size in bytes
     */
    private static function toBytes($phpSyntax)
    {
        $val = trim($phpSyntax);
        $last = strtolower($val[strlen($val) - 1]);

        if (!is_numeric($last)) {
            $val = substr($val, 0, -1);

            switch ($last) {
                case 'g':
                    $val *= 1024;
                    // no break
                case 'm':
                    $val *= 1024;
                    // no break
                case 'k':
                    $val *= 1024;
            }
        }

        return (int)$val;
    }
}
