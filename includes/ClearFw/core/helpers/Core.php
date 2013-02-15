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
	$context = Context::getInstance();
	return $context->getData($key);
}

/**
 * Set data in the request context. If a data is associated with the key
 * provided with the function call, it will be erased by the new one.
 *
 * @param string $key A key to identify the data.
 * @param mixed $data The data to store, identified by the key.
 * @return void
 */
function set_data($key, $data){
	$context = Context::getInstance();
	$context->setData($key, $data);
}

function has_data($key){
	$context = Context::getInstance();
	$data = $context->getData($key);
	return !empty($data);
}
?>