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
 * Copyright (c) 2016 (original work) Open Assessment Technologies SA;
 *               
 */

namespace oat\oatbox\validator;

/**
 * base of validator
 * @author Christophe GARCIA <christopheg@taotesting.com>
 */
abstract class AbstractFormValidator {
    
    /**
     * configuration of validation
     * @var array
     */
    protected $validation = [];
    /**
     *
     * @var array 
     */
    protected $errors = []; 
    /**
     *
     * @var boolean
     */
    protected $isValid;


    /**
     * valid a form. 
     * @param array $form
     * @return $this
     */
    public function validate(array $form) {
        $this->isValid = true;
        foreach($form as $field => $value) {
            if(array_key_exists($field , $this->validation )) {
                $this->validField($field , $value, $this->validation[$field]);
            }
        }
        return $this;
    }
    
    /**
     * add an error message
     * @param type $field
     * @param type $message
     * @return \oat\oatbox\validator\AbstractFormValidator
     */
    protected function addError($field , $message) {
        if(array_key_exists($field, $this->errors)) {
            $this->errors[$field][] = $message;
        } else {
            $this->errors[$field] = [$message];
        }
        return $this;
    }
    
    /**
     * execute all validation for a field
     * @param string $field
     * @param mixed $value
     * @param array $config
     */
    protected function validField($field , $value , $config) {
        foreach ($config as $validator) {
            $class  = $validator['class'];
            $option = $validator['options'];
            /* @var $test ValidatorInterface */
            $test = new $class($option);
            if(!$this->executeTest($value , $test)) {
                $this->isValid = false;
                $this->addError($field, $test->getMessage());
            }
        }
    } 
    /**
     * execute a validator
     * @param mixed $value
     * @param \oat\oatbox\validator\ValidatorInterface $validator
     * @return boolena
     */
    protected function executeTest($value , ValidatorInterface  $validator) {
        return $validator->evaluate($value);
    }
    
    /**
     * return all errors
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    /**
     * return error message for a field
     * @param string $field
     * @return array
     */
    public function getError($field) {
        if(array_key_exists($field, $this->errors)) {
            return $this->errors[$field];
        }
        return null;
    }
    /**
     * return form valiation status
     * @return boolean
     * @throws \RuntimeException
     */
    public function isValid() {
        if(is_null($this->isValid)) {
            throw new \RuntimeException('you must validate a form');
        }
        return $this->isValid;
    }
}
