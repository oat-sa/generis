<?php
function load_helper($helperName)
{
	$corePath 		= DIR_CORE_HELPERS . $helperName . '.php';
	$publicPath 	= DIR_HELPERS . $helperName . '.php';

	if (file_exists($corePath))
		require_once($corePath);
		
	else if (file_exists($publicPath))
		require_once($publicPath);
	
	else
	{
		throw new HelperLoadingException("The requested helper '${helperName}' could not be loaded.");
	}
}

function get_data($key)
{
	$context = Context::getInstance();
	return $context->getData($key);
}
?>