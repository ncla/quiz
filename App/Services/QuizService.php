<?php

namespace App\Services;

use App\DB\Database;

/**
 * Class QuizService
 * @package App\Services
 */
class QuizService
{
    /**
     * QuizService constructor.
     * @param $database Database
     * @return self
     */
    public function __construct($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @return mixed
     * @throws \App\DB\DatabaseException
     */
    public function getAllQuizzes()
    {
        return $this->database->query('SELECT id, name FROM quizzes')->fetchAll();
    }

    /**
     * @param $userId
     * @param $quizId
     * @return mixed
     * @throws \App\DB\DatabaseException
     */
    public function getUserProgress($userId, $quizId)
    {
        // Casting `questions_answered` to unsigned because PHP represents DECIMAL as a string (output from SUM())
        $result = $this->database->query('
            SELECT CAST(IFNULL(SUM(quiz_answers.option_id IS NOT NULL), 0) AS unsigned) AS questions_answered, 
                   COUNT(*) as questions_total
            FROM quiz_questions AS questions
            LEFT JOIN quiz_answers 
              ON (questions.id = quiz_answers.question_id AND quiz_answers.user_id = :user_id)
            WHERE questions.quiz_id = :quiz_id
            -- AND quiz_answers.id IS NULL
            ORDER BY questions.order_nr ASC
            -- LIMIT 1
            ', ['quiz_id' => $quizId, 'user_id' => $userId])->fetch();

        // Division by zero precautions
        if ($result['questions_answered'] !== 0) {
            $result['percentage_answered'] = round($result['questions_answered'] / $result['questions_total'] * 100, 2);
        } else {
            $result['percentage_answered'] = 0;
        }

        return $result;
    }

    /**
     * @param $userId
     * @param $quizId
     * @return array|bool
     * @throws \App\DB\DatabaseException
     */
    public function getNextQuestion($userId, $quizId)
    {
        $questionQuery = $this->database->query('
            SELECT questions.id, questions.question FROM quiz_questions AS questions
            LEFT JOIN quiz_answers 
              ON (questions.id = quiz_answers.question_id AND quiz_answers.user_id = :user_id)
            WHERE questions.quiz_id = :quiz_id
            AND quiz_answers.id IS NULL
            ORDER BY questions.order_nr ASC
            LIMIT 1
            ', ['quiz_id' => $quizId, 'user_id' => $userId]);

        if (!$questionQuery->rowCount() === 1) {
            return true;
        }

        $question = $questionQuery->fetch();

        // This assumes that appropriate amount of options are available (minimum of 2).
        // TODO: Should there be OptionsService too? QuestionService?
        $options = $this->database->query('
            SELECT `id`, `option` FROM quiz_options
            WHERE question_id = :question_id
            ORDER BY order_nr ASC
        ', ['question_id' => $question['id']])->fetchAll();

        $questionData = [
            'question' => $question,
            'options' => $options
        ];

        return $questionData;
    }

    /**
     * @param $userId
     * @param $quizId
     * @return mixed
     * @throws \App\DB\DatabaseException
     */
    public function getQuizResultForUser($userId, $quizId)
    {
        // Casting `answered_correctly` to unsigned because PHP represents DECIMAL as a string (output from SUM())
        return $this->database->query('
            SELECT CAST(IFNULL(SUM(quiz_options.correct_option = 1), 0) AS unsigned) AS answered_correctly,
                   COUNT(*) as total_questions
            FROM quiz_answers
            INNER JOIN quiz_options ON quiz_options.id = quiz_answers.option_id  
            INNER JOIN quiz_questions ON quiz_questions.id = quiz_options.question_id
            WHERE quiz_answers.user_id = :userId
            AND quiz_questions.quiz_id = :quizId
        ', ['userId' => $userId, 'quizId' => $quizId])->fetch();
    }

    /**
     * @param $optionId
     * @param $questionId
     * @param $quizId
     * @return bool
     * @throws \App\DB\DatabaseException
     */
    public function isOptionBelongingToCorrectQuestionAndQuiz($optionId, $questionId, $quizId)
    {
        $query = $this->database->query('
            SELECT COUNT(*) as count FROM quiz_options
            INNER JOIN quiz_questions ON quiz_questions.id = quiz_options.question_id
            INNER JOIN quizzes ON quiz_questions.quiz_id = quizzes.id
            WHERE quiz_options.id = :optionId
            AND quiz_questions.id = :questionId
            AND quiz_questions.quiz_id = :quizId
        ', ['optionId' => $optionId, 'questionId' => $questionId, 'quizId' => $quizId]);

        return $query->rowCount() === 1;
    }

    /**
     * @param $quizId
     * @return bool
     * @throws \App\DB\DatabaseException
     */
    public function checkQuizExistenceById($quizId)
    {
        $query = $this->database->query('SELECT id, name FROM quizzes WHERE id = :id LIMIT 1', ['id' => $quizId]);

        return $query->rowCount() === 1;
    }

    /**
     * @param $userId
     * @param $questionId
     * @param $optionId
     * @return Object
     * @throws \App\DB\DatabaseException
     */
    public function insertAnswer($userId, $questionId, $optionId)
    {
        return $this->database->query('
            INSERT INTO quiz_answers(user_id, question_id, option_id) VALUES(:userId, :questionId, :optionId)
        ', ['userId' => $userId, 'questionId' => $questionId, 'optionId' => $optionId]);
    }
}
