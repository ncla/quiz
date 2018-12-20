<?php

namespace App\Services;

/**
 * Class UserService
 * @package App\Services
 */
class UserService
{
    /**
     * UserService constructor.
     *
     * TODO: Add interface type hinting so you can pass any database thing
     * @param $database
     * @return self
     */
    public function __construct($database)
    {
        $this->database = $database;

        return $this;
    }

    /**
     * @param $name
     * @return mixed|integer User ID in database
     */
    public function createUser($name)
    {
        $this->database->query('INSERT INTO users(name) VALUES(:name)', ['name' => trim($name)]);

        return $this->database->lastInsertId();
    }

    /**
     * @param $id User ID
     * @return mixed
     */
    public function getUser($id)
    {
        return $this->database->query('SELECT id, name FROM users WHERE id = :id', ['id' => $id])->fetch();
    }
}
