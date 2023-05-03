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
 * Copyright (c) 2002-2008 (original work) Public Research Centre Henri Tudor & University of Luxembourg
 *                         (under the project TAO & TAO2);
 *               2008-2010 (update and modification) Deutsche Institut für Internationale Pädagogische Forschung
 *                         (under the project TAO-TRANSFER);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor
 *                         (under the project TAO-SUSTAIN & TAO-DEV);
 */

/**
 * Short description of class helpers_VersionedFile
 *
 * @access public
 *
 * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
 *
 * @package helpers
 */
class helpers_VersionedFile
{
    // --- ASSOCIATIONS ---

    // --- ATTRIBUTES ---

    // --- OPERATIONS ---

    /**
     * Short description of method deleteFiles
     *
     * @access public
     *
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     *
     * @param  array files
     * @param mixed $files
     *
     * @return boolean
     */
    public static function deleteFiles($files = [])
    {
        $returnValue = (bool) false;

        return (bool) $returnValue;
    }

    /**
     * Short description of method rmWorkingCopy
     *
     * @access public
     *
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     *
     * @param  string path
     * @param  boolean recursive
     * @param mixed $path
     * @param mixed $recursive
     *
     * @return boolean
     */
    public static function rmWorkingCopy($path, $recursive = true)
    {
        $returnValue = (bool) false;

        if (is_file($path)) {
            if (preg_match('/^\//', $path)) {
                $returnValue = @unlink($path);
            }
        } elseif ($recursive) {
            if (is_dir($path)) {
                $iterator = new DirectoryIterator($path);

                foreach ($iterator as $fileinfo) {
                    if (!$fileinfo->isDot()) {
                        self::rmWorkingCopy($fileinfo->getPathname(), true);
                    }
                    unset($fileinfo);
                }
                unset($iterator);
                $returnValue = @rmdir($path);
            }
        }

        return (bool) $returnValue;
    }

    /**
     * Short description of method cpWorkingCopy
     *
     * @access public
     *
     * @author Somsack Sipasseuth, <somsack.sipasseuth@tudor.lu>
     *
     * @param  string source
     * @param  string destination
     * @param  boolean recursive
     * @param  boolean ignoreSystemFiles
     * @param mixed $source
     * @param mixed $destination
     * @param mixed $recursive
     * @param mixed $ignoreSystemFiles
     *
     * @return boolean
     */
    public static function cpWorkingCopy($source, $destination, $recursive = true, $ignoreSystemFiles = true)
    {
        $returnValue = (bool) false;

        if (file_exists($source)) {
            if (is_dir($source) && $recursive) {
                foreach (scandir($source) as $file) {
                    if ($file != '.' && $file != '..' && $file != '.svn') {
                        if (!$ignoreSystemFiles && $file[0] == '.') {
                            continue;
                        }
                        self::cpWorkingCopy(
                            $source . '/' . $file,
                            $destination . '/' . $file,
                            true,
                            $ignoreSystemFiles
                        );
                    }
                }
            } else {
                if (is_dir(dirname($destination))) {
                    $returnValue = copy($source, $destination);
                } elseif ($recursive) {
                    if (mkdir(dirname($destination), 0775, true)) {
                        $returnValue = self::cpWorkingCopy($source, $destination, false, $ignoreSystemFiles);
                    }
                }
            }
        }

        return (bool) $returnValue;
    }
}
