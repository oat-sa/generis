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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA;
 *               
 * 
 */

namespace oat\oatbox;

/**
 * the generis autoloader
 *
 * @access public
 * @author Joel Bout <joel@taotesting.com>
 * @package common
 * @subpackage oatbox
 */
class AutoLoader
{
    /**
     * Key to use for the caching of the autoloader config
     * 
     * @var string
     */
    const CACHE_KEY = 'oat_autoloader';

    /**
     * Register this instance of ClassLoader as a php autoloader
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     */
    public static function register()
    {
        // init the autloader for generis
        self::registerGenerisAutoloader();
        
        // init the composer autoloader for libraries
        self::registerComposerAutoloader();
        
        // init the autoloader of the extensions, requires composer
        self::registerExtensionAutoloader();
    }
    
    /**
     * register the generis autoloader
     */
    protected static function registerGenerisAutoloader() {
        $classLoader = new static();
        spl_autoload_register(array($classLoader, 'autoload'));
        set_include_path(get_include_path() . PATH_SEPARATOR . GENERIS_BASE_PATH);
    }
    
    /**
     * register the composer autoloader
     * for 3rd party libraries
     */
    protected static function registerComposerAutoloader() {
        require_once VENDOR_PATH . 'autoload.php';
    }
    
    /**
     * register the extensions autoloader
     */
    protected static function registerExtensionAutoloader()
    {
        $classLoader = new \Composer\Autoload\ClassLoader();
        $map = self::getAutloadConfig();
        foreach ($map as $autoloader => $config) {
            switch ($autoloader) {
            	case "psr-0" :
            	    foreach ($config as $ns => $dir) {
            	        $classLoader->add($ns, $dir);
            	    }
            	    break;
            	case "psr-4" :
            	    foreach ($config as $ns => $dir) {
            	        $classLoader->addPsr4($ns, $dir);
            	    }
            	    break;
            	default :
            	    common_Logger::w('unknown autoloader '.$autoloader);
            }
        }
        $classLoader->register();
    }
    
    /**
     * Get the autload config from cache if available
     * 
     * @return array autoload config
     */
    private static function getAutloadConfig()
    {
        try {
            $returnValue = \common_cache_FileCache::singleton()->get(self::CACHE_KEY);
        } catch (\common_cache_NotFoundException $e) {
            $returnValue = array();
            foreach (\common_ext_ExtensionsManager::singleton()->getInstalledExtensions() as $ext) {
                $map = $ext->getManifest()->getAutoloaders();
                if (!empty($map)) {
                    foreach ($map as $key => $config) {
                        if (!isset($returnValue[$key])) {
                            $returnValue[$key] = array();
                        }
                        $returnValue[$key] = array_merge($returnValue[$key], $config);
                    }
                }
            }
            \common_cache_FileCache::singleton()->put($returnValue, self::CACHE_KEY);
        }
        return $returnValue;
    }

    /**
     * protect the cunstructer, objects should only be initialised via registerGenerisAutoloader()
     */
    protected function __construct() {
    }
    
    /**
     * Attempt to autload classes in tao
     *
     * @access public
     * @author Joel Bout <joel@taotesting.com>
     * @param  string pClassName
     * @return void
     */
    public function autoload($pClassName)
    {
        if(strpos($pClassName, '_') !== false){
    		$tokens = explode("_", $pClassName);
    		$size = count($tokens);
    		$path = '';
    		for ( $i = 0 ; $i<$size-1 ; $i++){
    			$path .= $tokens[$i].'/';
    		}
    		
    		$filePath = '/' . $path . 'class.'.$tokens[$size-1] . '.php';
    		if (file_exists(GENERIS_BASE_PATH .$filePath)){
    			require_once GENERIS_BASE_PATH .$filePath;
    			return;
    		}
    		$filePathInterface = '/' . $path . 'interface.'.$tokens[$size-1] . '.php';
    		if (file_exists(GENERIS_BASE_PATH .$filePathInterface)){
    			require_once GENERIS_BASE_PATH .$filePathInterface;
    			return;
    		}
    		
    		if (file_exists(ROOT_PATH .$filePath)){
    			require_once ROOT_PATH .$filePath;
    			return;
    		} elseif (file_exists(ROOT_PATH .$filePathInterface)){
    		        require_once ROOT_PATH .$filePathInterface;
    		        return;
    		}
    	}
    	else{
    		$packages = array(DIR_CORE,DIR_CORE_HELPERS,DIR_CORE_UTILS);
    		foreach($packages as $path) {
    			if (file_exists($path. $pClassName . '.class.php')) {
    				require_once $path . $pClassName . '.class.php';
    				return;
    			}
    		}
    	}
    }
    
    /**
     * Reloads the extension autoloader
     * 
     * This will result in having several autoloaders, but this should not be a problem
     * 
     */
    public static function reload() {
        \common_cache_FileCache::singleton()->remove(self::CACHE_KEY);
        self::registerExtensionAutoloader();
    }

}