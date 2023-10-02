<?php

namespace App\Enumeration;

enum CodeStatus: int
{
    public const CLIENT = 400;
    public const UNAUTHORIZED = 401;
    public const FORBIDDEN = 403;
    public const REQUEST_SUCCEED = 200;
    public const SERVER = 500;
    public const RESSOURCE_NOT_FOUND = 404;
}
