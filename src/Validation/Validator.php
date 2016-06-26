<?php

namespace Anam\Aba\Validation;

use \Exception;

class Validator
{
    protected static $transactionCodes = [
        'externally_initiated_debit'                => '13',
        'externally_initiated_credit'               => '50',
        'australian_government_security_interest '  => '51',
        'family_allowance'                          => '52',
        'pay'                                       => '53',
        'pension'                                   => '54',
        'allotment'                                 => '55',
        'dividend'                                  => '56',
        'debenture'                                 => '57',
        'note_interest'                             => '57'
    ];

    protected static $rules = [
        'bsb'               => '/^[\d]{3}-[\d]{3}$/',
        'account_number'    => '/^[\d]{0,9}$/',
        'bank_name'         => '/^[A-Z]{3}$/',
        // Your organisation name
        'user_name'         => '/^[A-Za-z\s+]{0,26}$/',
        // Title of account to be credited/debited
        'account_name'      => '/^[A-Za-z\s+]{0,32}$/',
        // User Identification Number which is allocated by APCA
        'user_number'       => '/^[\d]{0,6}$/',
        'description'       => '/^[A-Za-z\s]{0,12}$/',
        'indicator'         => '/^N|T|W|X|Y| /',
        'reference'         => '/^[A-Za-z0-9\s+]{0,18}$/',
        'remitter'          => '/^[A-Za-z\s+]{0,16}$/',
    ];

    protected static $messages = [ 
        'bsb'               => 'BSB format is incorrect. The valid format is XXX-XXX',
        'account_number'    => 'Account number must be up to 9 digits',
        'bank_name'         => 'Bank name must 3 characters long and Capitalised',
        'user_name'         => 'User or Preferred name must be letters only and up to 26 characters long',
        'account_name'      => 'Account name must be letters only and up to 32 characters long',
        'user_number'       => 'User number which is allocated by APCA must be up to 6 digits long. The Commonwealth bank default is 301500',
        'description'       => 'Description must be up to 12 characters long and letters only',
        'indicator'         => 'The Indicator is invalid. Must be one of N, W, X, Y or otherwise blank filled.',
        'reference'         => 'The reference must be up to 18 characters long. For example: Payroll number',
        'remitter'          => 'The remitter must be letters only and up to 16 characters long.',
    ];

    public static function validate(array $record, array $matchRules, $recordType = 'Detail')
    {
        $this->verifyRecord($record, $matchRules, $recordType);

        foreach ($matchRules as $rule) {
            if (! preg_match(self::$rules[$rule], $record[$rule])) {
                throw new Exception($recordType . ': ' . self::$messages[$rule]);
            }
        }
    }

    public function verifyRecord(array $record, array $matchRules, $recordType = 'Detail') 
    {
        $missingFields = array_diff($matchRules, array_keys($record));

        if ($missingFields) {
            throw new Exception('Some required {$recordType} fields missing: '. implode(",", $missingFields));
        }
    }

    public static function validateTransactionCode($code)
    {
        if (! in_array($code, $transactionCodes)) {
            throw new Exception("Transaction code is invalid.");
        }
    }

    public static function validateProcessDate($date)
    {
        if (! is_string($date) && ! is_numeric($date)) {
            throw new Exception("Process date is invalid. Process date must be in 'DDMMYY' format");
        }

        $parsed = date_parse_from_format('dmy', $date);
        
        if (! ($parsed['error_count'] === 0 && $parsed['warning_count'] === 0)) {
            throw new Exception("Process date is invalid. Process date must be in 'DDMMYY' format");
        }
    }

    public static function validateNumeric($value) {
        if (! is_numeric($value)) {
            throw new Exception("Amount or Withholding tax amount must be a numeric number");
        }
    }
}