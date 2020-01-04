<?php

declare(strict_types=1);

namespace PetWatcher\Application\Validation;

use Respect\Validation\Exceptions\NestedValidationException;

class Validator
{
    /**
     * @var array $errors List of all validation error messages
     */
    private $errors = [];

    /**
     * Validate input and write occurring error messages into associative array.
     *
     * @param mixed $input Input data
     * @param array $rules Set of validation rules
     * @param bool  $file  Whether $input is a file
     *
     * @return $this
     */
    public function validate($input, array $rules, bool $file = false)
    {
        foreach ($rules as $key => $rule) {
            try {
                if ($file) {
                    if (!isset($input['image']['tmp_name'])) {
                        throw new NestedValidationException($key . ' must not be omitted');
                    }
                    $rule->setName($key)->assert($input['image']['tmp_name']);
                } else {
                    if (!isset($input->getParsedBody()[$key])) {
                        throw new NestedValidationException($key . ' must not be omitted');
                    }
                    $rule->setName($key)->assert($input->getParsedBody()[$key]);
                }
            } catch (NestedValidationException $e) {
                $this->errors[$key] = $e->getMessages();
            }
        }
        return $this;
    }

    /**
     * Check if any validation error occurred.
     *
     * @return bool Whether validation has failed
     */
    public function failed()
    {
        return !empty($this->errors);
    }

    /**
     * Get all validation error messages.
     *
     * @return array Associative array with human-readable error messages
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
