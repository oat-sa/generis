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
 * Copyright (c) 2014 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * 
 */

/**
 * Short description of class common_cache_SingletonCache
 *
 * @abstract
 * @access public
 * @author Joel Bout, <joel@taotesting.com>
 * @package generis
 
 */
abstract class common_cache_SingletonCache
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * Short description of attribute instances
     *
     * @access private
     * @var array
     */
    private static $instances = array();

    // --- OPERATIONS ---

    /**
     * Short description of method singleton
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @return common_cache_Cache
     */
    public static function singleton()
    {
        $returnValue = null;

        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002008 begin
        $cacheName = get_called_class();
        if (!isset(self::$instances[$cacheName])) {
        	self::$instances[$cacheName] = new $cacheName();
        }
        
        $returnValue = self::$instances[$cacheName];
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002008 end

        return $returnValue;
    }

    /**
     * Short description of method getCached
     *
     * @access public
     * @author Joel Bout, <joel@taotesting.com>
     * @param  function
     */
    public static function getCached($function)
    {
        $returnValue = null;

        // section 10-30-1--78--412d45c0:13d453d515d:-8000:000000000000200A begin
		$args = func_get_args();
		array_shift($args);
        if (!is_string($function)){
            $r = new ReflectionFunction($function);
            $serial = md5(
              $r->getFileName().
              $r->getStartLine().
              serialize($args)
            );
        } else {
            $serial = md5($function.serialize($args));
        }
        if (static::singleton()->has($serial)) {
        	$returnValue = static::singleton()->has($serial);
        } else { 
	        $returnValue = call_user_func_array($fn, $args);
	        static::singleton()->put($serial, $returnValue);
        }
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:000000000000200A end

        return $returnValue;
    }

    /**
     * Short description of method __construct
     *
     * @access private
     * @author Joel Bout, <joel@taotesting.com>
     * @return mixed
     */
    private function __construct()
    {
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002011 begin
        // section 10-30-1--78--412d45c0:13d453d515d:-8000:0000000000002011 end
    }

} /* end of abstract class common_cache_SingletonCache */

?>