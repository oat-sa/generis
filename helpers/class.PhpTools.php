<?php

/**
 * A utility class handling php language related tasks
 * 
 */
class helpers_PhpTools {
    
    /**
     * Generate a random alphanumeric token with a specific $length.
     * 
     * Code based on http://stackoverflow.com/questions/7153000/get-class-name-from-file
     * by user http://stackoverflow.com/users/492901/netcoder
     * 
     * @param string file to anaylse
     * @return array
     */
    static public function getClassInfo($file) {
        $fp = fopen($file, 'r');
        $class = $namespace = $buffer = '';
        $i = 0;
        while (!$class) {
            if (feof($fp)) break;
        
            $buffer .= fread($fp, 512);
            // supress errors due to partially parsing
            $tokens = @token_get_all($buffer);
        
            if (strpos($buffer, '{') === false) continue;
        
            for (;$i<count($tokens);$i++) {
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