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
 * Copyright (c) 2013 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 *
 * @author Lionel Lecaque  <lionel@taotesting.com>
 * @license GPLv2
 * @package 
 * @subpackage 
 *
 */
class common_persistence_PhpFileDriver implements common_persistence_KvDriver
{
    private $directory;
    private $levels;
    
    /**
     * Workaround to prevent opcaches from providing
     * deprecated values
     * 
     * @var array
     */
    private $cache = array();
    
    /**
     * Nr of subfolder levels in order to prevent filesystem bottlenecks 
     * 
     * @var int
     */
    const DEFAULT_LEVELS = 3;

    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    function connect($id, array $params)
    {
        $this->directory = isset($params['dir']) 
            ? $params['dir'].($params['dir'][strlen($params['dir'])-1] == DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR)
            : GENERIS_FILES_PATH.$id.DIRECTORY_SEPARATOR;
        $this->levels = isset($params['levels']) ? $params['levels'] : self::DEFAULT_LEVELS;
        return new common_persistence_KeyValuePersistence($params, $this);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::set()
     */
    public function set($id, $value, $ttl = null)
    {
        if (!is_null($ttl)) {
            throw new common_exception_NotImplemented('TTL not implemented in '.__CLASS__);
        } else {
            $filePath = $this->getPath($id);
            $dirname = dirname($filePath);
            if (!file_exists($dirname)) {
                mkdir($dirname, 0700, true);
            }
            $string = "<?php return ".common_Utils::toPHPVariableString($value).";";
            // we first open with 'c' in case the flock fails
            // 'w' would empty the file that someone else might be working on
            if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)){
            
                // We first need to truncate.
                ftruncate($fp, 0);
            
                $success = fwrite($fp, $string);
                @flock($fp, LOCK_UN);
                @fclose($fp);
                if ($success) {
                    // OPcache workaround
                    $this->cache[$id] = $value;
                }
                return $success !== false;
            } else {
                return false;
            }
        }
        
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::get()
     */
    public function get($id) {
        if (isset($this->cache[$id])) {
            // OPcache workaround
            return $this->cache[$id];
        }
        $value = @include $this->getPath($id);
        return $value;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::exists()
     */
    public function exists($id) {
        return file_exists($this->getPath($id));
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::del()
     */
    public function del($id) {
        if (isset($this->cache[$id])) {
            // OPcache workaround
            unset($this->cache[$id]);
        }
        return @unlink($this->getPath($id));
    }

    /**
     * Map the provided key to a relativ path
     * 
     * @param string $key
     * @return string
     */
    private function getPath($key) {
        $encoded = md5($key);
        $returnValue = "";
        $len = strlen($encoded);
        for ($i = 0; $i < $len; $i++) {
            if ($i < $this->levels) {
                $returnValue .= $encoded[$i].DIRECTORY_SEPARATOR;
            } else {
                $returnValue .= $encoded[$i];
            } 
        }
        return  $this->directory.$returnValue.'.php';
    }

}
