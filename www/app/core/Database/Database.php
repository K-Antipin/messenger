<?php
namespace App\core\Database;

use RedBeanPHP\R;
use RedBeanPHP\RedException;

class Database extends R implements DatabaseInterface
{
    public function __construct()
    {
        try {
            Database::setup(
                'mysql:host=db;dbname=messenger',
                'root',
                'root'
            );
            if (!R::testConnection()) {
                throw new RedException('No connection');
            }
        } catch (RedException $e) {
            exit(var_dump($e));
        }
    }

    /**
     * Summary of create
     * @param object $entity
     * @param string $table
     * @return int|string
     */
    public function create(object $entity, string $table): int|string
    {
        $bean = Database::dispense($table);
        foreach ($entity as $k => $v) {
            $bean->$k = $v;
        }
        try {
            $id = Database::store($bean);
        } catch (RedException $e) {
            R::rollback();
            die($e);
        }

        return $id;
    }

    /**
     * Summary of update
     * @param object $obj
     * @param string $table
     * @return int|string
     */
    public function update(object|array $obj, string $table): int|string
    {
        $bean = Database::load($table, is_object($obj) ? $obj->id : $obj['id']);
        foreach ($obj as $k => $v) {
            if ($k != 'id') {
                $bean->$k = $v;
            }
        }
        
        $id = Database::store($bean);

        return $id;
    }

    public function delete(string $table, array|int|string $ids): int|bool
    {
        if (is_array($ids)) {
            Database::trashBatch($table, $ids);
            return true;
        } else {
            return Database::hunt($table, 'id = ?', [$ids]);
        }
    }

    

}