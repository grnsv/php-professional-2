<?php

namespace App\Exceptions;

class UserEmailExistsException extends \Exception
{
    protected $message = 'Пользователь с таким email уже существует в системе';
}
