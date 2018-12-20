<?php

namespace App\Validation;

/**
 * Class InputValidation
 * @package App\Validation
 */
abstract class InputValidation
{
    abstract public function __construct($request, $session);

    abstract public function validate();

    abstract protected function pass();

    abstract protected function reject($reason);
}
