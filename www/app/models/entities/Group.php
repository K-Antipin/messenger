<?php

namespace App\models\entities;

use DateTime;

class Group
{
    private int $group_id;
    private int $user_id;

    public function __construct(object $entity)
    {
        $this->group_id = $entity->group_id;
        $this->user_id = $entity->user_id;
    }
}