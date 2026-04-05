<?php

declare(strict_types=1);

namespace Framework\Web\Routes;

final class InvalidAction extends \Exception
{
    public function __construct(string $controller, string $action, ?string $detail = null)
    {
        $message = "Action '{$action}' is not a valid action for controller {$controller}";
        if ($detail !== null && $detail !== '') {
            $message .= ": {$detail}";
        }
        parent::__construct($message);
    }
}
