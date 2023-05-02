<?php

/**
 * A utility class focusing on Randomization.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 */
class helpers_Random
{
    /**
     * Generate a random alphanumeric token with a specific $length.
     *
     * @param integer $length the length of the token to be generated
     *
     * @return string a randomly generated alphanumeric token
     */
    public static function generateString($length)
    {
        $token = '';
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $maxIndex = strlen($chars) - 1;

        for ($i = 0; $i < $length; $i++) {
            $token .= $chars[mt_rand(0, $maxIndex)];
        }

        return $token;
    }
}
