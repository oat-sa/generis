<?php
/*  
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
 * Copyright (c) 2006-2009 (original work) Public Research Centre Henri Tudor (under the project FP6-IST-PALETTE);
 *               2009-2012 (update and modification) Public Research Centre Henri Tudor (under the project TAO-SUSTAIN & TAO-DEV);
 * 
 */
?>
<?php
/**
 * Load an helper file containing helper functions from the clearFw
 * helpers core path defined by the DIR_CORE_HELPERS constant or from the current public helpers path
 * defined by the DIR_HELPERS constant.
 * 
 * Helpers are loaded using the following procedure.
 * - 1. Looks for a file [$helperName].php in DIR_CORE_HELPERS
 * - 2. If it fails, looks for a file [$helperName].php in DIR_HELPERS
 * - 3. If it fails, throws a HelperLoadingException.
 *
 * @param string $helperName The name of the helper to load.
 */
function load_helper($helperName)
{
	$corePath 		= DIR_CORE_HELPERS . $helperName . '.php';
	$publicPath 	= DIR_HELPERS . $helperName . '.php';

	if (file_exists($corePath)){
		require_once($corePath);
	}	
	else if (file_exists($publicPath)){
		require_once($publicPath);
	}
	else
	{
		throw new HelperLoadingException("The requested helper '${helperName}' could not be loaded.");
	}
}

/**
 * Get data from the request context.
 *
 * @param string $key A key to identify the data.
 * @return mixed The data bound to the key. If no data is bound to the provided key, null is return.
 */
function get_data($key)
{
	return RenderContext::getCurrentContext()->getData($key);
}

/**
 * Returns whenever or not a variable with the specified key is defined
 * 
 * @param string $key
 * @return boolean
 */
function has_data($key){
	return RenderContext::getCurrentContext()->hasData($key);
}
?>