<?php
namespace App\Utils;

class Validator {
    public static function validate(array $data, array $rules): array {
        $errors = [];
        
        foreach ($rules as $field => $ruleString) {
            $ruleSet = explode('|', $ruleString);
            $value = $data[$field] ?? null;
            
            foreach ($ruleSet as $rule) {
                if ($rule === 'required') {
                    if ($value === null || $value === '') {
                        $errors[$field][] = 'The field is required.';
                    }
                } elseif ($value !== null && $value !== '') {
                    if ($rule === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                        $errors[$field][] = 'The field must be a valid email.';
                    } elseif ($rule === 'integer' && !filter_var($value, FILTER_VALIDATE_INT)) {
                        $errors[$field][] = 'The field must be an integer.';
                    } elseif ($rule === 'string' && !is_string($value)) {
                        $errors[$field][] = 'The field must be a string.';
                    } elseif (str_starts_with($rule, 'min:')) {
                        $min = (int) substr($rule, 4);
                        if (is_numeric($value) && $value < $min) {
                            $errors[$field][] = "The field must be at least $min.";
                        } elseif (is_string($value) && strlen($value) < $min) {
                            $errors[$field][] = "The field must be at least $min characters.";
                        }
                    } elseif (str_starts_with($rule, 'max:')) {
                        $max = (int) substr($rule, 4);
                        if (is_numeric($value) && $value > $max) {
                            $errors[$field][] = "The field must not be greater than $max.";
                        } elseif (is_string($value) && strlen($value) > $max) {
                            $errors[$field][] = "The field must not be greater than $max characters.";
                        }
                    } elseif ($rule === 'date' && strtotime((string)$value) === false) {
                        $errors[$field][] = 'The field must be a valid date.';
                    } elseif (str_starts_with($rule, 'in:')) {
                        $allowed = explode(',', substr($rule, 3));
                        if (!in_array((string)$value, $allowed, true)) {
                            $errors[$field][] = 'The field is not in the allowed list.';
                        }
                    }
                }
            }
        }
        
        return $errors;
    }
}
