<?php

namespace App\Validation;

class KecsoValidation
{
    // A futáradatok validálása
    public static function validateCouriorData(array $postData): array
    {
        $errors = [];

        // Mezők ellenőrzése
        $requiredFields = ['name', 'date', 'dateaddress', 'age', 'address', 'mothername'];

        foreach ($requiredFields as $field) {
            if (empty($postData[$field])) {
                $errors[$field] = 'Minden mező kitöltése kötelező!';
            }
        }

        // Hónap ellenőrzése (példa szerint, hogy július helyett júl is elfogadott legyen)
        $validMonths = ['január', 'február', 'március', 'április', 'május', 'június', 'július', 'augusztus', 'szeptember', 'október', 'november', 'december'];
        $submittedMonth = strtolower($postData['date']);
        $submittedMonth = str_replace('júl', 'július', $submittedMonth); // Hónap javítása

        if (!in_array($submittedMonth, $validMonths)) {
            $errors['date'] = 'Érvénytelen hónap formátum!';
        }

        return $errors;
    }
}