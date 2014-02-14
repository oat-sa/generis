<?php

use oat\generis\model\data\RdfsInterface;

class core_kernel_persistence_smoothsql_SmoothRdfs
    implements RdfsInterface
{
    public function getClassImplementation() {
        return \core_kernel_persistence_smoothsql_Class::singleton();
    }
    
    public function getResourceImplementation() {
        return \core_kernel_persistence_smoothsql_Resource::singleton();
    }
    
    public function getPropertyImplementation() {
        return \core_kernel_persistence_smoothsql_Property::singleton();
    }
    
}