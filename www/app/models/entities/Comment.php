<?php

namespace App\models\entities;

use DateTime;

class Comment
{
    public int $to_id;
    public int $from_id;
    public string $text;

    

    public function __construct($entity)
    {
        $this->to_id = $entity->to_id;
        $this->from_id = $entity->from_id;
        $this->text = $entity->text;
    }
}