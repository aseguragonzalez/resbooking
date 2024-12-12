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
    public int $id = 0;

    /**
     * Código postal
     * @var string
     */
    public string $code = "";

    /**
     * Descripción y/o Notas
     * @var string
     */
    public string $description = "";

}
