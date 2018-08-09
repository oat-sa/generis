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
     * The TTL mode offset in the connection parameters.
     */
    const OPTION_TTL = 'ttlMode';

    /**
     * The value offset in the record.
     */
    const ENTRY_VALUE = 'value';

    /**
     * The expiration timestamp of the record.
     */
    const ENTRY_EXPIRATION = 'expiresAt';

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
    private $ttlMode;

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
            (isset($params[static::OPTION_TTL]) && $params[static::OPTION_TTL] == true)
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
                static::ENTRY_VALUE      => $value,
                static::ENTRY_EXPIRATION => $this->calculateExpiresAt($ttl),
            ];
        } elseif (null !== $ttl) {
            throw new common_exception_NotImplemented('TTL not implemented in '.__CLASS__);
        }

        if ($nx) {
            throw new common_exception_NotImplemented('NX not implemented in '.__CLASS__);
        }

        return $this->writeFile($id, $value);
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
     * Writes the file.
     *
     * @param $id
     * @param $value
     * @param callable $preWriteValueProcessor   The value preprocessor method.
     *
     * @return bool
     *
     * @throws \common_exception_Error
     */
    private function writeFile($id, $value, $preWriteValueProcessor = null)
    {
        $filePath = $this->getPath($id);
        $dirname = dirname($filePath);
        if (!file_exists($dirname)) {
            mkdir($dirname, self::DEFAULT_MASK, true);
        }

        // we first open with 'c' in case the flock fails
        // 'w' would empty the file that someone else might be working on
        if (false !== ($fp = @fopen($filePath, 'c')) && true === flock($fp, LOCK_EX)) {

            // Runs the pre write callable.
            if (is_callable($preWriteValueProcessor)) {
                $value = call_user_func($preWriteValueProcessor, $id);
            }

            // We first need to truncate.
            ftruncate($fp, 0);
            $string = $this->getContent($id, $value);
            $success = fwrite($fp, $string);
            @flock($fp, LOCK_UN);
            @fclose($fp);
            if ($success) {
                // OPcache workaround
                if (function_exists('opcache_invalidate')) {
                    opcache_invalidate($filePath, true);
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
        $entry = $this->readFile($id);
        if ($entry != false && $this->isTtlMode()) {
            $entry = (is_null($entry[static::ENTRY_EXPIRATION]) || $entry[static::ENTRY_EXPIRATION] > $this->getTime())
                ? $entry[static::ENTRY_VALUE]
                : false
            ;
        }
        return $entry;
    }

    /**
     * Returns the processed entry.
     *
     * @param $id
     *
     * @return mixed
     */
    private function readFile($id)
    {
        return @include $this->getPath($id);
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
        if (!$this->isTtlMode()) {
            return file_exists($this->getPath($id));
        } else {
            return $this->get($id) !== false;
        }
    }
    
    /**
     * (non-PHPdoc)
     * @see common_persistence_KvDriver::del()
     */
    public function del($id)
    {
        $filePath = $this->getPath($id);

        // invalidate opcache first, fails on already deleted file
        if (function_exists('opcache_invalidate')) {
            opcache_invalidate($filePath, true);
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
        return $this->writeFile($id, '', [$this, 'getIncreasedValueEntry']);
    }

    /**
     * Returns the increased value entry.
     *
     * @param $id
     *
     * @return mixed
     */
    private function getIncreasedValueEntry($id)
    {
        $value = intval($this->get($id));
        $value++;
        if ($this->isTtlMode()) {
            $value = [
                static::ENTRY_VALUE      => $value,
                static::ENTRY_EXPIRATION => null,
            ];
        }
        return $value;
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
        return $this->writeFile($id, '', [$this, 'getDecreasedValueEntry']);
    }

    /**
     * Returns the decreased value entry.
     *
     * @param $id
     *
     * @return mixed
     */
    private function getDecreasedValueEntry($id)
    {
        $value = intval($this->get($id));
        $value--;
        if ($this->isTtlMode()) {
            $value = [
                static::ENTRY_VALUE      => $value,
                static::ENTRY_EXPIRATION => null,
            ];
        }
        return $value;
    }

    /**
     * purge the persistence directory
     * 
     * @return boolean
     */
    public function purge()
    {
        if (file_exists($this->directory)) {
            $files          = $this->getCachedFiles();
            $successDeleted = true;
            foreach ($files as $file) {
                $successDeleted &= $this->removeCacheFile($file);
            }

            return (bool)$successDeleted;
        }

        return false;
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

    /**
     * @return array
     */
    private function getCachedFiles()
    {
        try {
            $files = helpers_File::scandir($this->directory,[
                'recursive' => true,
                'only'      => helpers_File::SCAN_FILE,
                'absolute'  => true,
            ]);
        } catch (common_Exception $exception) {
            \common_Logger::e($exception->getMessage());
            return [];
        }

        return $files;
    }

    /**
     * @param string $filePath
     * @return bool
     */
    private function removeCacheFile($filePath)
    {
        try {
            if (function_exists('opcache_invalidate')) {
                opcache_invalidate($filePath, true);
            }

            return helpers_File::remove($filePath);
        } catch (common_exception_Error $exception) {
            \common_Logger::e($exception->getMessage());
            return false;
        }
    }
}
