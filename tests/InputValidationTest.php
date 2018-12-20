<?php

namespace tests;

use PHPUnit\Framework\TestCase;
use App\Validation\StartQuizValidation;

class StartQuizInputValidationTest extends TestCase
{
    /**
     * @dataProvider invalidInputDataProvider
     */
    public function testFailureFromInvalidInputData($inputData)
    {
        $validation = new StartQuizValidation($inputData);
        $isValid = $validation->validate();
        $this->assertFalse($isValid);
    }

    public function testEmptyInputData()
    {
        $validation = new StartQuizValidation([]);
        $isValid = $validation->validate();
        $this->assertFalse($isValid);
    }

    /**
     * @dataProvider validInputDataProvider
     */
    public function testSuccessFromValidInputData($inputData)
    {
        $validation = new StartQuizValidation($inputData);
        $isValid = $validation->validate();
        $this->assertFalse($isValid);
    }

    public function validInputDataProvider()
    {
        return [
            'number quiz id, string name' => ['name' => 'Bob', 'quiz_id' => '1'],
            'number quiz id, utf-8 name' => ['name' => 'Bōb', 'quiz_id' => '1'],
            'integer number quiz id, another utf-8 name' => ['name' => 'トリプテクタ', 'quiz_id' => 4]
        ];
    }

    public function invalidInputDataProvider()
    {
        return [
            'only name' => ['name' => 'Bob'],
            'only quiz id' => ['quiz_id' => '1'],
            'non-number for quiz_id' => ['name' => 'Bob', 'quiz_id' => 'text'],
            'empty string for quiz id' => ['name' => 'Bob', 'quiz_id' => '']
        ];
    }
}