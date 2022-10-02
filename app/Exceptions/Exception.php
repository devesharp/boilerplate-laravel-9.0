<?php

namespace App\Exceptions;

class Exception extends \Devesharp\Exceptions\Exception
{
    const RECOVERY_PASSWORD_TOKEN_INVALID = 1000;

    const USER_BLOCKED = 1001;

    const RECOVERY_PASSWORD_LOGIN_INVALID = 1002;

    const RECOVERY_PASSWORD_TOKEN_EXPIRED = 1003;
}
