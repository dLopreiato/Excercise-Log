<?php
// how we want the user to see dates
define('EXPECTED_DATE_FORMAT', 'F j, Y g:ia');

/* ALL FIELDS
 * REQUIREMENTS: name:string, require:boolean and type:APITypes
 * OPTIONAL: if require is false, defaultValue MUST be set, and have a matching type
 
 
 * ------------- API_TYPE_STRING -------------
 * Analagous to php string
 * Allows everything but <, >, and \
 * REQUIREMENTS: boolean:zeroLengthToNull
 * OPTIONS: minLength:int, maxLength:int, (regex:string AND regexDescription:string) */
define('API_TYPE_STRING', 0);

/* ------------- API_TYPE_DATE -------------
 * Analagous to php DateTime
 * OPTIONS: allowPastDates:boolean, allowFutureDates:boolean */
define('API_TYPE_DATE', 1);

/* ------------- API_TYPE_BOOL -------------
 * Analagous to php boolean */
define('API_TYPE_BOOL', 2);

/* ------------- API_TYPE_PRECISE_NUMERIC -------------
 * Analagous to php int or a MySQL Decimal
 * REQUIREMENTS: decimalDigits:int
 * OPTIONS: minValue:(numeric string), maxValue:(numeric string) */
define('API_TYPE_PRECISE_NUMERIC', 3);

/* ------------- API_TYPE_APPROXIMATE_NUMERIC -------------
 * Analagous to php float
 * OPTIONS: minValue:float, maxValue:int */
define('API_TYPE_APPROXIMATE_NUMERIC', 4);

/* ------------- API_TYPE_ARRAY -------------
 * Analagous to php array
 * REQUIREMENTS: elementConstraints:array
 * OPTIONS: minElements:int, maxElements:int */
define('API_TYPE_ARRAY', 5);

/* Returns any validation errors that the user should see in standard api error format.
 * $fieldOptions is an array of all the field options, and userInput is a string.

 * NOTES: 'required' is never checked here as userInput is required to be not null, and '' is handled as an empty string. */
function ValidateAPIType($fieldOptions, $userInput) {
    $errorArray = array();

    if ($fieldOptions['type'] == API_TYPE_STRING) {
        $userInput = trim((string)$userInput);

        // check minimum length is less than the given length
        if (isset($fieldOptions['minLength']) && $fieldOptions['minLength'] > strlen(utf8_decode($userInput))) {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' shorter than ' . $fieldOptions['minLength'],
                'userErrorText' => 'This field must be longer than ' . $fieldOptions['minLength'] . ' characters long.',
                'field' => $fieldOptions['name']);
        }
        // cehck maximum length is greater than the given length
        if (isset($fieldOptions['maxLength']) && $fieldOptions['maxLength'] < strlen(utf8_decode($userInput))) {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' longer than ' . $fieldOptions['maxLength'],
                'userErrorText' => 'This field must be shorter than ' . $fieldOptions['maxLength'] . ' characters long.',
                'field' => $fieldOptions['name']);
        }
        // check the given regex
        if (isset($fieldOptions['regex']) && !(preg_match($fieldOptions['regex'], $userInput) == 1)) {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' contains invalid characters',
                'userErrorText' => 'This field ' . $fieldOptions['regexDescription'],
                'field' => $fieldOptions['name']);
        }

        // check for valid characters always, YES EVEN IF ANOTHER REGEX HAS BEEN DEFINED WE WILL NEVER ALLOW ></
        if (!(preg_match("/^[^\<\>\\\\]*$/", $userInput) == 1)) {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' contains invalid characters',
                'userErrorText' => 'This field must not contain the less than, greater than, or backslash characters.',
                'field' => $fieldOptions['name']);
        }
    }

    elseif ($fieldOptions['type'] == API_TYPE_DATE) {
        // check if it's parsable
        $userInput = DateTime::createFromFormat(EXPECTED_DATE_FORMAT, $userInput);
        if ($userInput != false) {
            // check if it's allowed to be in the past
            if (isset($fieldOptions['allowPastDates']) && $fieldOptions['allowPastDates'] == false && (new DateTime("now")) > $userInput) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' is not allowed to be in the past',
                    'userErrorText' => 'This field must be set to a future date.',
                    'field' => $fieldOptions['name']);
            }

            // cehck if it's allowed to be in the future
            if (isset($fieldOptions['allowFutureDates']) && $fieldOptions['allowFutureDates'] == false && (new DateTime("now")) < $userInput) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' is not allowed to be in the future',
                    'userErrorText' => 'This field must be set to a past date.',
                    'field' => $fieldOptions['name']);
            }
        }
        else {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' is not a valid date',
                'userErrorText' => 'This field must be a valid date.',
                'field' => $fieldOptions['name']);
        }
    }

    elseif ($fieldOptions['type'] == API_TYPE_BOOL) {
        $userInput = strtolower(trim((string)$userInput));
        if ($userInput != 'true' && $userInput != 'false') {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' must be either \'true\' or \'false\'',
                'userErrorText' => 'This field must be either \'true\' or \'false\' (case in-sensitive).',
                'field' => $fieldOptions['name']);
        }
    }

    elseif ($fieldOptions['type'] == API_TYPE_PRECISE_NUMERIC) {
        $regexString = ($fieldOptions['decimalDigits'] > 0)
            ? ("/^\-?[0-9]+\.[0-9]{" . $fieldOptions['decimalDigits'] . "}$/")
            : ("/^\-?[0-9]+$/");
        if (!(preg_match($regexString, $userInput) == 1)) {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' must be in decimal format with ' . $fieldOptions['decimalDigits'] . ' decimal digits',
                'userErrorText' => 'This field must have ' . $fieldOptions['decimalDigits'] . ' digits after it\'s decimal point.',
                'field' => $fieldOptions['name']);
        }
        else {
            if (isset($fieldOptions['minValue'])) {
                $inputSeparated = explode('.', $userInput);
                $minValueSeparated = explode('.', $fieldOptions['minValue']);
                if (isset($inputSeparated[1]) && !(strstr($userInput, '-') === false)) {
                    $inputSeparated[1] = '-' . $inputSeparated[1];
                }
                if (isset($minValueSeparated[1]) && !(strstr($fieldOptions['minValue'], '-') === false)) {
                    $minValueSeparated[1] = '-' . $minValueSeparated[1];
                }
                if (((int) $inputSeparated[0] < (int) $minValueSeparated[0]) || (isset($inputSeparated[1]) &&
                    ((int) $inputSeparated[0] == (int) $minValueSeparated[0]) &&
                    ((int) $inputSeparated[1] < (int) $minValueSeparated[1]))) {
                    $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' must be greater than ' . $fieldOptions['minValue'],
                        'userErrorText' => 'This field must be greater than ' . $fieldOptions['minValue'] . '.',
                        'field' => $fieldOptions['name']);
                }
            }

            if (isset($fieldOptions['maxValue'])) {
                $inputSeparated = explode('.', $userInput);
                $maxValueSeparated = explode('.', $fieldOptions['maxValue']);
                if (isset($inputSeparated[1]) && !(strstr($userInput, '-') === false)) {
                    $inputSeparated[1] = '-' . $inputSeparated[1];
                }
                if (isset($minValueSeparated[1]) && !(strstr($fieldOptions['maxValue'], '-') === false)) {
                    $minValueSeparated[1] = '-' . $minValueSeparated[1];
                }
                if (((int) $inputSeparated[0] > (int) $maxValueSeparated[0]) || (isset($inputSeparated[1]) &&
                    ((int) $inputSeparated[0] == (int) $maxValueSeparated[0]) &&
                    ((int) $inputSeparated[1] > (int) $maxValueSeparated[1]))) {
                    $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' must be less than ' . $fieldOptions['maxValue'],
                        'userErrorText' => 'This field must be less than ' . $fieldOptions['maxValue'] . '.',
                        'field' => $fieldOptions['name']);
                }
            }
        }
    }

    elseif ($fieldOptions['type'] == API_TYPE_APPROXIMATE_NUMERIC) {
        if (is_numeric($userInput)) {
            $userInput = (float) $userInput;
            // check minimum value is less than the given value
            if (isset($fieldOptions['minValue']) && $fieldOptions['minValue'] > $userInput) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' less than ' . $fieldOptions['minValue'],
                    'userErrorText' => 'This field must be greater than ' . $fieldOptions['minValue'] . '.',
                    'field' => $fieldOptions['name']);
            }
            // cehck maximum value is greater than the given value
            if (isset($fieldOptions['maxValue']) && $fieldOptions['maxValue'] < $userInput) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' greater than ' . $fieldOptions['maxValue'],
                    'userErrorText' => 'This field must be less than ' . $fieldOptions['maxValue'] . '.',
                    'field' => $fieldOptions['name']);
            }
        }
        else {
            $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' is not a numeric value',
                'userErrorText' => 'This field must be a number.',
                'field' => $fieldOptions['name']);
        }
    }
    //elementConstraints
    elseif ($fieldOptions['type'] == API_TYPE_ARRAY) {
        if ($userInput == '') {
            if (isset($fieldOptions['minElements']) && $fieldOptions['minElements'] > 0) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' has less than ' . $fieldOptions['minElements'] . ' elements',
                    'userErrorText' => 'This field must have more than ' . $fieldOptions['minElements'] . ' elements.',
                    'field' => $fieldOptions['name']);
            }
        }
        else {
            $userInput = explode(',', $userInput);
            if (isset($fieldOptions['minElements']) && count($userInput) < $fieldOptions['minElements']) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' has less than ' . $fieldOptions['minElements'] . ' elements',
                    'userErrorText' => 'This field must have more than ' . $fieldOptions['minElements'] . ' elements.',
                    'field' => $fieldOptions['name']);
            }
            if (isset($fieldOptions['maxElements']) && count($userInput) > $fieldOptions['maxElements']) {
                $errorArray[] = array('errorDescription' => 'variable \'' . $fieldOptions['name'] . '\' has more than ' . $fieldOptions['maxElements'] . ' elements',
                    'userErrorText' => 'This field must have less than ' . $fieldOptions['maxElements'] . ' elements.',
                    'field' => $fieldOptions['name']);
            }
            foreach ($userInput as $arrayElement) {
                $errorArray = array_merge($errorArray, ValidateAPIType($fieldOptions['elementConstraints'], $arrayElement));
            }
        }
    }

    return $errorArray;
}


/* Returns all validation errors that the user should see, in standard api error format.
 * $formOptions is an array of all the arrays of field constraints, and formInput is the array of user input (ususally
 * $_GET or $_POST). */
function ValidateFormInput($formOptions, $formInput) {
    $outputErrorArray = array();

    foreach ($formOptions as $fieldConstraints) {
        if (isset($formInput[$fieldConstraints['name']])) {
            $outputErrorArray = array_merge($outputErrorArray, ValidateAPIType($fieldConstraints, $formInput[$fieldConstraints['name']]));
        }
        else {
            if ($fieldConstraints['require'] == true) {
                $outputErrorArray[] = array('errorDescription' => 'variable \'' . $fieldConstraints['name'] . '\' is not set',
                    'userErrorText' => 'Something went wrong with your request. Let us know, and we\'ll try to fix it!',
                    'field' => $fieldConstraints['name']);
            }
        }
    }

    return $outputErrorArray;
}



/* Prepares a single type for the database. This one shouldn't really be used that much, and is mostly a helper to
 * PrepareFormInputForDatabase. Since it's pretty small, I'm not going to document it. It's pretty self explanatory. */
function PrepareAPITypeForDatabase($fieldOptions, $userValue, $mysqliConnection) {
    $outputValue;
    if ($fieldOptions['type'] == API_TYPE_STRING) {
        if ($userValue == '' && $fieldOptions['zeroLengthToNull'] == true) {
            $outputValue = null;
        }
        else {
            $outputValue = '\'' . trim($mysqliConnection->real_escape_string($userValue)) . '\'';
        }
    }
    elseif ($fieldOptions['type'] == API_TYPE_DATE) {
        $userValue = new DateTime($userValue);
        $outputValue = '\'' . $userValue->format(MYSQL_DATE_FORMAT) . '\'';
    }
    elseif ($fieldOptions['type'] == API_TYPE_BOOL) {
        $outputValue = ($userValue == 'true') ? ('1') : ('0');
    }
    elseif ($fieldOptions['type'] == API_TYPE_PRECISE_NUMERIC) {
        $outputValue = $userValue;
    }
    elseif ($fieldOptions['type'] == API_TYPE_APPROXIMATE_NUMERIC) {
        $outputValue = $userValue;
    }
    elseif ($fieldOptions['type'] == API_TYPE_ARRAY) {
        $userValue = explode(',', $userValue);
        $outputValue = array();
        foreach ($userValue as $elementOfValue) {
            $outputValue[] = PrepareAPITypeForDatabase($fieldOptions['elementConstraints'], $elementOfValue, $mysqliConnection);
        }
    }
    return $outputValue;
}


/* Returns an associative array which in which the key is the name constrain, and the value is ready for database input.
 * An unset variable will not have a key in the returning array. Arrays will be returned as arrays, and are not actually
 * suitable for Database querying (though each element of the array will be). You should use the value for an array as
 * the foreach loop thing. The MySQL connection is so that it can appropriately escape the string. THE ASSUMPTION IS
 * THAT THE DATA HAS ALREADY BEEN VALIDATED. YOU WILL GET GARBAGE DATA IF YOU DON'T DO THAT FIRST.
 *
 * Example: bool:id with user input "true" will have the return array('id' => 1). Strings will be surrounded in quotes,
 * numbers won't.
 *
 * $formOptions is an array of all the arrays of field constraints, and formInput is the array of user input (ususally
 * $_GET or $_POST). */
function PrepareFormInputForDatabase($formOptions, $formInput, $mysqliConnection) {
    $outputArray = array();

    foreach ($formOptions as $fieldOptions) {
        if (isset($formInput[$fieldOptions['name']])) {
            $outputArray[$fieldOptions['name']] = PrepareAPITypeForDatabase($fieldOptions, $formInput[$fieldOptions['name']], $mysqliConnection);
        }
        else {
            if (isset($fieldOptions['defaultValue'])) {
                $outputArray[$fieldOptions['name']] = PrepareAPITypeForDatabase($fieldOptions, $fieldOptions['defaultValue'], $mysqliConnection);
            }
        }
    }
    return $outputArray;
}

?>
