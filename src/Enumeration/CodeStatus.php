<?php

namespace App\Enumeration;

enum CodeStatus: int
{
    const CLIENT = 400;
    const REQUEST_SUCCEED = 200;
    const SERVER = 500;
    const RESSOURCE_NOT_FOUND = 404;
    const FORBIDDEN = 403;
}
