<?php

namespace App\Validators;

use Illuminate\Validation\Validator;

class Ssn extends Validator
{

    const NO_MATCH = 'ssnNoMatch';

    const INCORRECT_LENGTH = 'ssnIncorrectLength';

    const INVALID = 'ssnInvalid';

    const LENGTH = 11;

    /**
     * @param $rule
     * @param $value
     * @return mixed
     */
    public function validateSsn($rule, $value)
    {

        return $this->isValid($value);
    }

    /**
     * @var array
     */
    protected $messageTemplates = [

        self::NO_MATCH         => "The input does not match SSN format.",
        self::INCORRECT_LENGTH => "The SSN must be " . self::LENGTH . " characters",
        self::INVALID          => "Invalid SSN type given. Numeric string expected",
    ];

    /**
     * @var string
     */
    protected $pattern = '/^(\d{6})\-(\d{4})$/';

    /**
     * @param $value
     */
    public function isValid($value)
    {

        $length = strlen($value);

        if ($length !== self::LENGTH) {

            $this->errors()->add(self::LENGTH, self::messageTemplates[self::LENGTH]);
        }

        if (!preg_match($this->pattern, $value, $matches)) {

            $this->errors()->add(self::NO_MATCH, self::messageTemplates[self::NO_MATCH]);

            return false;
        }

        foreach ($matches as $match) {

            if (0 === (int) $match) {

                $this->errors()->add(self::NO_MATCH, $this->messageTemplates[self::NO_MATCH]);

                return false;
            }
        }

        return true;
    }
}
