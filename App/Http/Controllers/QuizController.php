<?php

namespace App\Http\Controllers;

use App\DB\DatabaseException;
use App\Helpers\Template;
use App\Services\QuizService;
use App\Services\UserService;
use App\Validation\StartQuizValidation;
use App\Validation\SubmitQuizAnswerValidation;

/**
 * Class QuizController
 * @package App\Http\Controllers
 */
class QuizController
{
    protected $quizService;
    protected $userService;

    public function __construct(QuizService $quizService, UserService $userService)
    {
        $this->quizService = $quizService;
        $this->userService = $userService;

        return $this;
    }

    /**
     * Index page with a that lists all quizzes
     * @throws DatabaseException
     * @return $this
     */
    public function index()
    {
        session_start();

        $view = new Template();

        if (isset($_SESSION['flash_message'])) {
            $view->flashMessage = $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
        }

        // Because we do not have a proper user authentication, each test is taken with unique user, and when
        // you visit the index page, you start over therefor have no user ID attached to session
        session_unset();

        $view->quizzesSelect = $this->quizService->getAllQuizzes();

        $view->content = $view->render(ROOT . 'resources/views/quiz_index.phtml');
        echo $view->render(ROOT . 'resources/views/main.phtml');

        return $this;
    }

    /**
     * Start quiz request handling
     * @throws DatabaseException
     * @return $this
     */
    public function startQuiz()
    {
        session_start();

        // TODO: We are hard-coding validation here... ? Controllers are not suppose to have validation?
        $validation = new StartQuizValidation($_REQUEST, $_SESSION);

        if (!$validation->validate()) {
            $_SESSION['flash_message'] = $validation->getRejectReason();
            header('Location: /');
            return $this;
        }

        // Check if quiz exists in DB.
        if (!$this->quizService->checkQuizExistenceById($_REQUEST['quiz_id'])) {
            header('Location: /');
            return $this;
        }

        $userId = $this->userService->createUser($_REQUEST['name']);

        $_SESSION['user_id'] = $userId;
        $_SESSION['quiz_id'] = $_REQUEST['quiz_id'];

        header('Location: /quiz/', true);
        return $this;
    }

    /**
     * Quiz in progress view
     * @return $this
     * @throws DatabaseException
     */
    public function showQuiz()
    {
        session_start();

        if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['user_id'])) {
            header('Location: /');
            return $this;
        }

        // Check if quiz exists
        if (!$this->quizService->checkQuizExistenceById($_SESSION['quiz_id'])) {
            header('Location: /');
            return $this;
        }

        // Get user progress
        $progress = $this->quizService->getUserProgress($_SESSION['user_id'], $_SESSION['quiz_id']);

        // Determine if we need to redirect to quiz complete page
        if ($progress['questions_answered'] === $progress['questions_total']) {
            header('Location: /quiz/done');
            return $this;
        }

        // Get next question (that has not been answered) and it's options
        $question = $this->quizService->getNextQuestion($_SESSION['user_id'], $_SESSION['quiz_id']);

        // Store the question_id in $_SESSION so we do not have to rely on user data input
        $_SESSION['question_id'] = $question['question']['id'];

        $view = new Template();

        $view->question = $question;
        $view->userProgress = $progress;

        $view->content = $view->render(ROOT . 'resources/views/quiz_show.phtml');

        echo $view->render(ROOT . 'resources/views/main.phtml');

        return $this;
    }

    /**
     * Submit quiz answer request handling
     * @return $this
     * @throws DatabaseException
     */
    public function submitAnswer()
    {
        session_start();

        $validation = new SubmitQuizAnswerValidation($_REQUEST, $_SESSION);

        if ($validation->validate() === false) {
            header('Location: /quiz/?validation');
            return $this;
        }

        // Validation: check $_SESSION, quiz,
        if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['user_id'])) {
            header('Location: /');
            return $this;
        }

        // Check if option_id exists and belongs to a question
        $answerBelongsCorrectly = $this->quizService->isOptionBelongingToCorrectQuestionAndQuiz(
            $_REQUEST['selected_option'],
            $_SESSION['question_id'],
            $_SESSION['quiz_id']
        );

        if (!$answerBelongsCorrectly) {
            header('Location: /');
            return $this;
        }

        $this->quizService->insertAnswer($_SESSION['user_id'], $_SESSION['question_id'], $_REQUEST['selected_option']);

        header('Location: /quiz/');
        return $this;
    }

    /**
     * Completed quiz view
     * @return $this
     * @throws DatabaseException
     */
    public function showCompletedQuiz()
    {
        session_start();

        if (!isset($_SESSION['quiz_id']) || !isset($_SESSION['user_id'])) {
            header('Location: /');
            return $this;
        }

        $view = new Template();

        $view->user = $this->userService->getUser($_SESSION['user_id']);
        $view->quizResult = $this->quizService->getQuizResultForUser($_SESSION['user_id'], $_SESSION['quiz_id']);

        $view->content = $view->render(ROOT . 'resources/views/quiz_complete.phtml');

        echo $view->render(ROOT . 'resources/views/main.phtml');

        return $this;
    }
}
