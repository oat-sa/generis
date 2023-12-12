<?php

namespace oat\generis\Helper;

use Iterator;

class PropertyCache
{
    /**
     * Clear property cached data
     */
    public static function clearCachedValuesByTriples(Iterator $triples): void
    {
        foreach ($triples as $triple) {
            $classProperty = new \core_kernel_classes_Property($triple->predicate);
            $classProperty->clearCachedValues();
        }
    }

    /**
     * Warmup property cached data
     */
    public static function warmupCachedValuesByProperties(array $properties): void
    {
        foreach ($properties as $property) {
            $classProperty = new \core_kernel_classes_Property($property);
            $classProperty->warmupCachedValues();
        }
    }
}
