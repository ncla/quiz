<?php

namespace App\Validation;

/**
 * Class StartQuizValidation
 * @package App\Validation
 */
class StartQuizValidation extends InputValidation
{

    protected $inputData;
    protected $sessionData;

    protected $rejectReason;

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
        if (!isset($this->inputData['quiz_id'])) {
            return $this->reject('Please select a quiz');
        }

        if (!isset($this->inputData['name']) || strlen(trim($this->inputData['name'])) === 0) {
            return $this->reject('Please enter your name');
        }

        if (!ctype_digit($this->inputData['quiz_id']) || !ctype_alpha($this->inputData['name'])) {
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
        $this->rejectReason = $reason;

        return false;
    }

    /**
     * @return string Reject reason text
     */
    public function getRejectReason()
    {
        return $this->rejectReason;
    }
}
