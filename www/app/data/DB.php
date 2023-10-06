<?php

namespace App\data;

use RedBeanPHP\R;
use RedBeanPHP\RedException;

// try {
//     R::setup('sqlite:' . DATA . 'gallary.sqlite');
//     if (!R::testConnection()) {
//         throw new RedException('No connection');
//     }
// } catch (RedException $e) {
//     exit(var_dump($e));
// }

try {
    R::setup(
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

class DB
{
    public static function exec(string $sql, array $binding = null)
    {
        return R::exec($sql, $binding);
    }

    public static function loadAll(string $table, array $id)
    {
        return R::loadAll($table, $id);
    }

    public static function find(string $table, string $sql, array $id)
    {
        return R::find($table, $sql, $id);
    }

    public static function findAll(string $table, string $sql = null)
    {
        $table = self::testInput($table);
        return isset($sql) ? R::findAll($table, $sql) : R::findAll($table);
    }

    public static function getAll(string $sql, array $id = \null)
    {
        return isset($id) ? R::getAll($sql, $id) : R::getAll($sql);
    }

    public static function findLike($table, $search, $sql = '')
    {
        // var_dump($id);
        return R::findLike($table, $search, $sql); //$table, ['user_id' => $id], 'ORDER BY id ASC LIMIT 10'
    }

    public static function findOne(string $table, array $id, string $sql = 'id = ?')
    {
        $table = self::testInput($table);
        return R::findOne($table, $sql, $id);
    }
    public static function create(object $entity, string $table)
    {
        $bean = R::dispense($table);
        foreach ($entity as $k => $v) {
            // var_dump($k, $v);
            $bean->$k = $v;
        }
        
        try {
            $id = R::store($bean);
        } catch (RedException $e) {
            R::rollback();
            die($e);
        }

        return $id;
    }

    public static function update(object $obj, string $table)
    {
        $bean = R::load($table, $obj->id);
        foreach ($obj as $k => $v) {
            if ($k != 'id') {
                $bean->$k = $v;
            }
        }
        // $bean->user_hash = $obj->user_hash;
        // $bean->user_ip = $obj->user_ip;
        // $bean->updated = $obj->updated;

        $id = R::store($bean);

        return $id;
    }

    public static function delete(string $table, $id, $sqlSnippet = 'id = ?'): string|int
    {

        // \var_dump($id);
        $id = R::hunt($table, $sqlSnippet, [$id]);
        return $id;
    }

    // public static function dropTable(string $table)
    // {
    //     // TODO
    // }

    private static function testInput($data): string
    {
        $data = trim($data);
        $data = stripslashes($data);
        return htmlspecialchars($data);
    }
}