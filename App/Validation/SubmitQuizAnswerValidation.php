<?php

namespace App\Validation;

/**
 * Class SubmitQuizAnswerValidation
 * @package App\Validation
 */
class SubmitQuizAnswerValidation extends InputValidation
{
    protected $inputData;
    protected $sessionData;

    /**
     * StartQuizValidation constructor.
     * @param array $request Input data, usually straight from $_REQUEST
     * @param array $session Session data, usually straight from $_SESSION
     * @return $this
     */
    public function __construct($request = [], $session = [])
    {
        $this->inputData = $request;
        $this->sessionData = $session;

        return $this;
    }

    /**
     * @return bool
     */
    public function validate()
    {
        if (!ctype_digit($this->inputData['selected_option'])) {
            return $this->reject('Invalid data');
        }

        return $this->pass();
    }

    /**
     * @return bool
     */
    protected function pass()
    {
        return true;
    }

    /**
     * @param $reason
     * @return bool
     */
    protected function reject($reason)
    {
        return false;
    }
}
