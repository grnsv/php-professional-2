<?php

namespace App\Traits;

use App\Entities\User\User;

trait Author
{
    private User $author;

    public function getAuthor(): User
    {
        return $this->author;
    }

    public function setAuthor(User $user): self
    {
        $this->author = $user;

        return $this;
    }
}
