<?php

declare(strict_types=1);

/**
 * Entidad comentario. Representa el comentario de un usuario
 * respecto al producto con el que está relacionado
 */
class Comment{

    /**
     * Identidad del comentario
     * @var int
     */
    public int $id = 0;

    /**
     * Referencia al producto asociado
     * @var int
     */
    public $Product=0;

    /**
     * Título del comentario
     * @var string
     */
    public $Title="";

    /**
     * Texto del comentario
     * @var string
     */
    public $Text="";

    /**
     * Autor / Usuario que realiza el comentario
     * @var string
     */
    public $Author="";

    /**
     * Fecha en la que se realiza el comentario
     * @var string
     */
    public $Date=null;

    /**
     * Cantidad de votos positivos
     * @var int
     */
    public $Likes=0;

    /**
     * Cantidad de votos negativos
     * @var int
     */
    public $Unlikes=0;

    /**
     * Estado del comentario
     * @var boolean
     */
    public $State=1;

    /**
     * Constructor
     */
    public function __construct() {
        $date = new DateTime();
        $this->Date = $date->format( 'Y-m-d H:i:s' );
    }
}
