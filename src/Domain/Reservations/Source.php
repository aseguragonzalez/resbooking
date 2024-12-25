<?php

declare(strict_types=1);

namespace App\Domain\Shared;

enum Source: int
{
    case facebook = 1;
    case instagram = 2;
    case twitter = 3;
    case whatsapp = 4;
    case telegram = 5;
    case email = 6;
    case phone = 7;
    case website = 8;
    case other = 9;
}
