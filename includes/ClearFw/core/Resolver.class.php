<?php
/**
 * This class resolve data containing into a specific URL
 *
 * @uses Class Plugin
 * @author Eric Montecalvo <eric.montecalvo@tudor.lu> <eric.mtc@gmail.com>
 */
class Resolver {

	/**
	 * @var String The Url requested
	 */
	protected $url;

	/**
	 * @var Sring The extension (extension name) requested
	 */
	protected $extension;

	/**
	 * @var Sring The module (classe name) requested
	 */
	protected $module;

	/**
	 * @var String The action (method name) requested
	 */
	protected $action;

	/**
	 * @var Bool Equal true if a plugin is requested
	 */
	protected $isPlugin;

	/**
	 * @var String the plugin name (plugin folder)
	 */
	protected $pluginFolderName;

	/**
	 * The constructor
	 */
    public function __construct($url = null) {
		if($url == null)
 			 $this->url = $_SERVER['REQUEST_URI'];
		else $this->url = $url;

    	$this->module		= null;
    	$this->action		= null;
    	$this->isPlugin		= false;
    	$this->pluginFolderName = '';

		# Now resolve the Url
    	$this->resolveUrl();
    }

    /**
     * @return	String The module name
     */
    public function getExtensionFromURL() {
    	return $this->extension;
    }

	/**
     * @return	String The module name
     */
    public function getModule() {
    	$defaultModuleName = 'Main';
    	
    	if (defined('DEFAULT_MODULE_NAME')){
    		$defaultModuleName = DEFAULT_MODULE_NAME;
    	}
    	
    	return is_null($this->module) ? $defaultModuleName : $this->module;
    }

    /**
     * Return action name
     * @return String The action name
     */
    public function getAction() {
    	$defaultActionName = 'index';
    	
    	if (defined('DEFAULT_ACTION_NAME')){
    		$defaultActionName = DEFAULT_ACTION_NAME;
    	}
    	
    	return is_null($this->action) ? $defaultActionName : $this->action;
    }

    /**
     * @return Bool True if a plugin is requested
     */
    public function isPlugin() {
    	return $this->isPlugin;
    }

    /**
     * @return String The plugin name (the folder name)
     */
    public function getPluginName() {
    	return $this->pluginFolderName;
    }

    /**
     * Parse the URL to initialise the object
     */
    protected function resolveUrl() {
		# Get the index file
    	if(defined('INDEX_FILE'))
			$index = INDEX_FILE;
		else
			$index = 'index.php';

    	# Look for the index file
    	if (strpos($this->url, '/'.$index) !== false){
			# Get the url of the framework requested-object
			$s = explode($index, $this->url);
			$cleanUrl = $s[1];
    	}else{
    		$cleanUrl = $this->url;
    	}

    	$this->resolveRequest($this->url);
    }

	/**
	 * Parse the framework-object requested into the URL
	 *
	 * @param String $request A sub part of the requested URL
	 */
	protected function resolveRequest($request){
		if(empty($request)) return;

		# Clean the request string
		if(strpos($request, '?') !== false){
			$request = substr($request, 0, strpos($request, '?'));
		}
		
		$matches = array();
		preg_match ('/^([^\/]*)\/\/([^\/]*)(\/.*)?$/' , ROOT_URL, $matches);
		$request = isset($matches[3]) ? substr($request, strlen($matches[3])) : $request;
		$request = trim($request, '/');
		
		# Resolve
		$tab = explode('/', $request);
		# Decode the URL
		for($i=0;$i<count($tab);$i++)
			$tab[$i] = urldecode($tab[$i]);

		if (count($tab) > 0) {
			$this->extension	= $tab[0];
			$this->module		= isset($tab[1]) ? $tab[1] : null;
			$this->action		= isset($tab[2]) ? $tab[2] : null;
		} else {
			throw new common_exception_Error('Empty request Uri '.$request.' reached resolver');
		}
		
		//legacy
		if (isset($_GET['extension'])) {
			common_Logger::i('deprecated parameter extension(\''.$_GET['extension'].'\') used coming from '.$_SERVER['HTTP_REFERER']);
			$this->extension = $_GET['extension'] == 'none' ? 'tao' : $_GET['extension'];
		}
	}
}
?>
