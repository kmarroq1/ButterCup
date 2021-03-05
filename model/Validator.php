<?php

require_once 'Fields.php';

class Validator {

    private $fields;

    public function __construct() {
        $this->fields = new Fields();
    }

    public function getFields() {
        return $this->fields;
    }

    public function foundErrors() {
        return $this->fields->hasErrors();
    }

    public function addField($name, $message = '') {
        return $this->fields->addField($name, $message);
    }

    // Validate a generic text field
    public function checkText($name, $value,
            $required = true, $min = 1, $max = 255) {

        // Get Field object
        $field = $this->fields->getField($name);

        // If field is not required and empty, remove error and exit
        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        // Check field and set or clear error message
        if ($required && empty($value)) {
            $field->setErrorMessage('Required');
        } else if (strlen($value) < $min) {
            $field->setErrorMessage('Too short');
        } else if (strlen($value) > $max) {
            $field->setErrorMessage('Too long');
        } else {
            $field->clearErrorMessage();
        }
    }

    // Validate a code field
    public function checkCode($name, $value, $required = true) {
        $field = $this->fields->getField($name);

        // If field is not required and empty, remove errors and exit
        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        // Call the text method and exit if it yields an error
        $this->checkText($name, $value, $required, $min = 6, $max = 10);
        if ($field->hasError()) {
            return;
        }

        // Split the code at hyphen and check parts
        $parts = explode('-', $value);
        if (count($parts) < 2) {
            $field->setErrorMessage('Hyphen required.');
            return;
        }
        if (count($parts) > 2) {
            $field->setErrorMessage('Only one hyphen allowed.');
            return;
        }

        $letters_part = $parts[0];
        $numerical_part = $parts[1];
        // Check lengths of letters and numerical parts
        if (strlen($letters_part) > 6 || strlen($numerical_part) > 3) {
            $field->setErrorMessage('Must be 3-6 letters and 2-3 digits.');
            return;
        }

        // Pattern for letters part
       $pattern = '/^[[:upper:]]{3,6}-[[:digit:]]{2,3}$/';

        // Call the pattern method and exit if it yields an error
        $this->checkPattern($name, $value, $pattern,
                'Invalid product code.');
        if ($field->hasError()) {
            return;
        }
    }

    // Validate a price field
    public function checkPrice($name, $value,
            $required = true) {

        // Get Field object
        $field = $this->fields->getField($name);

        // Call the text method and exit if it yields an error
        $this->checkText($name, $value, $required);
        if ($field->hasError()) {
            return;
        }

        // Check field and set or clear error message
        if (!is_numeric($value)) {
            $field->setErrorMessage('Must be a valid number.');
        } else {
            $field->clearErrorMessage();
        }

        // Pattern for price
        $price_pattern = '/^\$?[0-9]+(\.[0-9]{1,2})?$/';

        // Call the pattern method and exit if it yields an error
        $this->checkPattern($name, $value, $price_pattern,
                'Invalid price.');
        if ($field->hasError()) {
            return;
        }
    }

    // Validate a field with a generic pattern
    public function checkPattern($name, $value, $pattern, $message,
            $required = true) {

        // Get Field object
        $field = $this->fields->getField($name);

        // If field is not required and empty, remove errors and exit
        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        // Check field and set or clear error message
        $match = preg_match($pattern, $value);
        if ($match === false) {
            $field->setErrorMessage('Error testing field.');
        } else if ($match != 1) {
            $field->setErrorMessage($message);
        } else {
            $field->clearErrorMessage();
        }
    }

    public function checkPhone($name, $value, $required = false) {
        $field = $this->fields->getField($name);

        // Call the text method and exit if it yields an error
        $this->checkText($name, $value, $required);
        if ($field->hasError()) {
            return;
        }

        // Call the pattern method to validate a phone number
        $pattern = '/^[[:digit:]]{3}-[[:digit:]]{3}-[[:digit:]]{4}$/';
        $message = 'Invalid phone number.';
        $this->checkPattern($name, $value, $pattern, $message, $required);
    }

    public function checkEmail($name, $value, $required = true) {
        $field = $this->fields->getField($name);

        // If field is not required and empty, remove errors and exit
        if (!$required && empty($value)) {
            $field->clearErrorMessage();
            return;
        }

        // Call the text method and exit if it yields an error
        $this->checkText($name, $value, $required);
        if ($field->hasError()) {
            return;
        }

        // Split email address on @ sign and check parts
        $parts = explode('@', $value);
        if (count($parts) < 2) {
            $field->setErrorMessage('At sign required.');
            return;
        }
        if (count($parts) > 2) {
            $field->setErrorMessage('Only one at sign allowed.');
            return;
        }
        $local = $parts[0];
        $domain = $parts[1];

        // Check lengths of local and domain parts
        if (strlen($local) > 64) {
            $field->setErrorMessage('Username part too long.');
            return;
        }
        if (strlen($domain) > 255) {
            $field->setErrorMessage('Domain name part too long.');
            return;
        }

        // Patterns for address formatted local part
        $atom = '[[:alnum:]_!#$%&\'*+\/=?^`{|}~-]+';
        $dotatom = '(\.' . $atom . ')*';
        $address = '(^' . $atom . $dotatom . '$)';

        // Patterns for quoted text formatted local part
        $char = '([^\\\\"])';
        $esc = '(\\\\[\\\\"])';
        $text = '(' . $char . '|' . $esc . ')+';
        $quoted = '(^"' . $text . '"$)';

        // Combined pattern for testing local part
        $localPattern = '/' . $address . '|' . $quoted . '/';

        // Call the pattern method and exit if it yields an error
        $this->checkPattern($name, $local, $localPattern,
                'Invalid username part.');
        if ($field->hasError()) {
            return;
        }

        // Patterns for domain part
        $hostname = '([[:alnum:]]([-[:alnum:]]{0,62}[[:alnum:]])?)';
        $hostnames = '(' . $hostname . '(\.' . $hostname . ')*)';
        $top = '\.[[:alnum:]]{2,6}';
        $domainPattern = '/^' . $hostnames . $top . '$/';

        // Call the pattern method
        $this->checkPattern($name, $domain, $domainPattern,
                'Invalid domain name part.');
    }

}

?>