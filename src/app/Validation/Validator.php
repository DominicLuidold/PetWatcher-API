<?php

namespace PetWatcher\Validation;

use Respect\Validation\Exceptions\NestedValidationException;
use Slim\Http\Request;

class Validator {
    /**
     * @var array $errors List of all validation error messages
     */
    private $errors = [];

    /**
     * Write each error message into associative array
     *
     * @param \Slim\Http\Request $request
     * @param array $rules
     * @return $this
     */
    public function validate(Request $request, array $rules) {
        foreach ($rules as $key => $rule) {
            try {
                $rule->setName($key)->assert($request->getParam($key));
            } catch (NestedValidationException $e) {
                $this->errors[$key] = $e->getMessages();
            }
        }
        return $this;
    }

    /**
     * Check if any validation error occurred
     *
     * @return bool Whether validation has failed
     */
    public function failed() {
        return !empty($this->errors);
    }

    /**
     * Get all validation error messages
     *
     * @return array Associative array with human-readable error messages
     */
    public function getErrors() {
        return $this->errors;
    }
}
