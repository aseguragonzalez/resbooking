<?php

/**
 * DTO para la transferencia de estado
 *
 * @author manager
 */
class StateDTO {

    /**
     * Identidad de la entidad
     * @var int
     */
    public int $id = 0;

    /**
     * Nuevo estado a asignar a la entidad
     * @var int
     */
    public bool $state = false;

}
