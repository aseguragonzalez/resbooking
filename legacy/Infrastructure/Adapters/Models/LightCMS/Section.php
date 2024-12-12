<?php

declare(strict_types=1);

namespace App\Legacy\Infrastructure2\Adapters\LightCMS;


class Section
{
    public int $id = 0;
    public int $projectId = 0;
    public $Root = null;
    public string $name = "";
    public $Link = "";
    public $Author = "";
    public $Keywords = "";
    public string $description = "";
    public $Tooltip = "";
    public $Template = "";
    public $Position = 0;
    public $Draft = true;
    public $State = true;
}
