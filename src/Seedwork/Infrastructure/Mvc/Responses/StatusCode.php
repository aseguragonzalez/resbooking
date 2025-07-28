<?php

declare(strict_types=1);

namespace Seedwork\Infrastructure\Mvc\Responses;

enum StatusCode: int
{
    case Ok = 200;
    case Created = 201;
    case Accepted = 202;
    case NoContent = 204;
    case MovedPermanently = 301;
    case Found = 302;
    case BadRequest = 400;
    case Unauthorized = 401;
    case Forbidden = 403;
    case NotFound = 404;
    case MethodNotAllowed = 405;
    case Conflict = 409;
    case PreconditionFailed = 412;
    case UnprocessableEntity = 422;
    case InternalServerError = 500;
    case NotImplemented = 501;
    case BadGateway = 502;
    case ServiceUnavailable = 503;
    case GatewayTimeout = 504;
}
