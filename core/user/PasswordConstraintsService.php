<?php

/**
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; under version 2
 * of the License (non-upgradable).
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 *
 * Copyright (c) 2015 (original work) Open Assessment Technologies SA (under the project TAO-PRODUCT);
 * @author Mikhail Kamarouski, <kamarouski@1pt.com>
 */
namespace oat\generis\model\user;

use oat\oatbox\service\ConfigurableService;
use oat\oatbox\service\ServiceManager;

/**
 * Class PasswordConstraintsService used to verify password strength
 * @package generis
 */
class PasswordConstraintsService extends ConfigurableService
{
    const SERVICE_ID = 'generis/passwords';

    const OPTION_CONSTRAINTS = 'constrains';

    /**
     * @var array
     */
    protected $validators = null;

    public static function singleton()
    {
        return ServiceManager::getServiceManager()->get(self::SERVICE_ID);
    }


    /**
     * Test if password pass all constraints rules
     *
     * @param $password
     *
     * @return bool
     */
    public function validate( $password )
    {
        $result = true;
        /** @var \tao_helpers_form_Validator $validator */
        foreach ($this->getValidators() as $validator) {
            $result &= $validator->evaluate( $password );
        }

        return (boolean) $result;
    }

    /**
     * Set up all validator according configuration file
     *
     * @param $config
     */
    protected function register($config)
    {
        $this->validators = array();

        if (array_key_exists( 'length', $config ) && (int) $config['length']) {
            $this->validators[] = new \tao_helpers_form_validators_Length( array( 'min' => (int) $config['length'] ) );
        }

        if (( array_key_exists( 'upper', $config ) && $config['upper'] )
            || ( array_key_exists( 'lower', $config ) && $config['lower'] )
        ) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one letter' ),
                    'format'  => '/\pL/'
                ), 'letters'
            );
        }

        if (( array_key_exists( 'upper', $config ) && $config['upper'] )) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include upper case letters' ),
                    'format'  => '/(\p{Lu}+)/',
                ), 'caseUpper'
            );
        }

        if (( array_key_exists( 'lower', $config ) && $config['lower'] )) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include lower case letters' ),
                    'format'  => '/(\p{Ll}+)/'
                ), 'caseLower'
            );
        }

        if (array_key_exists( 'number', $config ) && $config['number']) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one number' ),
                    'format'  => '/\pN/'
                ), 'number'
            );
        }

        if (array_key_exists( 'spec', $config ) && $config['spec']) {
            $this->validators[] = new \tao_helpers_form_validators_Regex(
                array(
                    'message' => __( 'Must include at least one special letter' ),
                    'format'  => '/[^p{Ll}\p{Lu}\pL\pN]/'
                ), 'spec'
            );
        }

    }

    /**
     * Any errors that was found during validation process
     * @return array
     */
    public function getErrors()
    {
        $errors = array();
        /** @var \tao_helpers_form_Validator $validator */
        foreach ($this->validators as $validator) {
            $errors[] = $validator->getMessage();
        }

        return $errors;
    }

    /**
     * List of active validators
     * @return array
     */
    public function getValidators()
    {
        if (is_null($this->validators)) {
            $this->register($this->getOption(self::OPTION_CONSTRAINTS));
        }
        return $this->validators;
    }
}
