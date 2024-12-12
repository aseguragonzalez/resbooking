<?php

declare(strict_types=1);

/**
 * DTO para agregar Likes y UnLikes de un comentario
 */
class CommentLikeDTO{

    /**
     * Identidad del comentario asociado
     * @var integer
     */
    public $Comment = 0;

    /**
     * Cantiddad de Likes que tiene
     * @var int
     */
    public $Likes = 0;

    /**
     * Cantidad de unlikes que tiene el comentario
     * @var int
     */
    public $Unlikes = 0;

}
