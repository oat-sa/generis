<?php

/**
 * A utility class focusing on Randomization.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class helpers_PasswordHash {
    
    CONST LEGACY_ALGORITHM = 'md5';
    CONST LEGACY_SALT_LENGTH = 0;
    
    private $algorithm;
    private $saltLength;
    
    /**
     * @return helpers_PasswordHash
     */
    public static function getGenerisHash() {
        return new self(
            defined('PASSWORD_HASH_ALGORITHM') ? PASSWORD_HASH_ALGORITHM : self::LEGACY_ALGORITHM,
            defined('PASSWORD_HASH_SALT_LENGTH') ? PASSWORD_HASH_SALT_LENGTH : self::LEGACY_SALT_LENGTH
        );
    }
    
    public function __construct($algorithm, $saltLength) {
        $this->algorithm = $algorithm;
        $this->saltLength = $saltLength;
    }

    public function encrypt($password) {
        $salt = helpers_Random::generateString($this->saltLength);
        return $salt.hash($this->algorithm, $salt.$password);
    }
    
    public function verify($password, $hash) {
        $salt = substr($hash, 0, $this->saltLength);
        $hashed = substr($hash, $this->saltLength);
        return hash($this->algorithm, $salt.$password) == $hashed;
    }
}