<?php

declare(strict_types=1);

/**
 * Description of ResbookingModel
 *
 * @author alfonso
 */
class ResbookingModel extends \SaasModel{

    public $Random = "";

    public function __construct() {
        parent::__construct();
        $date = new \DateTime("NOW");
        $this->Random = $date->format("YmdHis");
    }

}
