<?php

class helpers_PropertyCache
{
    /**
     * Clear property cached data
     */
    public static function clearCachedValues(Iterator $triples): void
    {
        foreach ($triples as $triple) {
            $property = new \core_kernel_classes_Property($triple->predicate);
            $property->clearCachedValues();
        }
    }

    /**
     * Warmup property cached data
     */
    public static function warmupCachedValues(Iterator $triples): void
    {
        foreach ($triples as $triple) {
            $property = new \core_kernel_classes_Property($triple->predicate);
            $property->warmupCachedValues();
        }
    }
}
