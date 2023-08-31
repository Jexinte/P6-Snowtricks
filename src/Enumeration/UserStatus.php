<?php

namespace App\Enumeration;

enum UserStatus: int
{
    const CONNECTED = true;
    const ACCOUNT_NOT_ACTIVATE = false;
    const ACCOUNT_ACTIVATE = true;

    const ASK_RESET_PASSWORD = true;
}
