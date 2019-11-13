<?php
/**
 * Created by PhpStorm.
 * User: sylvain
 * Date: 07/03/18
 * Time: 18:20
 * PHP version 7
 */

namespace App\Model;

/**
 *
 */
class StudentManager extends AbstractManager
{
    /**
     *
     */
    const TABLE = 'student';

    /**
     *  Initializes this class.
     */
    public function __construct()
    {
        parent::__construct(self::TABLE);
    }


    /**
     * @param array $student
     * @return int
     */
    public function insert(array $student): int
    {
        // prepared request
        $statement = $this->pdo->prepare(
            "INSERT INTO $this->table (`firstname`, lastname, path) VALUES (:firstname, :lastname, :path)"
        );
        $statement->bindValue('firstname', $student['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $student['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('path', $student['path'], \PDO::PARAM_STR);

        if ($statement->execute()) {
            return (int)$this->pdo->lastInsertId();
        }
    }


    /**
     * @param int $id
     */
    public function delete(int $id): void
    {
        // prepared request
        $statement = $this->pdo->prepare("DELETE FROM $this->table WHERE id=:id");
        $statement->bindValue('id', $id, \PDO::PARAM_INT);
        $statement->execute();
    }


    /**
     * @param array $student
     * @return bool
     */
    public function update(array $student):bool
    {

        // prepared request
        $statement = $this->pdo->prepare(
            "UPDATE $this->table SET firstname=:firstname, lastname=:lastname, path=:path WHERE id=:id"
        );
        $statement->bindValue('id', $student['id'], \PDO::PARAM_INT);
        $statement->bindValue('firstname', $student['firstname'], \PDO::PARAM_STR);
        $statement->bindValue('lastname', $student['lastname'], \PDO::PARAM_STR);
        $statement->bindValue('path', $student['path'], \PDO::PARAM_STR);

        return $statement->execute();
    }
}
