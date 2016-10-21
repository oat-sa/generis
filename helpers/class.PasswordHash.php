<?php
use oat\generis\model\user\PasswordConstraintsException;
use oat\generis\model\user\PasswordConstraintsService;

/**
 * Password Hash class.
 * 
 * An helper class focusing on password validation/generation.
 */
class helpers_PasswordHash
{
    private $algorithm;
    private $saltLength;

    public function __construct($algorithm, $saltLength) {
        $this->algorithm = $algorithm;
        $this->saltLength = $saltLength;
    }

    /**
     * @param $password
     *
     * @return string
     * @throws PasswordConstraintsException
     */
    public function encrypt($password)
    {

        if (PasswordConstraintsService::singleton()->validate($password)) {
            $salt = helpers_Random::generateString($this->getSaltLength());
            return $salt.hash($this->getAlgorithm(), $salt.$password);
        }

        throw new PasswordConstraintsException(
            __( 'Password must be: %s' ,
            implode( ',', PasswordConstraintsService::singleton()->getErrors() )
        ));
    }

    public function verify($password, $hash) 
    {
        $salt = substr($hash, 0, $this->getSaltLength());
        $hashed = substr($hash, $this->getSaltLength());
        return hash($this->getAlgorithm(), $salt.$password) === $hashed;
    }

    protected function getAlgorithm()
    {
        return $this->algorithm;
    }
    
    protected function getSaltLength()
    {
        return $this->saltLength;
    }
}
