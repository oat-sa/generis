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

 *
 */
class common_persistence_PhpFileDriver implements common_persistence_KvDriver, common_persistence_Purgable
{
    /**
     * The op cache mode offset in the connection parameters.
     */
    const OP_CACHE_MODE_OFFSET = 'opCacheMode';

    /**
     * The TTL mode offset in the connection parameters.
     */
    const TTL_MODE_OFFSET = 'ttlMode';

    /**
     * The value offset in the cache record.
     */
    const CACHE_VALUE_OFFSET = 'value';

    /**
     * The expiration timestamp of the cache record.
     */
    const CACHE_EXPIRES_AT_OFFSET = 'expiresAt';

    /**
     * List of characters permited in filename
     * @var array
     */
    private static $ALLOWED_CHARACTERS = array('A' => '','B' => '','C' => '','D' => '','E' => '','F' => '','G' => '','H' => '','I' => '','J' => '','K' => '','L' => '','M' => '','N' => '','O' => '','P' => '','Q' => '','R' => '','S' => '','T' => '','U' => '','V' => '','W' => '','X' => '','Y' => '','Z' => '','a' => '','b' => '','c' => '','d' => '','e' => '','f' => '','g' => '','h' => '','i' => '','j' => '','k' => '','l' => '','m' => '','n' => '','o' => '','p' => '','q' => '','r' => '','s' => '','t' => '','u' => '','v' => '','w' => '','x' => '','y' => '','z' => '',0 => '',1 => '',2 => '',3 => '',4 => '',5 => '',6 => '',7 => '',8 => '',9 => '','_' => '','-' => '');

    /**
     * absolute path of the directory to use
     * ending on a directory seperator
     * 
     * @var string
     */
    private $directory;
    
    /**
     * Nr of subfolder levels in order to prevent filesystem bottlenecks
     * Only used in non human readable mode
     * 
     * @var int
     */
    private $levels;
    
    /**
     * Whenever or not the filenames should be human readable
     * FALSE by default for performance issues with many keys
     * 
     * @var boolean
     */
    private $humanReadable;

    /**
     * @var bool
     */
    private $opCacheMode;

    /**
     * @var bool
     */
    private $ttlMode;

    /**
     * Workaround to prevent opcaches from providing
     * deprecated values
     * 
     * @var array
     */
    private $cache = array();
    
    /**
     * Using 3 default levels, so the files get split up into
     * 16^3 = 4096 induvidual directories 
     * 
     * @var int
     */
    const DEFAULT_LEVELS = 3;
    
    const DEFAULT_MASK = 0700;

    /**
     * (non-PHPdoc)
     * @see common_persistence_Driver::connect()
     */
    public function connect($id, array $params)
    {
        $this->directory = isset($params['dir'])
            ? $params['dir'].($params['dir'][strlen($params['dir'])-1] === DIRECTORY_SEPARATOR ? '' : DIRECTORY_SEPARATOR)
            : FILES_PATH.'generis'.DIRECTORY_SEPARATOR.$id.DIRECTORY_SEPARATOR;
        $this->levels = isset($params['levels']) ? $params['levels'] : self::DEFAULT_LEVELS;
        $this->humanReadable = isset($params['humanReadable']) ? $params['humanReadable'] : false;

        // Sets ttl mode TRUE when the passed ttl mode is true.
        $this->setTtlMode(
            (isset($params[static::TTL_MODE_OFFSET]) && $params[static::TTL_MODE_OFFSET] == true)
        );

        // Sets op cache mode to false when the passed op cache mode is false.
        $this->setOpCacheMode(
            !(isset($params[static::OP_CACHE_MODE_OFFSET]) && $params[static::OP_CACHE_MODE_OFFSET] == false)
        );

        return new common_persistence_KeyValuePersistence($params, $this);
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::set()
     *
     * @throws common_exception_NotImplemented
     * @throws \common_exception_Error
     */
    public function set($id, $value, $ttl = null, $nx = false)
    {
        if ($this->isTtlMode()) {
            $value = [
                static::CACHE_VALUE_OFFSET      => $value,
                static::CACHE_EXPIRES_AT_OFFSET => $this->calculateExpiresAt($ttl),
            ];
        } else {
            if (null !== $ttl) {
                throw new common_exception_NotImplemented('TTL not implemented in '.__CLASS__);
            } elseif ($nx) {
                throw new common_exception_NotImplemented('NX not implemented in '.__CLASS__);
            } else {
            }
        }

        return $this->writeCacheFile($id, $value);
    }

    /**
     * Calculates and returns the expires at timestamp or null on empty ttl.
     *
     * @param $ttl
     *
     * @return int|null
     */
    protected function calculateExpiresAt($ttl)
    {
        return $ttl === null
            ? null
            : $this->getTime() + $ttl
        ;
    }

    /**
     * Writes the cache file.
     *
     * @param $id
     * @param $value
     *
     * @return bool
     *
     * @throws \common_exception_Error
     */
    private function writeCacheFile($id, $value)
    {
        $filePath = $this->getPath($id);
        $dirname = dirname($filePath);
        if (!file_exists($dirname)) {
            mkdir($dirname, self::DEFAULT_MASK, true);
        }

        $string = $this->getContent($id, $value);

        // we first open with 'c' in case the flock fails
        // 'w' would empty the file that someone else might be working on
        if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)) {
            // We first need to truncate.
            ftruncate($fp, 0);

            $success = fwrite($fp, $string);
            @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($success) {
                // OPcache workaround
                if ($this->isOpCacheMode()) {
                    $this->cache[$id] = $value;
                    if ($this->isTtlMode() && isset($value[static::CACHE_VALUE_OFFSET])) {
                        $this->cache[$id] = $value[static::CACHE_VALUE_OFFSET];
                    }
                    if (function_exists('opcache_invalidate')) {
                        opcache_invalidate($filePath, true);
                    }
                }
            } else {
                common_Logger::w('Could not write '.$filePath);
            }

            return $success !== false;
        } else {
            common_Logger::w('Could not obtain lock on '.$filePath);

            return false;
        }
    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::get()
     */
    public function get($id)
    {
        if ($this->isOpCacheMode() && isset($this->cache[$id])) {
            // OPcache workaround
            return $this->cache[$id];
        }

        $value = $this->isTtlMode()
            ? $this->getRaw($id)[static::CACHE_VALUE_OFFSET]
            : $this->getRaw($id)
        ;

        if ($this->isOpCacheMode()) {
            $this->cache[$id] = $value;
        }

        return $value;
    }

    /**
     * Returns the raw cache record.
     *
     * @param $id
     *
     * @return mixed
     */
    public function getRaw($id)
    {
        $raw = @include $this->getPath($id);

        if ($this->isTtlMode()) {
            $raw[static::CACHE_VALUE_OFFSET] = $this->processValue($raw);
        }

        return $raw;
    }

    /**
     * Processes the given value.
     *
     * @param $value
     *
     * @return bool
     */
    private function processValue($value)
    {
        $expiresAt = isset($value[static::CACHE_EXPIRES_AT_OFFSET])
            ? $value[static::CACHE_EXPIRES_AT_OFFSET]
            : null
        ;

        if (isset($value[static::CACHE_VALUE_OFFSET]) && ($expiresAt === null || $expiresAt > $this->getTime())) {
            return $value[static::CACHE_VALUE_OFFSET];
        }

        return false;
    }

    /**
     * Returns the current timestamp.
     *
     * @return int
     */
    public function getTime()
    {
        return time();
    }

    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::exists()
     */
    public function exists($id)
    {
        if (!file_exists($this->getPath($id))) {
            return false;
        }

        if ($this->isTtlMode()) {
            $value = $this->getRaw($id);

            if (
                !isset($value[static::CACHE_VALUE_OFFSET]) ||
                $value[static::CACHE_VALUE_OFFSET] === false
            )
            {
                return false;
            }
        }

        return true;
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::del()
     */
    public function del($id)
    {
        $filePath = $this->getPath($id);

        // OPcache workaround
        if ($this->isOpCacheMode()) {
            unset($this->cache[$id]);

            // invalidate opcache first, fails on already deleted file
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($filePath, true);
            }
        }
        $success = @unlink($filePath);
        
        return $success;
    }

    /**
     * Increment existing value
     *
     * @param string $id
     *
     * @return mixed
     *
     * @throws \common_exception_Error
     */
    public function incr($id)
    {
        $value = $this->getRaw($id);

        if ($this->isTtlMode()) {
            $value[static::CACHE_VALUE_OFFSET]++;
        } else {
            $value++;
        }

        return $this->writeCacheFile($id, $value);
    }

    /**
     * Decrement existing value
     *
     * @param $id
     *
     * @return mixed
     *
     * @throws \common_exception_Error
     */
    public function decr($id)
    {
        $value = $this->getRaw($id);

        if ($this->isTtlMode()) {
            $value[static::CACHE_VALUE_OFFSET]--;
        } else {
            $value--;
        }

        return $this->writeCacheFile($id, $value);
    }

    /**
     * purge the persistence directory
     * 
     * @return boolean
     */
    public function purge()
    {
        // @todo opcache invalidation
        return file_exists($this->directory)
            ? helpers_File::emptyDirectory($this->directory)
            : false;
    }

    /**
     * Map the provided key to a relativ path
     * 
     * @param string $key
     * @return string
     */
    protected function getPath($key)
    {
        if ($this->humanReadable) {
            $path = $this->sanitizeReadableFileName($key);
        } else {
            $encoded = hash('md5', $key);
            $path = implode(DIRECTORY_SEPARATOR,str_split(substr($encoded, 0, $this->levels))).DIRECTORY_SEPARATOR.$encoded;
        }
        return  $this->directory.$path.'.php';
    }
    
    /**
     * Cannot use helpers_File::sanitizeInjectively() because
     * of backwards compatibility
     *
     * @param string $key
     * @return Ambigous string
     */
    protected function sanitizeReadableFileName($key)
    {
        $path = '';
        foreach (str_split($key) as $char) {
            $path .= isset(self::$ALLOWED_CHARACTERS[$char]) ? $char : base64_encode($char);
        }
        return $path;
    }
    
    /**
     * Generate the php code that returns the provided value
     * 
     * @param string $key
     * @param mixed $value
     *
     * @return string
     *
     * @throws \common_exception_Error
     */
    protected function getContent($key, $value)
    {
        return $this->humanReadable
            ? "<?php return ".common_Utils::toHumanReadablePhpString($value).";".PHP_EOL
            : "<?php return ".common_Utils::toPHPVariableString($value).";";
    }

    /**
     * Returns TRUE when the connection is in op cache mode.
     *
     * @return bool
     */
    public function isOpCacheMode()
    {
        return $this->opCacheMode;
    }

    /**
     * Sets the op cache mode.
     *
     * @param bool $opCacheMode
     */
    public function setOpCacheMode($opCacheMode)
    {
        $this->opCacheMode = $opCacheMode;
    }

    /**
     * Returns TRUE when the connection is in TTL mode.
     *
     * @return bool
     */
    public function isTtlMode()
    {
        return $this->ttlMode;
    }

    /**
     * Sets the TTL mode.
     *
     * @param bool $ttlMode
     */
    public function setTtlMode($ttlMode)
    {
        $this->ttlMode = $ttlMode;
    }
}
