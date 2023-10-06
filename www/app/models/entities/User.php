<?php

namespace App\models\entities;

use DateTime;

class User
{
    // public int|string $id;
    // public string $name;
    // public string $nickname;
    // public bool $display;
    // public bool $notification;
    // public string $email;
    // public string $password;
    // public string $user_hash;
    // public string $user_ip;
    // public string $role;
    // public string $avatar;
    // public DateTime $created;
    // public DateTime $updated;

    public function __construct(
        public int|string $id,
        public string $name,
        public string $nickname,
        public bool $display,
        public bool $notification,
        public string $email,
        public string $password,
        public string $user_hash,
        public string $user_ip,
        public string $role,
        public string $avatar,
        public DateTime $created,
        public DateTime $updated,
    ) {
    }
    public function user($data)
    {
        return $this;
    }

    public function role(): string
    {
        return $this->role;
    }

    public function avatar(): string
    {
        return $this->avatar;
    }

    public function id(): int
    {
        return (int) $this->id;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function nickname(): string
    {
        return $this->nickname;
    }

    public function display(): bool
    {
        return $this->display;
    }

    public function notification(): bool
    {
        return $this->notification;
    }

    public function email(): string
    {
        return $this->email;
    }

    public function password(): string
    {
        return $this->password;
    }

    public function user_hash(): string
    {
        return $this->user_hash;
    }

    public function user_ip(): string
    {
        return $this->user_ip;
    }

    public function created(): DateTime
    {
        return new DateTime($this->created);
    }

    public function updated(): DateTime
    {
        return new DateTime($this->updated);
    }
}