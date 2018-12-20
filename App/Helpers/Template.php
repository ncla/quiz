<?php

namespace App\Helpers;

use Exception;

/**
 * Class Template
 * Taken from http://chadminick.com/articles/simple-php-template-engine.html
 * @package App\Helpers
 */
class Template
{
    private $vars = array();

    /**
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->vars[$name];
    }

    /**
     * @param $name
     * @param $value
     * @throws Exception
     */
    public function __set($name, $value)
    {
        if ($name == 'view_template_file') {
            throw new Exception("Cannot bind variable named 'view_template_file'");
        }
        $this->vars[$name] = $value;
    }

    /**
     * @param $view_template_file Template file to include
     * @return false|string
     * @throws Exception
     */
    public function render($view_template_file)
    {
        if (array_key_exists('view_template_file', $this->vars)) {
            throw new Exception("Cannot bind variable called 'view_template_file'");
        }
        extract($this->vars);
        ob_start();
        include($view_template_file);
        return ob_get_clean();
    }
}
