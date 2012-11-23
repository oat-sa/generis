<?php

error_reporting(E_ALL);

/**
 * The Manifest class enables you to retrieve the distributions that a manifest
 *
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */

if (0 > version_compare(PHP_VERSION, '5')) {
    die('This file was generated for PHP 5');
}

/* user defined includes */
// section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D5C-includes begin
// section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D5C-includes end

/* user defined constants */
// section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D5C-constants begin
// section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D5C-constants end

/**
 * The Manifest class enables you to retrieve the distributions that a manifest
 *
 * @access public
 * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
 * @package common
 * @subpackage distrib
 */
class common_distrib_Manifest
{
    // --- ASSOCIATIONS ---


    // --- ATTRIBUTES ---

    /**
     * The distributions the manifest describes.
     *
     * @access private
     * @var array
     */
    private $distributions = array();

    // --- OPERATIONS ---

    /**
     * Creates a new instance of common_distrib_Manifest. A
     * will be thrown if there is no file at the request location. A
     * will be thrown if it has not the expected format.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string path The path where the distributions.php file is located.
     * @return mixed
     */
    public function __construct($path)
    {
        // section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D65 begin
        if (is_readable($path)){
        	$distributions = require($path);
        	foreach ($distributions as $d){
        		
        		$id = '';
        		$name = '';
        		$description = '';
        		$version = '';
        		$extensions = array();
        		
        		// Id is mandatory.
        		if (!empty($d['id'])){
        			$id = $d['id'];
        			
        			// Name is mandatory.
        			if (!empty($d['name'])){
        				$name = $d['name'];	
        				
        				// Description is mandatory.
        				if (!empty($d['description'])){
        					$description = $d['description'];
        					
        					// Version is mandatory.
        					if (!empty($d['version'])){
        						$version = $d['version'];
        						
        						// Extensions is mandatory.
        						if (!empty($d['extensions'])){
        							$extensions = $d['extensions'];
        							
        							// We now instantiate a distribution.
        							$distrib = new common_distrib_Distribution($id, $name, $description, $version, $extensions);
        							array_push($this->distributions, $distrib);
        						}
        						else{
        							$msg = "Mandatory field 'extensions' not found.";
        							throw new common_distrib_MalformedManifestException($msg);
        						}
        					}
        					else{
        						$msg = "Mandatory field 'version' not found.";
        						throw new common_distrib_MalformedManifestException($msg);
        					}
        				}
        				else{
        					$msg = "Mandatory field 'description' not found.";
        					throw new common_distrib_MalformedManifestException($msg);
        				}
        			}
        			else{
        				$msg = "Mandatory field 'name' not found.";
        				throw new common_distrib_MalformedManifestException($msg);	
        			}
        		}
        		else{
        			$msg = "Mandatory field 'id' not found.";
        			throw new common_distrib_MalformedManifestException($msg);	
        		}
        	}
        }
        else{
        	$msg = "Distributions Manifest not found in '${path}.'";
        	throw new common_distrib_ManifestNotFoundException($msg);
        }
        // section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D65 end
    }

    /**
     * Get the distributions described by the manifest.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @return array
     */
    public function getDistributions()
    {
        $returnValue = array();

        // section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D62 begin
        $returnValue = $this->distributions;
        // section 10-13-1-85--4c870d1c:13b280e5266:-8000:0000000000001D62 end

        return (array) $returnValue;
    }

    /**
     * Get a specific distribution by id. A common_distrib_DistributionNotFound
     * will be thrown if the manifest does not describy any distribution
     * the provided id.
     *
     * @access public
     * @author Jerome Bogaerts, <jerome.bogaerts@tudor.lu>
     * @param  string id
     * @return common_distrib_Distribution
     */
    public function getDistributionById($id)
    {
        $returnValue = null;

        // section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DBB begin
        // section 10-13-1-85--15eb259f:13b2dbd2961:-8000:0000000000001DBB end

        return $returnValue;
    }

} /* end of class common_distrib_Manifest */

?>