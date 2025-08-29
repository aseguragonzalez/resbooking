<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Requests;

enum RequestContextKeys: string
{
    case LANGUAGE = 'language';
}
