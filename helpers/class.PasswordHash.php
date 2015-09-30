<?php
use oat\generis\model\user\PasswordConstraintsException;
use oat\generis\model\user\PasswordConstraintsService;

/**
 * A utility class focusing on Randomization.
 *
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class helpers_PasswordHash {

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
    public function encrypt($password) {

        $errors = PasswordConstraintsService::singleton()->getErrors($password);

        if (0 === count($errors)) {
            $salt = helpers_Random::generateString($this->saltLength);
            return $salt.hash($this->algorithm, $salt.$password);
        }

        $exception = new PasswordConstraintsException('Password must be: %s' . implode(',', $errors));
        $exception->setErrors($errors);

        throw $exception;
    }

    public function verify($password, $hash) {
        $salt = substr($hash, 0, $this->saltLength);
        $hashed = substr($hash, $this->saltLength);
        return hash($this->algorithm, $salt.$password) === $hashed;
    }

}