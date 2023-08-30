<?php

namespace App\Enumeration;

enum UserStatus: int
{
    const CONNECTED = 1;
    const ACCOUNT_NOT_ACTIVATE = 0;
    const ACCOUNT_ACTIVATE = 1;
    const LOGOUT = 1;
}
