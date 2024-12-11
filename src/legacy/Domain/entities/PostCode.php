<?php

declare(strict_types=1);

/**
 * Entidad para el registro de códigos postales en la tabla maestra
 *
 * @author alfonso
 */
class PostCode {

    /**
     * Identidad del registro
     * @var int
     */
    public $Id = 0;

    /**
     * Código postal
     * @var string
     */
    public $Code = "";

    /**
     * Descripción y/o Notas
     * @var string
     */
    public $Description = "";

}
