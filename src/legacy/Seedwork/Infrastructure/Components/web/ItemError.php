<?php

declare(strict_types=1);

/**
 * DTO para visualizar errores
 */
class ItemError{

    /**
     * Texto a visualizar
     * @var string
     */
    public $Text = "";

    /**
     * Constructor
     * @param string $text texto a visualizar
     */
    public function __construct($text = "") {
        $this->Text = $text;
    }
}
