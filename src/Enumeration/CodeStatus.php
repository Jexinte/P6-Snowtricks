<?php

namespace App\Enumeration;

enum CodeStatus: int
{
    const REDIRECT = 302;
    const CLIENT = 400;
    const SERVER = 500;
    const RESSOURCE_NOT_FOUND = 404;
}
