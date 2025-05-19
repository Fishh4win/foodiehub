<?php
namespace App\Core;

/**
 * Validator Class
 * 
 * Validates form data with various rules
 */
class Validator {
    /**
     * @var array Data to validate
     */
    private $data = [];
    
    /**
     * @var array Validation rules
     */
    private $rules = [];
    
    /**
     * @var array Error messages
     */
    private $errors = [];
    
    /**
     * @var array Custom error messages
     */
    private $customMessages = [];
    
    /**
     * @var array Default error messages
     */
    private $defaultMessages = [
        'required' => 'The :field field is required',
        'email' => 'The :field must be a valid email address',
        'min' => 'The :field must be at least :param characters',
        'max' => 'The :field must not exceed :param characters',
        'matches' => 'The :field must match the :param field',
        'unique' => 'The :field is already taken',
        'numeric' => 'The :field must be a number',
        'integer' => 'The :field must be an integer',
        'float' => 'The :field must be a decimal number',
        'alpha' => 'The :field must contain only letters',
        'alpha_num' => 'The :field must contain only letters and numbers',
        'alpha_dash' => 'The :field must contain only letters, numbers, dashes, and underscores',
        'url' => 'The :field must be a valid URL',
        'date' => 'The :field must be a valid date',
        'min_value' => 'The :field must be at least :param',
        'max_value' => 'The :field must not exceed :param',
        'between' => 'The :field must be between :param',
        'in' => 'The selected :field is invalid',
        'not_in' => 'The selected :field is invalid',
        'regex' => 'The :field format is invalid',
    ];
    
    /**
     * Constructor
     * 
     * @param array $data Data to validate
     * @param array $rules Validation rules
     * @param array $customMessages Custom error messages
     */
    public function __construct($data = [], $rules = [], $customMessages = []) {
        $this->data = $data;
        $this->rules = $rules;
        $this->customMessages = $customMessages;
    }
    
    /**
     * Set data to validate
     * 
     * @param array $data Data to validate
     * @return $this
     */
    public function setData($data) {
        $this->data = $data;
        return $this;
    }
    
    /**
     * Set validation rules
     * 
     * @param array $rules Validation rules
     * @return $this
     */
    public function setRules($rules) {
        $this->rules = $rules;
        return $this;
    }
    
    /**
     * Set custom error messages
     * 
     * @param array $messages Custom error messages
     * @return $this
     */
    public function setCustomMessages($messages) {
        $this->customMessages = $messages;
        return $this;
    }
    
    /**
     * Validate data against rules
     * 
     * @return bool True if validation passes
     */
    public function validate() {
        $this->errors = [];
        
        foreach ($this->rules as $field => $rules) {
            // Skip validation if field doesn't exist and not required
            if (!isset($this->data[$field]) && !$this->hasRule($rules, 'required')) {
                continue;
            }
            
            // Get field value
            $value = $this->data[$field] ?? null;
            
            // Split rules
            $rulesArray = is_string($rules) ? explode('|', $rules) : $rules;
            
            // Validate each rule
            foreach ($rulesArray as $rule) {
                $this->validateRule($field, $value, $rule);
            }
        }
        
        return empty($this->errors);
    }
    
    /**
     * Validate a single rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param string $rule Rule to validate
     */
    private function validateRule($field, $value, $rule) {
        // Split rule into name and parameters
        $parameters = [];
        
        if (strpos($rule, ':') !== false) {
            list($rule, $paramStr) = explode(':', $rule, 2);
            $parameters = explode(',', $paramStr);
        }
        
        // Call validation method
        $method = 'validate' . ucfirst($rule);
        
        if (method_exists($this, $method)) {
            $result = $this->$method($field, $value, $parameters);
            
            if ($result === false) {
                $this->addError($field, $rule, $parameters);
            }
        }
    }
    
    /**
     * Check if rules contain a specific rule
     * 
     * @param string|array $rules Rules to check
     * @param string $rule Rule to find
     * @return bool True if rule exists
     */
    private function hasRule($rules, $rule) {
        if (is_string($rules)) {
            return strpos($rules, $rule) !== false;
        }
        
        foreach ($rules as $r) {
            if ($r === $rule || strpos($r, $rule . ':') === 0) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Add an error message
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $parameters Rule parameters
     */
    private function addError($field, $rule, $parameters = []) {
        // Get error message
        $message = $this->getErrorMessage($field, $rule, $parameters);
        
        // Add error
        $this->errors[$field][] = $message;
    }
    
    /**
     * Get error message for a rule
     * 
     * @param string $field Field name
     * @param string $rule Rule name
     * @param array $parameters Rule parameters
     * @return string Error message
     */
    private function getErrorMessage($field, $rule, $parameters) {
        // Check for custom message
        $customKey = "{$field}.{$rule}";
        
        if (isset($this->customMessages[$customKey])) {
            $message = $this->customMessages[$customKey];
        } elseif (isset($this->customMessages[$field])) {
            $message = $this->customMessages[$field];
        } elseif (isset($this->defaultMessages[$rule])) {
            $message = $this->defaultMessages[$rule];
        } else {
            $message = "The {$field} field is invalid";
        }
        
        // Replace placeholders
        $message = str_replace(':field', $field, $message);
        
        if (!empty($parameters)) {
            if (count($parameters) === 1) {
                $message = str_replace(':param', $parameters[0], $message);
            } else {
                $message = str_replace(':param', implode(' and ', $parameters), $message);
            }
        }
        
        return $message;
    }
    
    /**
     * Get all errors
     * 
     * @return array Error messages
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * Get first error for each field
     * 
     * @return array First error for each field
     */
    public function getFirstErrors() {
        $firstErrors = [];
        
        foreach ($this->errors as $field => $errors) {
            $firstErrors[$field] = reset($errors);
        }
        
        return $firstErrors;
    }
    
    /**
     * Get all errors as a flat array
     * 
     * @return array All error messages
     */
    public function getAllErrors() {
        $allErrors = [];
        
        foreach ($this->errors as $errors) {
            $allErrors = array_merge($allErrors, $errors);
        }
        
        return $allErrors;
    }
    
    /**
     * Check if field has errors
     * 
     * @param string $field Field name
     * @return bool True if field has errors
     */
    public function hasError($field) {
        return isset($this->errors[$field]);
    }
    
    /**
     * Get errors for a field
     * 
     * @param string $field Field name
     * @return array Field errors
     */
    public function getFieldErrors($field) {
        return $this->errors[$field] ?? [];
    }
    
    /**
     * Validate required rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateRequired($field, $value, $parameters) {
        if (is_null($value)) {
            return false;
        } elseif (is_string($value) && trim($value) === '') {
            return false;
        } elseif (is_array($value) && count($value) < 1) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate email rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateEmail($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate min rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateMin($field, $value, $parameters) {
        $min = (int) $parameters[0];
        return strlen($value) >= $min;
    }
    
    /**
     * Validate max rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateMax($field, $value, $parameters) {
        $max = (int) $parameters[0];
        return strlen($value) <= $max;
    }
    
    /**
     * Validate matches rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateMatches($field, $value, $parameters) {
        $otherField = $parameters[0];
        return isset($this->data[$otherField]) && $value === $this->data[$otherField];
    }
    
    /**
     * Validate unique rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateUnique($field, $value, $parameters) {
        // This requires database access, so it's a placeholder
        // In a real implementation, you would check if the value exists in the database
        return true;
    }
    
    /**
     * Validate numeric rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateNumeric($field, $value, $parameters) {
        return is_numeric($value);
    }
    
    /**
     * Validate integer rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateInteger($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }
    
    /**
     * Validate float rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateFloat($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_FLOAT) !== false;
    }
    
    /**
     * Validate alpha rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateAlpha($field, $value, $parameters) {
        return preg_match('/^[a-zA-Z]+$/', $value);
    }
    
    /**
     * Validate alpha_num rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateAlphaNum($field, $value, $parameters) {
        return preg_match('/^[a-zA-Z0-9]+$/', $value);
    }
    
    /**
     * Validate alpha_dash rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateAlphaDash($field, $value, $parameters) {
        return preg_match('/^[a-zA-Z0-9_-]+$/', $value);
    }
    
    /**
     * Validate url rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateUrl($field, $value, $parameters) {
        return filter_var($value, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Validate date rule
     * 
     * @param string $field Field name
     * @param mixed $value Field value
     * @param array $parameters Rule parameters
     * @return bool True if validation passes
     */
    private function validateDate($field, $value, $parameters) {
        $format = $parameters[0] ?? 'Y-m-d';
        $date = \DateTime::createFromFormat($format, $value);
        return $date && $date->format($format) === $value;
    }
}
