<?php

/**
 * A utility class handling php language related tasks
 * 
 */
class helpers_PhpTools {
    
    /**
     * Generate a random alphanumeric token with a specific $length.
     * 
     * Code losely based on http://stackoverflow.com/questions/7153000/get-class-name-from-file
     * by user http://stackoverflow.com/users/492901/netcoder
     * 
     * @param string file to anaylse
     * @return array
     */
    static public function getClassInfo($file) {
	    $buffer = file_get_contents($file);
	    $tokens = @token_get_all($buffer);
	    $class = $namespace = $buffer = '';
        for ($i=0;$i<count($tokens);$i++) {
            if ($tokens[$i][0] === T_NAMESPACE) {
                for ($j=$i+1;$j<count($tokens); $j++) {
                    if ($tokens[$j][0] === T_STRING) {
                        $namespace .= '\\'.$tokens[$j][1];
                    } else if ($tokens[$j] === '{' || $tokens[$j] === ';') {
                        break;
                    }
                }
            }

            if ($tokens[$i][0] === T_CLASS) {
                for ($j=$i+1;$j<count($tokens);$j++) {
                    if ($tokens[$j] === '{') {
                        if (!isset($tokens[$i+2][1])) {
                            error_log($file.' does not contain a valid class definition');
                            break;
                        } else {
                            $class = $tokens[$i+2][1];
                        }
                    }
                }
            }
        }
        return array(
        	'ns' => $namespace,
            'class' => $class
        );
    }
}