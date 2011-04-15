<?php

class PluginTest extends Module{

    function index() {
    	echo '<h1>Example Plugins Index :</h1>';
    	# Without the plugin class autoloader
    	spl_autoload_unregister('Plugin::pluginClassAutoLoad'); # Desactivation (Activation comes from common.php)
    	self::loadPlugin();
    	self::loadAllPlugins();

    	# Using the plugin class autoloader
    	spl_autoload_register('Plugin::pluginClassAutoLoad');
    	self::autoLoadActionClass();
    	self::autoLoadModelClass();
    }

    static function autoLoadActionClass(){
    	echo '<h2>autoLoadActionClass</h2>';
		ActionMinimalPlugin::index();
    }

    static function autoLoadModelClass(){
    	echo '<h2>autoLoadModelClass</h2>';
		echo ModelMinimalPlugin::getStatus();
    }

    static function loadPlugin(){
       	echo '<h2>loadPlugin</h2>';
	 	# The plugin is not loaded
		echo '<h3>test 1</h3>';
		if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'fail ';
		else echo 'ok ';

	 	# Load all classes now, without the plugin autoloader
	 	Plugin::load('minimal');
		echo '<h3>test 2</h3>';
		if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'ok';
		else echo 'fail';

		echo '<h3>test 3 - List of plugins</h3>';
		var_dump(Plugin::getPluginList());

		echo '<h3>test 4 - Manifest of the minimal plugin</h3>';
		var_dump(Plugin::getManifest('minimal'));
    }

    static function loadAllPlugins(){
    	echo '<h2>loadAllPlugins</h2>';
    	Plugin::loadAllPlugin();
    	if( class_exists('ActionMinimalPlugin') && class_exists('ModelMinimalPlugin'))
			echo 'ok';
		else echo 'fail';
    }

}
?>