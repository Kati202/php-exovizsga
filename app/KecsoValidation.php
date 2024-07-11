<?php

namespace App;

class KecsoValidation
{
    // A futáradatok validálása
    public static function validateCouriorData(array $postData): array
    {
        $errors = [];

        $requiredFields = ['name', 'date', 'dateaddress', 'age', 'address', 'mothername'];

        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                $errors[$field] = 'Minden mező kitöltése kötelező!';
            }
        }
       return $errors;
      
    }
}