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
 * Copyright (c) 2015-2020 (original work) Open Assessment Technologies SA
 *
 */

namespace oat\generis\model\kernel\persistence\file;

use oat\generis\model\data\Model;
use \common_exception_MissingParameter;
use \common_exception_Error;
use core_kernel_persistence_smoothsql_SmoothModel as SmoothModel;

/**
 * transitory model for the smooth sql implementation
 *
 * @author joel bout <joel@taotesting.com>
 * @package generis
 */
class FileModel implements Model
{
    /**
     * Path to the rdf file
     *
     * @var string
     */
    private $file;
    
    public static function fromFile($filePath)
    {
        return new self(['file' => $filePath]);
    }
    
    public static function toFile($filePath, $triples)
    {
        $graph = new \EasyRdf_Graph();
        foreach ($triples as $triple) {
            if (!empty($triple->lg)) {
                $graph->addLiteral($triple->subject, $triple->predicate, $triple->object, $triple->lg);
            } elseif (\common_Utils::isUri($triple->object)) {
                $graph->add($triple->subject, $triple->predicate, $triple->object);
            } else {
                $graph->addLiteral($triple->subject, $triple->predicate, $triple->object);
            }
        }
        $format = \EasyRdf_Format::getFormat('rdfxml');
        return file_put_contents($filePath, $graph->serialise($format));
    }
    
    /**
     * Constructor of the smooth model, expects a persistence in the configuration
     *
     * @param array $configuration
     * @throws common_exception_MissingParameter
     */
    public function __construct($options = [])
    {
        if (!isset($options['file'])) {
            throw new common_exception_MissingParameter('file', __CLASS__);
        }
        $this->file = $options['file'];
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getConfig()
     */
    public function getOptions()
    {
        return [
            'file' => $this->file
        ];
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfInterface()
     */
    public function getRdfInterface()
    {
        return new FileRdf($this->file);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getRdfsInterface()
     */
    public function getRdfsInterface()
    {
        throw new \common_exception_NoImplementation('Rdfs interface not implemented for ' . __CLASS__);
    }
    
    /**
     * (non-PHPdoc)
     * @see \oat\generis\model\data\Model::getSearchInterface()
     */
    public function getSearchInterface()
    {
        throw new \common_exception_NoImplementation('Rdfs interface not implemented for ' . __CLASS__);
    }

    // helper
    
    /**
     * @deprecated
     *
     * @param string $file
     * @throws common_exception_Error
     */
    public static function getModelIdFromXml($file)
    {
       return SmoothModel::DEFAULT_READ_ONLY_MODEL;
    }
}
