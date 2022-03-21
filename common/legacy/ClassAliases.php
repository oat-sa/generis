<?php

/**
 * This file used for migrating extensions to the new version of EasyRdf library.
 * After updating all extension this part of code can be removed.
 */

use EasyRdf\Format;
use EasyRdf\Graph;

class_alias(Graph::class, 'EasyRdf_Graph');
class_alias(Format::class, 'EasyRdf_Format');
