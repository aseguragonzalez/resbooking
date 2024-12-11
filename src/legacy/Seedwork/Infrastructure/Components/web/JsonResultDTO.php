<?php

declare(strict_types=1);

/**
 * DTO para retornar resultado de operaciones en JSON
 *
 * @author alfonso
 */
class JsonResultDTO {
    public $Result = FALSE;
    public $Error = "";
    public $Code = 200;
    public $Exception = NULL;
}
